<?php

namespace App\Http\Controllers\Respondent;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitFormRequest;
use App\Models\Form;
use App\Models\FormResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubmitController extends Controller
{
    public function submit(SubmitFormRequest $request, Form $form)
    {
        $user = $request->user();

        // Get the draft response (validation already done in FormRequest)
        $response = FormResponse::query()
            ->where('form_id', $form->id)
            ->where('respondent_user_id', $user->id)
            ->where('status', '!=', 'submitted')
            ->latest('id')
            ->firstOrFail();

        // Submit the response in a transaction
        DB::transaction(function () use ($request, $response, $user) {
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
        });

        return redirect()->route('forms.done', ['form' => $form->uid])
            ->with('status', 'Respons berhasil terkirim.');
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
