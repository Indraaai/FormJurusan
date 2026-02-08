<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function csv(Form $form): StreamedResponse
    {
        $filename = 'form-' . $form->uid . '-responses.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->stream(function () use ($form) {
            $out = fopen('php://output', 'w');

            // UTF-8 BOM agar Excel nyaman
            fwrite($out, "\xEF\xBB\xBF");

            // Header kolom sederhana (bisa diperkaya)
            fputcsv($out, ['Response UID', 'User', 'Email', 'Submitted At', 'Question', 'Answer']);

            // Use chunk to prevent memory exhaustion with large datasets
            $form->responses()
                ->with([
                    'respondent:id,name,email',
                    'answers.question:id,title',
                    'answers.option:id,label',
                    'answers.fileMedia:id,attached_type,attached_id,path'
                ])
                ->chunk(100, function ($responses) use ($out) {
                    foreach ($responses as $resp) {
                        $user = optional($resp->respondent)->name ?? '-';
                        $email = $resp->respondent_email ?? optional($resp->respondent)->email ?? '-';
                        foreach ($resp->answers as $ans) {
                            $q = $ans->question_text_snapshot ?: optional($ans->question)->title;
                            $val = $ans->long_text_value
                                ?? $ans->text_value
                                ?? ($ans->option_label_snapshot ?: optional($ans->option)->label)
                                ?? (string) $ans->number_value
                                ?? ($ans->date_value ? $ans->date_value->format('Y-m-d') : null)
                                ?? ($ans->time_value ? $ans->time_value->format('H:i:s') : null)
                                ?? ($ans->datetime_value ? $ans->datetime_value->format('Y-m-d H:i:s') : null)
                                ?? ($ans->fileMedia ? $ans->fileMedia->path : null)
                                ?? '';

                            fputcsv($out, [
                                $resp->uid,
                                $user,
                                $email,
                                optional($resp->submitted_at)->format('Y-m-d H:i:s'),
                                $q,
                                $val,
                            ]);
                        }
                    }
                });

            fclose($out);
        }, 200, $headers);
    }
}
