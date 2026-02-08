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

        $userId = $request->user()->id;

        // Cek apakah user sudah pernah submit
        $submitted = FormResponse::query()
            ->where('form_id', $form->id)
            ->where('respondent_user_id', $userId)
            ->where('status', 'submitted')
            ->latest('submitted_at')
            ->first();

        // Cek apakah ada draft yang belum selesai
        $draft = FormResponse::query()
            ->where('form_id', $form->id)
            ->where('respondent_user_id', $userId)
            ->where('status', 'draft')
            ->latest('id')
            ->first();

        return view('forms.start', [
            'form'      => $form,
            'submitted' => $submitted,
            'draft'     => $draft,
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
