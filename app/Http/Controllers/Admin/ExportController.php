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

            // Header kolom
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

                            // BUG-011 FIX: Use explicit conditionals instead of fragile ?? chain
                            $val = $this->resolveAnswerValue($ans);

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

    /**
     * BUG-011 FIX: Resolve answer value with explicit type checks
     * instead of fragile null-coalescing chain.
     */
    private function resolveAnswerValue($ans): string
    {
        // Text values (most common)
        if ($ans->long_text_value !== null) {
            return $ans->long_text_value;
        }
        if ($ans->text_value !== null) {
            return $ans->text_value;
        }

        // Single-choice option
        $optionLabel = $ans->option_label_snapshot ?: optional($ans->option)->label;
        if ($optionLabel !== null) {
            return $optionLabel;
        }

        // Multi-choice (checkboxes) — join labels
        if ($ans->selectedOptions && $ans->selectedOptions->isNotEmpty()) {
            return $ans->selectedOptions
                ->map(fn($so) => $so->option_label_snapshot ?? optional($so->option)->label ?? '')
                ->filter()
                ->implode(', ');
        }

        // Grid cells
        if ($ans->gridCells && $ans->gridCells->isNotEmpty()) {
            return $ans->gridCells
                ->map(fn($gc) => ($gc->row_label ?? '') . ': ' . ($gc->col_label ?? ''))
                ->implode('; ');
        }

        // Numeric — explicit check for 0
        if ($ans->number_value !== null) {
            return (string) $ans->number_value;
        }

        // Date/time
        if ($ans->date_value !== null) {
            return $ans->date_value instanceof \Carbon\Carbon
                ? $ans->date_value->format('Y-m-d')
                : (string) $ans->date_value;
        }
        if ($ans->time_value !== null) {
            return $ans->time_value instanceof \Carbon\Carbon
                ? $ans->time_value->format('H:i:s')
                : (string) $ans->time_value;
        }
        if ($ans->datetime_value !== null) {
            return $ans->datetime_value instanceof \Carbon\Carbon
                ? $ans->datetime_value->format('Y-m-d H:i:s')
                : (string) $ans->datetime_value;
        }

        // File upload
        if ($ans->fileMedia) {
            return $ans->fileMedia->original_name ?? $ans->fileMedia->path ?? '';
        }

        return '';
    }
}
