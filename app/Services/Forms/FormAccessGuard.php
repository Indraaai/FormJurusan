<?php

namespace App\Services\Forms;

use App\Models\Form;
use Carbon\Carbon;

class FormAccessGuard
{
    public function ensureFillable(Form $form): void
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
    }
}
