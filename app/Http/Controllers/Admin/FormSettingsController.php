<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormSetting;
use Illuminate\Http\Request;

class FormSettingsController extends Controller
{
    public function edit(Form $form)
    {
        $settings = $form->settings ?: new FormSetting(['form_id' => $form->id]);
        return view('admin.forms.settings', compact('form', 'settings'));
    }

    public function update(Request $request, Form $form)
    {
        $data = $request->validate([
            'require_sign_in' => ['required', 'boolean'],
            'collect_emails' => ['required', 'boolean'],
            'limit_one_response' => ['required', 'boolean'],
            'allow_edit_after_submit' => ['required', 'boolean'],
            'show_progress_bar' => ['required', 'boolean'],
            'shuffle_question_order' => ['required', 'boolean'],
            'response_receipt_enabled' => ['required', 'boolean'],
            'confirmation_message' => ['nullable', 'string', 'max:500'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'captcha_enabled' => ['required', 'boolean'],
            'theme' => ['nullable', 'array'],
        ]);

        $settings = $form->settings ?: new FormSetting(['form_id' => $form->id]);
        $settings->fill($data);
        $settings->save();

        return back()->with('status', 'Pengaturan form disimpan.');
    }
}
