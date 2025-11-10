<?php

namespace App\Http\Controllers\Respondent;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormResponse;
use App\Services\Forms\FormAccessGuard;
use App\Services\Forms\DraftResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StartController extends Controller
{
    public function __construct(
        private FormAccessGuard $guard,
        private DraftResponseService $drafts
    ) {}

    public function show(Request $request, Form $form)
    {
        // Eager load settings to prevent N+1 query
        $form->loadMissing('settings');

        $this->guard->ensureFillable($form);

        // Cek apakah user sudah pernah mengisi
        $existing = FormResponse::query()
            ->where('form_id', $form->id)
            ->where('respondent_user_id', $request->user()->id)
            ->where('status', 'submitted')
            ->latest('submitted_at')
            ->first();

        return view('forms.start', [
            'form'     => $form,
            'existing' => $existing,
        ]);
    }

    public function begin(Request $request, \App\Models\Form $form)
    {
        // Eager load settings to prevent N+1 query
        $form->loadMissing('settings');

        $this->guard->ensureFillable($form);

        $resp = $this->drafts->getOrCreate($form, $request);

        return to_route('forms.section', ['form' => $form->uid, 'pos' => 1]);
    }
}
