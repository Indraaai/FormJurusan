<?php

namespace App\Services\Forms;

use App\Models\FormResponse;
use App\Models\FormSection;
use App\Models\FormAnswer;
use App\Models\MediaAsset;
use App\Models\QuestionOption;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class AnswerSaver
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }
    /**
     * Simpan semua jawaban untuk 1 section.
     */
    public function saveSection(FormResponse $resp, FormSection $section, Request $request): void
    {
        // BUG-013 FIX: Only log sensitive request data in local environment
        if (app()->environment('local')) {
            Log::debug('AnswerSaver.saveSection - request data', [
                'response_id' => $resp->id,
                'request_all' => $request->all(),
            ]);
        }

        DB::transaction(function () use ($resp, $section, $request) {
            foreach ($section->questions as $q) {
                $key    = "q.{$q->id}";
                $fileKey = "qfile.{$q->id}";

                $answer = FormAnswer::firstOrNew([
                    'response_id' => $resp->id,
                    'question_id' => $q->id,
                ]);
                $answer->question_text_snapshot = $q->title;

                // reset nilai
                $answer->text_value = $answer->long_text_value = null;
                $answer->number_value = null;
                $answer->date_value = $answer->time_value = $answer->datetime_value = null;
                $answer->option_id = null;
                $answer->option_label_snapshot = null;

                // bersihkan pilihan banyak
                if ($answer->exists) {
                    $answer->selectedOptions()->delete();
                    $answer->gridCells()->delete(); // grid belum dipakai; aman
                }

                switch ($q->type) {
                    case 'short_text':
                    case 'email':    // BUG-014 FIX: Handle email type (stored as text)
                    case 'url':      // BUG-014 FIX: Handle url type (stored as text)
                        $val = $this->sanitizeInput($request->input($key));
                        if ($val !== null) $answer->text_value = $val;
                        break;

                    case 'long_text':
                        $val = $this->sanitizeInput($request->input($key));
                        if ($val !== null) $answer->long_text_value = $val;
                        break;

                    case 'number':   // BUG-014 FIX: Handle number type explicitly
                        $val = $request->input($key);
                        if ($val !== null && $val !== '') $answer->number_value = (float) $val;
                        break;

                    case 'multiple_choice':
                    case 'dropdown':
                        $optId = $request->input($key);
                        if ($optId !== null && $optId !== '') {
                            // Strict validation: must be numeric
                            if (!is_numeric($optId)) {
                                throw new \InvalidArgumentException("Option ID must be numeric for question {$q->id}");
                            }

                            $opt = $this->validateOptionId($optId, $q->id);
                            if ($opt) {
                                $answer->option_id = $opt->id;
                                $answer->option_label_snapshot = $opt->label;
                            }
                        }
                        break;

                    case 'checkboxes':
                        // disimpan di selectedOptions (hasMany)
                        // Save dulu untuk mendapat ID (jika record baru)
                        $answer->save();
                        $ids = (array) $request->input($key, []);

                        // Validate all IDs are numeric
                        foreach ($ids as $id) {
                            if (!is_numeric($id)) {
                                throw new \InvalidArgumentException("All option IDs must be numeric for question {$q->id}");
                            }
                        }

                        $validIds = QuestionOption::where('question_id', $q->id)
                            ->whereIn('id', $ids)->pluck('id')->all();

                        foreach ($validIds as $oid) {
                            $opt = QuestionOption::find($oid);
                            $answer->selectedOptions()->create([
                                'option_id' => $oid,
                                'option_label_snapshot' => $opt?->label,
                            ]);
                        }
                        // Note: $answer->save() dipanggil lagi di akhir loop untuk update timestamps
                        break;

                    case 'linear_scale':
                        $val = $request->input($key);
                        if ($val !== null && $val !== '') $answer->number_value = (int) $val;
                        break;

                    case 'date':
                        $val = $request->input($key);
                        if ($val) $answer->date_value = Carbon::parse($val);
                        break;

                    case 'time':
                        $val = $request->input($key);
                        if ($val) $answer->time_value = $val; // simpan string 'H:i'
                        break;

                    case 'datetime':  // BUG-014 FIX: Handle datetime type
                    case 'date_time': // alias
                        $val = $request->input($key);
                        if ($val) $answer->datetime_value = Carbon::parse($val);
                        break;

                    case 'file_upload':
                        if ($request->hasFile($fileKey)) {
                            $f = $request->file($fileKey);
                            $path = $this->fileUploadService->uploadFile($f);

                            $answer->save();

                            MediaAsset::updateOrCreate(
                                ['attached_type' => 'answer', 'attached_id' => $answer->id],
                                [
                                    'form_id'       => $resp->form_id,
                                    'type'          => 'file',
                                    'disk'          => 'local',
                                    'path'          => $path,
                                    'mime'          => $f->getClientMimeType(),
                                    'original_name' => $f->getClientOriginalName(),
                                    'sha256'        => @hash_file('sha256', $f->getRealPath()) ?: null,
                                    'size_kb'       => (int) round(($f->getSize() ?? 0) / 1024),
                                    'created_by'    => $request->user()->id ?? null,
                                ]
                            );
                        }
                        break;

                    case 'mc_grid':
                        // BUG-007 FIX: Handle multiple-choice grid
                        // Expected input: q[{$q->id}][row_option_id] = col_option_id
                        $answer->save();
                        $gridData = (array) $request->input($key, []);
                        foreach ($gridData as $rowOptId => $colOptId) {
                            if (!is_numeric($rowOptId) || !is_numeric($colOptId)) continue;
                            $rowOpt = QuestionOption::where('question_id', $q->id)
                                ->where('role', 'row')->find($rowOptId);
                            $colOpt = QuestionOption::where('question_id', $q->id)
                                ->where('role', 'column')->find($colOptId);
                            if ($rowOpt && $colOpt) {
                                $answer->gridCells()->create([
                                    'row_option_id' => $rowOpt->id,
                                    'col_option_id' => $colOpt->id,
                                    'row_label'     => $rowOpt->label,
                                    'col_label'     => $colOpt->label,
                                ]);
                            }
                        }
                        break;

                    case 'checkbox_grid':
                        // BUG-007 FIX: Handle checkbox grid
                        // Expected input: q[{$q->id}][row_option_id][] = [col_option_id, ...]
                        $answer->save();
                        $gridData = (array) $request->input($key, []);
                        foreach ($gridData as $rowOptId => $colOptIds) {
                            if (!is_numeric($rowOptId)) continue;
                            $rowOpt = QuestionOption::where('question_id', $q->id)
                                ->where('role', 'row')->find($rowOptId);
                            if (!$rowOpt) continue;
                            foreach ((array) $colOptIds as $colOptId) {
                                if (!is_numeric($colOptId)) continue;
                                $colOpt = QuestionOption::where('question_id', $q->id)
                                    ->where('role', 'column')->find($colOptId);
                                if ($colOpt) {
                                    $answer->gridCells()->create([
                                        'row_option_id' => $rowOpt->id,
                                        'col_option_id' => $colOpt->id,
                                        'row_label'     => $rowOpt->label,
                                        'col_label'     => $colOpt->label,
                                    ]);
                                }
                            }
                        }
                        break;

                    default:
                        $val = $request->input($key);
                        if ($val !== null) $answer->text_value = is_array($val) ? json_encode($val) : $val;
                }

                $answer->save();
            }
        });
    }

    private function validateOptionId($optionId, $questionId)
    {
        if (!is_numeric($optionId)) {
            throw new \InvalidArgumentException('Option ID harus berupa angka');
        }

        $optionId = (int) $optionId;
        $option = QuestionOption::where('question_id', $questionId)->find($optionId);

        if (!$option) {
            throw new \InvalidArgumentException('Option tidak valid untuk pertanyaan ini');
        }

        return $option;
    }

    /**
     * Sanitize input by trimming whitespace only.
     *
     * SECURITY NOTE: We do NOT htmlspecialchars() here because:
     * 1. Data should be stored in raw form in database
     * 2. Laravel Blade {{ }} auto-escapes output (prevents XSS)
     * 3. Early encoding causes double-encoding issues
     *
     * Follow principle: "Escape on OUTPUT, not on INPUT"
     *
     * @param mixed $input
     * @return mixed
     */
    private function sanitizeInput($input)
    {
        if ($input === null) {
            return null;
        }

        if (is_array($input)) {
            // Recursively sanitize array values
            return array_map([$this, 'sanitizeInput'], $input);
        }

        if (is_string($input)) {
            // Only trim whitespace - that's it!
            // Security: Laravel Blade {{ }} auto-escapes output
            return trim($input);
        }

        // Return as-is for other types (numbers, booleans, etc.)
        return $input;
    }
}
