<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormLogicRule;
use App\Models\FormSection;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LogicController extends Controller
{
    public function index(Form $form)
    {
        $form->load(['sections', 'questions']);
        $rules = $form->logicRules()->orderBy('priority')->get();
        return view('admin.logic.index', compact('form', 'rules'));
    }

    public function create(Form $form)
    {
        $questions = $form->questions()->get();
        $sections = $form->sections()->get();
        return view('admin.logic.create', compact('form', 'questions', 'sections'));
    }

    public function store(Request $request, Form $form)
    {
        $data = $request->validate([
            'source_question_id' => ['required', 'exists:questions,id'],
            'operator' => ['required', Rule::in(FormLogicRule::OPERATORS)],
            'value_text' => ['nullable', 'string'],
            'value_number' => ['nullable', 'numeric'],
            'option_id' => ['nullable', 'integer', 'exists:question_options,id'],
            'target_section_id' => ['nullable', 'integer', 'exists:form_sections,id'],
            'action' => ['required', Rule::in(FormLogicRule::ACTIONS)],
            'priority' => ['required', 'integer', 'min:0'],
            'is_enabled' => ['required', 'boolean'],
        ]);

        // Validasi ringan: pastikan source question & target section milik form yg sama
        $source = Question::where('id', $data['source_question_id'])->firstOrFail();
        if ($source->section->form_id !== $form->id) {
            abort(422, 'Pertanyaan tidak milik form ini.');
        }
        if (!empty($data['target_section_id'])) {
            $target = FormSection::findOrFail($data['target_section_id']);
            if ($target->form_id !== $form->id) {
                abort(422, 'Section target tidak milik form ini.');
            }
        }

        $form->logicRules()->create($data);

        return redirect()->route('admin.forms.logic.index', $form)
            ->with('status', 'Rule logic dibuat.');
    }

    public function edit(FormLogicRule $logicRule)
    {
        $form = $logicRule->form()->with(['sections', 'questions'])->first();
        return view('admin.logic.edit', compact('form', 'logicRule'));
    }

    public function update(Request $request, FormLogicRule $logicRule)
    {
        $data = $request->validate([
            'operator' => ['required', Rule::in(FormLogicRule::OPERATORS)],
            'value_text' => ['nullable', 'string'],
            'value_number' => ['nullable', 'numeric'],
            'option_id' => ['nullable', 'integer', 'exists:question_options,id'],
            'target_section_id' => ['nullable', 'integer', 'exists:form_sections,id'],
            'action' => ['required', Rule::in(FormLogicRule::ACTIONS)],
            'priority' => ['required', 'integer', 'min:0'],
            'is_enabled' => ['required', 'boolean'],
        ]);

        $logicRule->update($data);

        return back()->with('status', 'Rule logic diperbarui.');
    }

    public function destroy(FormLogicRule $logicRule)
    {
        $form = $logicRule->form;
        $logicRule->delete();
        return redirect()->route('admin.forms.logic.index', $form)
            ->with('status', 'Rule logic dihapus.');
    }
}
