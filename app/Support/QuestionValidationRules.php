<?php

namespace App\Support;

use App\Models\Question;
use App\Models\QuestionValidation;

class QuestionValidationRules
{
    /**
     * Build Laravel validation rules & messages untuk satu pertanyaan.
     * @param Question $question
     * @param string $fieldName nama field request, contoh: "q.{$question->id}"
     * @return array [ 'rules' => string|array, 'messages' => array ]
     */
    public static function buildForQuestion(Question $question, string $fieldName): array
    {
        $rules = [];
        $messages = [];

        // Wajib diisi dari kolom question.required (di luar tabel validation)
        if ($question->required) {
            $rules[] = 'required';
            $messages["{$fieldName}.required"] = 'Pertanyaan ini wajib diisi.';
        } else {
            // supaya aturan lain tidak error saat kosong
            $rules[] = 'nullable';
        }

        /** @var \Illuminate\Support\Collection $vals */
        $vals = $question->validations; // pastikan eager load di controller jika butuh

        foreach ($vals as $val) {
            /** @var QuestionValidation $val */
            $msg = $val->message ?? null;
            $extras = $val->extras ?? [];

            switch ($val->validation_type) {
                case 'text_length':
                    // gunakan min_value/max_value sebagai integer
                    if (!is_null($val->min_value)) {
                        $rules[] = 'min:' . (int)$val->min_value;
                        if ($msg) $messages["{$fieldName}.min"] = $msg;
                    }
                    if (!is_null($val->max_value)) {
                        $rules[] = 'max:' . (int)$val->max_value;
                        if ($msg) $messages["{$fieldName}.max"] = $msg;
                    }
                    // untuk text, tambahkan tipe string
                    if (in_array($question->type, ['short_text', 'long_text'])) {
                        array_unshift($rules, 'string');
                    }
                    break;

                case 'regex':
                    if ($val->pattern) {
                        // Pastikan delimiter regex aman; asumsikan pattern sudah /.../ atau plain
                        $pattern = self::ensureRegexDelimited($val->pattern);
                        $rules[] = 'regex:' . $pattern;
                        if ($msg) $messages["{$fieldName}.regex"] = $msg;
                    }
                    break;

                case 'number_range':
                    $rules[] = 'numeric';
                    if (!is_null($val->min_value)) {
                        $rules[] = 'min:' . +$val->min_value;
                        if ($msg) $messages["{$fieldName}.min"] = $msg;
                    }
                    if (!is_null($val->max_value)) {
                        $rules[] = 'max:' . +$val->max_value;
                        if ($msg) $messages["{$fieldName}.max"] = $msg;
                    }
                    // optional step di extras (tidak native laravel), bisa dibuat custom rule nanti
                    break;

                case 'date_range':
                    // gunakan format Y-m-d di min/max_value (disimpan sebagai decimal di tabelmu;
                    // saran: taruh di extras['min_date'] / ['max_date'])
                    $min = $extras['min_date'] ?? null;
                    $max = $extras['max_date'] ?? null;
                    $rules[] = 'date';
                    if ($min) {
                        $rules[] = 'after_or_equal:' . $min;
                        if ($msg) $messages["{$fieldName}.after_or_equal"] = $msg;
                    }
                    if ($max) {
                        $rules[] = 'before_or_equal:' . $max;
                        if ($msg) $messages["{$fieldName}.before_or_equal"] = $msg;
                    }
                    break;

                case 'time_range':
                    $min = $extras['min_time'] ?? null; // format HH:MM
                    $max = $extras['max_time'] ?? null;
                    // time di Laravel: pakai date_format atau after/before custom; paling praktis: regex HH:MM + custom compare di FormRequest bila perlu.
                    $rules[] = 'date_format:H:i';
                    if ($min) {
                        $rules[] = "after_or_equal:$min";
                        if ($msg) $messages["{$fieldName}.after_or_equal"] = $msg;
                    }
                    if ($max) {
                        $rules[] = "before_or_equal:$max";
                        if ($msg) $messages["{$fieldName}.before_or_equal"] = $msg;
                    }
                    break;

                case 'file_type':
                    // gunakan extras['mimes'] = ['jpg','png'] atau ['image/jpeg','application/pdf'].
                    if (!empty($extras['mimetypes'])) {
                        $rules[] = 'mimetypes:' . implode(',', (array)$extras['mimetypes']);
                        if ($msg) $messages["{$fieldName}.mimetypes"] = $msg;
                    } elseif (!empty($extras['mimes'])) {
                        $rules[] = 'mimes:' . implode(',', (array)$extras['mimes']);
                        if ($msg) $messages["{$fieldName}.mimes"] = $msg;
                    }
                    break;

                case 'file_size':
                    // Laravel file size satuannya KB untuk rule 'max'
                    // Simpan max_value sebagai KB (disarankan). Jika kamu simpan MB, kali 1024 di sini.
                    if (!is_null($val->max_value)) {
                        $rules[] = 'max:' . (int)$val->max_value; // asumsikan KB
                        if ($msg) $messages["{$fieldName}.max"] = $msg;
                    }
                    break;

                case 'required':
                    // sudah di-handle oleh $question->required, tapi kalau kamu tetap pakai record ini:
                    // naikkan prioritas: ganti 'nullable' dengan 'required'
                    $rules = array_values(array_filter($rules, fn($r) => $r !== 'nullable'));
                    array_unshift($rules, 'required');
                    if ($msg) $messages["{$fieldName}.required"] = $msg;
                    break;
            }
        }

        // Normalisasi: gabung unique bagian file
        $rules = self::normalizeRules($rules, $question);

        return ['rules' => $rules, 'messages' => $messages];
    }

    private static function ensureRegexDelimited(string $pattern): string
    {
        // Jika pattern sudah diawali delimiter / atau #, biarkan
        $first = substr($pattern, 0, 1);
        $last  = substr($pattern, -1);
        if ($first === '/' && $last === '/') return $pattern;
        if ($first === '#' && $last === '#') return $pattern;
        // Bungkus dengan /.../ tanpa flags
        return '/' . str_replace('/', '\/', $pattern) . '/';
    }

    private static function normalizeRules(array $rules, Question $q): array
    {
        // Jika tipe file_upload, pastikan rule 'file' ada
        if ($q->type === 'file_upload' && !in_array('file', $rules, true)) {
            array_unshift($rules, 'file');
        }
        return $rules;
    }
}
