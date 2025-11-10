<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\QuestionValidation;

class QuestionValidationUpsertRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin();
    }

    public function rules(): array
    {
        $types = implode(',', QuestionValidation::TYPES);

        return [
            'validation_type' => "required|in:{$types}",

            'min_value' => 'nullable|numeric',
            'max_value' => 'nullable|numeric',
            'pattern'   => 'nullable|string|max:255',
            'message'   => 'nullable|string|max:255',

            // extras sebagai wadah bebas
            'extras' => 'nullable|array',

            // 👉 pakai nullable (bukan array) karena kita akan parse sendiri
            'extras.mimes'      => 'nullable',
            'extras.mimetypes'  => 'nullable',

            // 👉 pakai nullable + format (biar "" tidak dianggap error)
            'extras.min_date' => 'nullable|date_format:Y-m-d',
            'extras.max_date' => 'nullable|date_format:Y-m-d',
            'extras.min_time' => 'nullable|date_format:H:i',
            'extras.max_time' => 'nullable|date_format:H:i',
        ];
    }

    public function prepareForValidation(): void
    {
        $extras = $this->input('extras', []);

        // Konversi string komaseparated -> array
        foreach (['mimes', 'mimetypes'] as $k) {
            if (array_key_exists($k, $extras)) {
                $val = $extras[$k];
                if (is_string($val)) {
                    $val = trim($val);
                    if ($val === '') {
                        unset($extras[$k]); // kosong → buang, biar lolos nullable
                    } else {
                        $val = array_values(array_filter(array_map('trim', explode(',', $val))));
                        $extras[$k] = $val;  // sekarang array → lolos rules nullable|array jika kamu ganti
                    }
                }
            }
        }

        // Buang kunci tanggal/jam yang kosong supaya lolos nullable
        foreach (['min_date', 'max_date', 'min_time', 'max_time'] as $k) {
            if (array_key_exists($k, $extras) && $extras[$k] === '') {
                unset($extras[$k]);
            }
        }

        $this->merge(['extras' => $extras]);
    }
}
