<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormSection;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuestionController extends Controller
{
    public function index(Form $form)
    {
        $form->load(['sections.questions.options']);
        return view('admin.questions.index', compact('form'));
    }

    public function create(Form $form)
    {
        // Bisa pilih section saat membuat pertanyaan
        $sections = $form->sections()->get();
        return view('admin.questions.create', compact('form', 'sections'));
    }

    public function store(Request $request, Form $form)
    {
        $data = $request->validate([
            'section_id' => ['required', 'exists:form_sections,id'],
            'type' => ['required', Rule::in(Question::TYPES)],
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'required' => ['required', 'boolean'],
            'shuffle_options' => ['required', 'boolean'],
            'other_option_enabled' => ['required', 'boolean'],
            'settings' => ['nullable', 'array'],

            // opsi sederhana (untuk MC/Checkbox/Dropdown)
            'options' => ['array'],
            'options.*.label' => ['required_with:options', 'string'],
            'options.*.value' => ['nullable', 'string'],
        ]);

        $section = FormSection::where('id', $data['section_id'])
            ->where('form_id', $form->id)->firstOrFail();

        $position = (int) ($section->questions()->max('position') ?? 0) + 1;

        $question = $section->questions()->create([
            'type' => $data['type'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'required' => $data['required'],
            'position' => $position,
            'shuffle_options' => $data['shuffle_options'],
            'other_option_enabled' => $data['other_option_enabled'],
            'settings' => $data['settings'] ?? null,
        ]);

        // Buat options bila tipe mendukung
        if (in_array($question->type, ['multiple_choice', 'checkboxes', 'dropdown']) && !empty($data['options'])) {
            $pos = 1;
            foreach ($data['options'] as $opt) {
                $question->options()->create([
                    'label' => $opt['label'],
                    'value' => $opt['value'] ?? null,
                    'position' => $pos++,
                    'role' => 'option',
                    'is_other' => false,
                ]);
            }
            if ($question->other_option_enabled) {
                $question->options()->create([
                    'label' => 'Lainnya',
                    'value' => null,
                    'position' => $pos,
                    'role' => 'option',
                    'is_other' => true,
                ]);
            }
        }

        return redirect()->route('admin.forms.questions.index', $form)
            ->with('status', 'Pertanyaan dibuat.');
    }

    public function show(Question $question)
    {
        $question->load('section.form', 'options', 'validations');
        return view('admin.questions.show', compact('question'));
    }

    public function edit(Question $question)
    {
        $question->load('section.form', 'options', 'validations');
        $form = $question->section->form;
        $sections = $form->sections;
        return view('admin.questions.edit', compact('form', 'question', 'sections'));
    }

    public function update(Request $request, Question $question)
    {
        $data = $request->validate([
            'section_id' => ['required', 'exists:form_sections,id'],
            'type' => ['required', Rule::in(Question::TYPES)],
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'required' => ['required', 'boolean'],
            'position' => ['nullable', 'integer', 'min:1'],
            'shuffle_options' => ['required', 'boolean'],
            'other_option_enabled' => ['required', 'boolean'],
            'settings' => ['nullable', 'array'],

            // Sinkronisasi opsi sederhana
            'options' => ['array'],
            'options.*.id' => ['nullable', 'integer', 'exists:question_options,id'],
            'options.*.label' => ['required_with:options', 'string'],
            'options.*.value' => ['nullable', 'string'],
            'options.*.role' => ['nullable', Rule::in(['option', 'row', 'column'])],
            'options.*.is_other' => ['nullable', 'boolean'],
        ]);

        // Pastikan section target milik form yang sama
        $targetSection = FormSection::where('id', $data['section_id'])->firstOrFail();
        $form = $targetSection->form;

        $question->update([
            'section_id' => $data['section_id'],
            'type' => $data['type'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'required' => $data['required'],
            'position' => $data['position'] ?? $question->position,
            'shuffle_options' => $data['shuffle_options'],
            'other_option_enabled' => $data['other_option_enabled'],
            'settings' => $data['settings'] ?? null,
        ]);

        // Sinkronisasi opsi (sederhana)
        if (isset($data['options'])) {
            $keepIds = [];
            $pos = 1;
            foreach ($data['options'] as $opt) {
                $payload = [
                    'label' => $opt['label'],
                    'value' => $opt['value'] ?? null,
                    'position' => $pos++,
                    'role' => $opt['role'] ?? 'option',
                    'is_other' => (bool) ($opt['is_other'] ?? false),
                ];

                if (!empty($opt['id'])) {
                    $qo = QuestionOption::where('id', $opt['id'])
                        ->where('question_id', $question->id)->first();
                    if ($qo) {
                        $qo->update($payload);
                        $keepIds[] = $qo->id;
                        continue;
                    }
                }
                $new = $question->options()->create($payload);
                $keepIds[] = $new->id;
            }
            // hapus opsi yang tidak di-keep
            QuestionOption::where('question_id', $question->id)
                ->whereNotIn('id', $keepIds)->delete();
        }

        return back()->with('status', 'Pertanyaan diperbarui.');
    }

    public function destroy(Question $question)
    {
        $form = $question->section->form;
        $question->delete();
        return redirect()->route('admin.forms.questions.index', $form)
            ->with('status', 'Pertanyaan dihapus.');
    }
}
