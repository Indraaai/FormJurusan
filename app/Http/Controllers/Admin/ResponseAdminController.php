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
            ->with([
                'respondent:id,name,email', // Select only needed columns
            ])
            ->latest('submitted_at')
            ->paginate(20);

        return view('admin.responses.index', compact('form', 'responses'));
    }

    public function show(FormResponse $response)
    {
        // Load all related data in one go
        $response->load([
            'form:id,uid,title',
            'respondent:id,name,email',
            'answers' => function ($query) {
                $query->with([
                    'question:id,title,type,section_id',
                    'question.section:id,title,position',
                    'option:id,label',
                    'selectedOptions.option:id,label',
                    'gridCells',
                    'fileMedia:id,attached_type,attached_id,original_name,path,mime,size_kb'
                ])->orderBy('question_id');
            }
        ]);

        return view('admin.responses.show', compact('response'));
    }
}
