<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\QuestionValidationUpsertRequest;
use App\Models\Question;
use App\Models\QuestionValidation;

class QuestionValidationAdminController extends Controller
{
    public function index(Question $question)
    {
        $question->load(['section.form', 'validations']);
        return view('admin/questions/validations/index', [
            'question' => $question,
            'form'     => $question->section->form,
            'validations' => $question->validations()->latest('id')->get(),
        ]);
    }

    public function create(Question $question)
    {
        $question->load(['section.form']);
        return view('admin/questions/validations/create', [
            'question' => $question,
            'form'     => $question->section->form,
        ]);
    }

    public function store(QuestionValidationUpsertRequest $req, Question $question)
    {
        $payload = $req->validated();
        $payload['question_id'] = $question->id;
        QuestionValidation::create($payload);

        return to_route('admin.questions.validations.index', $question)
            ->with('status', 'Validation ditambahkan.');
    }

    public function edit(QuestionValidation $validation)
    {
        $validation->load('question.section.form');
        return view('admin/questions/validations/edit', [
            'validation' => $validation,
            'question'   => $validation->question,
            'form'       => $validation->question->section->form,
        ]);
    }

    public function update(QuestionValidationUpsertRequest $req, QuestionValidation $validation)
    {
        $validation->update($req->validated());
        return to_route('admin.questions.validations.index', $validation->question)
            ->with('status', 'Validation diperbarui.');
    }

    public function destroy(QuestionValidation $validation)
    {
        $question = $validation->question;
        $validation->delete();
        return to_route('admin.questions.validations.index', $question)
            ->with('status', 'Validation dihapus.');
    }
}
