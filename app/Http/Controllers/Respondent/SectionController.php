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
use Illuminate\Support\Facades\Log;

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

        // Load all needed relations in one optimized query
        $form->load([
            'sections' => fn($q) => $q->orderBy('position')
                ->select('id', 'form_id', 'title', 'description', 'position'),
            'sections.questions' => fn($q) => $q->orderBy('position')
                ->select(
                    'id',
                    'section_id',
                    'type',
                    'title',
                    'description',
                    'required',
                    'position',
                    'shuffle_options',
                    'other_option_enabled',
                    'settings'
                ),
            'sections.questions.options' => fn($q) => $q->orderBy('position')
                ->select('id', 'question_id', 'label', 'value', 'position', 'role', 'is_other'),
            'sections.questions.validations' => fn($q) => $q
                ->select(
                    'id',
                    'question_id',
                    'validation_type',
                    'min_value',
                    'max_value',
                    'pattern',
                    'message',
                    'extras'
                ),
        ]);

        $section = $this->nav->getByPosition($form, $pos);
        abort_if(!$section, 404);

        $resp = $this->drafts->getOrCreate($form, $request);

        // Eager load answer relations
        $answers = $resp->answers()
            ->whereIn('question_id', $section->questions->pluck('id'))
            ->with([
                'selectedOptions:id,answer_id,option_id,option_label_snapshot',
                'fileMedia:id,attached_type,attached_id,original_name,path,mime'
            ])
            ->get()
            ->keyBy('question_id');

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

        // BUG-013 FIX: Only log sensitive request data in local environment
        if (app()->environment('local')) {
            Log::debug('Section save validation', [
                'form_id' => $form->id,
                'section_id' => $section->id,
                'response_id' => $resp->id,
                'rules' => $rules,
                'request_all' => $request->all(),
            ]);
        }

        $validated = $request->validate($rules, $messages);

        // BUG-013 FIX: Only log in local environment
        if (app()->environment('local')) {
            Log::debug('Validation passed', [
                'validated_data_keys' => array_keys($validated),
            ]);
        }

        // Simpan jawaban
        try {
            $this->saver->saveSection($resp, $section, $request);
        } catch (\InvalidArgumentException $e) {
            // Validation errors from AnswerSaver - show to user
            return back()
                ->withErrors(['answer_validation' => $e->getMessage()])
                ->withInput();
        } catch (\Exception $e) {
            // Unexpected error - log and show generic message
            Log::error('Failed to save form section', [
                'form_id' => $form->id,
                'section_id' => $section->id,
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->with('error', 'Terjadi kesalahan saat menyimpan jawaban. Silakan coba lagi.')
                ->withInput();
        }

        // Jika user klik "Review" atau ini section terakhir
        if ($request->boolean('go_review')) {
            return redirect()->route('forms.review', $form->uid)
                ->with('status', 'Jawaban disimpan. Silakan review sebelum kirim.');
        }

        // Next section? (BUG-006 FIX: pass response for branching logic evaluation)
        $next = $this->nav->next($form, $section, $resp);
        if ($next) {
            return to_route('forms.section', ['form' => $form->uid, 'pos' => $next->position])
                ->with('status', 'Jawaban tersimpan.');
        }

        // Jika ini section terakhir dan user tidak klik review, auto redirect ke review
        return redirect()->route('forms.review', $form->uid)
            ->with('status', 'Jawaban tersimpan. Silakan review sebelum mengirim.');
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
