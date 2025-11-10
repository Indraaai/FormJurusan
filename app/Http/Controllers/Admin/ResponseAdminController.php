<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormResponse;

class ResponseAdminController extends Controller
{
    public function index(Form $form)
    {
        $responses = $form->responses()
            ->with('respondent')
            ->latest('submitted_at')
            ->paginate(20);

        return view('admin.responses.index', compact('form', 'responses'));
    }

    public function show(FormResponse $response)
    {
        $response->load(['form', 'respondent', 'answers.question', 'answers.option', 'answers.fileMedia']);
        return view('admin.responses.show', compact('response'));
    }
}
