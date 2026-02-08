<?php

namespace App\Services\Forms;

use App\Models\Form;
use App\Models\FormResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class FormAccessGuard
{
    /**
     * Ensure the form is accessible and fillable by the current user.
     *
     * @param Form $form
     * @param int|null $userId  Override user ID (default: current auth user)
     */
    public function ensureFillable(Form $form, ?int $userId = null): void
    {
        // Use 403 (Forbidden) instead of 404 for unpublished forms
        if (!$form->is_published) {
            abort(403, 'Form ini belum dipublikasikan.');
        }

        // Ensure settings is loaded to prevent N+1
        if (!$form->relationLoaded('settings')) {
            $form->load('settings');
        }

        // Get start and end datetime from settings
        $start = $form->settings?->start_at;
        $end   = $form->settings?->end_at;

        $start = $start ? Carbon::parse($start) : null;
        $end   = $end   ? Carbon::parse($end)   : null;

        if ($start && now()->lt($start)) {
            abort(403, 'Form belum aktif. Mulai: ' . $start->format('d M Y H:i'));
        }

        if ($end && now()->gt($end)) {
            abort(403, 'Form sudah ditutup. Berakhir: ' . $end->format('d M Y H:i'));
        }

        // BUG-009 FIX: Check limit_one_response
        $this->checkOneResponseLimit($form, $userId ?? Auth::id());
    }

    /**
     * If limit_one_response is enabled, prevent user from filling again after submit.
     */
    protected function checkOneResponseLimit(Form $form, ?int $userId): void
    {
        if (!$userId) return;
        if (!$form->settings?->limit_one_response) return;

        $hasSubmitted = FormResponse::where('form_id', $form->id)
            ->where('respondent_user_id', $userId)
            ->where('status', 'submitted')
            ->exists();

        if ($hasSubmitted) {
            // Check if edit after submit is allowed
            if (!$form->settings?->allow_edit_after_submit) {
                abort(403, 'Kamu sudah mengisi form ini. Pengisian dibatasi satu kali.');
            }
        }
    }
}
