<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;

class FormPreviewController extends Controller
{
    public function show(Form $form)
    {
        $form->load([
            'sections' => fn($q) => $q->orderBy('position'),
            'sections.questions' => fn($q) => $q->orderBy('position'),
            'sections.questions.options' => fn($q) => $q->orderBy('position'),
        ]);

        return view('admin.forms.preview', [
            'form' => $form,
        ]);
    }
}
