<?php

namespace App\Services\Forms;

use App\Models\Form;
use App\Models\FormResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DraftResponseService
{
    public function getOrCreate(Form $form, Request $request): FormResponse
    {
        $user = $request->user();
        $userId = $user->id;

        // Use database transaction with locking to prevent race condition
        return DB::transaction(function () use ($form, $userId, $user, $request) {
            // Get settings
            $settings = $form->settings;

            // Check if limit_one_response and already submitted
            if ($settings?->limit_one_response) {
                $submitted = FormResponse::where('form_id', $form->id)
                    ->where('respondent_user_id', $userId)
                    ->where('status', 'submitted')
                    ->lockForUpdate()
                    ->first();

                if ($submitted) {
                    return $submitted;
                }
            }

            // Look for existing draft with database lock
            $draft = FormResponse::where('form_id', $form->id)
                ->where('respondent_user_id', $userId)
                ->where('status', 'draft')
                ->lockForUpdate()
                ->first();

            if ($draft) {
                return $draft;
            }

            // Create new draft response
            return FormResponse::create([
                'uid'                 => (string) Str::ulid(),
                'form_id'             => $form->id,
                'respondent_user_id'  => $userId,
                'respondent_email'    => $settings?->collect_emails ? $user->email : null,
                'status'              => 'draft',
                'started_at'          => now(),
                'source_ip'           => $request->ip(),
                'user_agent'          => substr((string)$request->userAgent(), 0, 1024),
            ]);
        });
    }
}
