<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Carbon\Carbon;

class TimeBeforeOrEqual implements ValidationRule
{
    public function __construct(
        private string $maxTime
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $inputTime = Carbon::createFromFormat('H:i', $value);
            $maxTime = Carbon::createFromFormat('H:i', $this->maxTime);

            if ($inputTime->gt($maxTime)) {
                $fail("Waktu harus {$this->maxTime} atau lebih awal.");
            }
        } catch (\Exception $e) {
            $fail('Format waktu tidak valid.');
        }
    }
}
