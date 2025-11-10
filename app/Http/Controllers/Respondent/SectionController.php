<?php

namespace App\Http\Controllers\Respondent;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Services\Forms\FormAccessGuard;
use App\Services\Forms\DraftResponseService;
use App\Services\Forms\SectionNavigator;
use App\Services\Forms\AnswerSaver;
use App\Support\QuestionValidationRules;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function __construct(
        private FormAccessGuard $guard,
        private DraftResponseService $drafts,
        private SectionNavigator $nav,
        private AnswerSaver $saver,
    ) {}

    public function show(Request $request, Form $form, int $pos = 1)
    {
        // Eager load settings to prevent N+1 query
        $form->loadMissing('settings');

        $this->guard->ensureFillable($form);

        $form->load([
            'sections' => fn($q) => $q->orderBy('position'),
            'sections.questions' => fn($q) => $q->orderBy('position'),
            'sections.questions.options' => fn($q) => $q->orderBy('position'),
        ]);

        $section = $this->nav->getByPosition($form, $pos);
        abort_if(!$section, 404);

        $resp = $this->drafts->getOrCreate($form, $request);

        $answers = $resp->answers()
            ->whereIn('question_id', $section->questions->pluck('id'))
            ->with(['selectedOptions', 'fileMedia'])
            ->get()->keyBy('question_id');

        return view('forms.section', [
            'form'     => $form,
            'section'  => $section,
            'response' => $resp,
            'answers'  => $answers,
            'preview'  => false,
        ]);
    }

    public function save(Request $request, Form $form, int $pos)
    {
        // Eager load settings to prevent N+1 query
        $form->loadMissing('settings');

        $this->guard->ensureFillable($form);

        $form->load([
            'sections' => fn($q) => $q->orderBy('position'),
            'sections.questions' => fn($q) => $q->orderBy('position'),
            'sections.questions.options' => fn($q) => $q->orderBy('position'),
            'sections.questions.validations',
        ]);

        $section = $this->nav->getByPosition($form, $pos);
        abort_if(!$section, 404);

        $resp = $this->drafts->getOrCreate($form, $request);

        // Build rules per-pertanyaan
        $rules = [];
        $messages = [];
        foreach ($section->questions as $q) {
            $built = QuestionValidationRules::buildForQuestion($q, "q.{$q->id}");
            $rules["q.{$q->id}"] = $built['rules'];
            $messages = array_merge($messages, $built['messages']);

            // handle file_upload: pakai input qfile[$id]
            if ($q->type === 'file_upload') {
                $builtFile = QuestionValidationRules::buildForQuestion($q, "qfile.{$q->id}");
                $rules["qfile.{$q->id}"] = $builtFile['rules'];
                $messages = array_merge($messages, $builtFile['messages']);
                // optional: hapus 'required' di q.* agar tidak bentrok
                unset($rules["q.{$q->id}"]);
            }
        }
        $validated = $request->validate($rules, $messages);

        // Simpan jawaban
        $this->saver->saveSection($resp, $section, $request);

        if ($request->boolean('go_review')) {
            return redirect()->route('forms.review', $form->uid)
                ->with('status', 'Jawaban disimpan. Silakan review sebelum kirim.');
        }

        // Next section?
        $next = $this->nav->next($form, $section);
        if ($next) {
            return to_route('forms.section', ['form' => $form->uid, 'pos' => $next->position])
                ->with('status', 'Jawaban tersimpan.');
        }

        // Last section → siap submit
        return to_route('forms.section', ['form' => $form->uid, 'pos' => $section->position])
            ->with('ready_to_submit', true)
            ->with('status', 'Jawaban tersimpan. Silakan submit.');
    }
    public function review(Request $request, \App\Models\Form $form)
    {
        $user = $request->user();

        // Ambil draft aktif (atau yang terbaru selain submitted)
        $response = \App\Models\FormResponse::query()
            ->where('form_id', $form->id)
            ->where('respondent_user_id', $user->id)
            ->where('status', '!=', 'submitted')
            ->latest('id')
            ->first();

        if (!$response) {
            // Kalau tidak ada draft, arahkan ke start
            return redirect()->route('forms.start', $form->uid)
                ->with('status', 'Belum ada jawaban yang bisa direview.');
        }

        // Eager load struktur form & jawaban
        $form->load([
            'sections' => fn($q) => $q->orderBy('position'),
            'sections.questions' => fn($q) => $q->orderBy('position')->with('options', 'validations'),
        ]);

        $answers = $response->answers()
            ->with(['selectedOptions', 'gridCells', 'option', 'fileMedia', 'question.section'])
            ->get()
            ->keyBy('question_id');

        return view('forms.review', [
            'form'     => $form,
            'response' => $response,
            'answers'  => $answers,
        ]);
    }
}
