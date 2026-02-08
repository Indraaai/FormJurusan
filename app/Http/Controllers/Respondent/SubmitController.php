<?php

namespace App\Http\Controllers\Respondent;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitFormRequest;
use App\Models\Form;
use App\Models\FormResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubmitController extends Controller
{
    public function submit(SubmitFormRequest $request, Form $form)
    {
        $user = $request->user();

        // BUG-003 FIX: Always re-fetch with lockForUpdate inside transaction
        // to prevent race condition between validation and submission
        return DB::transaction(function () use ($request, $form, $user) {
            // Re-fetch response with pessimistic lock to prevent concurrent submissions
            $response = FormResponse::query()
                ->where('form_id', $form->id)
                ->where('respondent_user_id', $user->id)
                ->where('status', 'draft')
                ->lockForUpdate()
                ->latest('id')
                ->first();

            if (!$response) {
                abort(403, 'Draft response tidak ditemukan atau sudah dikirim sebelumnya.');
            }

            // Double-check status inside lock (belt and suspenders)
            if ($response->status !== 'draft') {
                abort(403, 'Respons ini sudah dikirim sebelumnya.');
            }

            // Submit the response
            $started  = $response->started_at ?: ($response->created_at ?: now());
            $duration = now()->diffInSeconds($started);

            $response->fill([
                'status'           => 'submitted',
                'submitted_at'     => now(),
                'duration_seconds' => max(0, (int) $duration),
                'respondent_email' => $user->email,
                'source_ip'        => $request->ip(),
                'user_agent'       => $request->userAgent(),
            ])->save();

            // Log submission for audit trail
            Log::channel('forms')->info('Form response submitted', [
                'form_id' => $response->form_id,
                'response_id' => $response->id,
                'user_id' => $user->id,
                'duration_seconds' => $response->duration_seconds,
            ]);

            return redirect()->route('forms.done', ['form' => $form->uid])
                ->with('status', 'Respons berhasil terkirim.');
        });
    }

    public function done(Request $request, Form $form)
    {
        $response = FormResponse::query()
            ->where('form_id', $form->id)
            ->where('respondent_user_id', $request->user()->id)
            ->latest('submitted_at')
            ->first();

        return view('forms.done', compact('form', 'response'));
    }
}
