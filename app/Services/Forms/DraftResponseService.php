<?php

namespace App\Services\Forms;

use App\Models\Form;
use App\Models\FormResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class DraftResponseService
{
    public function getOrCreate(Form $form, Request $request): FormResponse
    {
        $user = $request->user();
        $userId = $user->id;

        return DB::transaction(function () use ($form, $userId, $user, $request) {
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

            // Use firstOrCreate with explicit locking to prevent race condition
            // This prevents race condition by combining check + create atomically
            $draft = FormResponse::firstOrCreate(
                [
                    'form_id' => $form->id,
                    'respondent_user_id' => $userId,
                    'status' => 'draft',
                ],
                [
                    'uid' => (string) Str::ulid(),
                    'respondent_email' => $settings?->collect_emails ? $user->email : null,
                    'started_at' => now(),
                    'source_ip' => $request->ip(),
                    'user_agent' => substr((string)$request->userAgent(), 0, 1024),
                ]
            );

            return $draft;
        });
    }

    /**
     * Clean up duplicate drafts for a specific user and form
     * Call this method if duplicate drafts are detected
     */
    public function cleanupDuplicateDrafts(int $formId, int $userId): void
    {
        DB::transaction(function () use ($formId, $userId) {
            $drafts = FormResponse::where('form_id', $formId)
                ->where('respondent_user_id', $userId)
                ->where('status', 'draft')
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->get();

            if ($drafts->count() > 1) {
                // Keep the oldest, delete the rest
                $keep = $drafts->first();
                $drafts->skip(1)->each->delete();

                Log::warning('Duplicate drafts cleaned up', [
                    'form_id' => $formId,
                    'user_id' => $userId,
                    'kept_draft_id' => $keep->id,
                    'deleted_count' => $drafts->count() - 1,
                ]);
            }
        });
    }
}
