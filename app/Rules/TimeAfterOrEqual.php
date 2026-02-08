<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Carbon\Carbon;

class TimeAfterOrEqual implements ValidationRule
{
    public function __construct(
        private string $minTime
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            // Parse time strings to comparable format
            $inputTime = Carbon::createFromFormat('H:i', $value);
            $minTime = Carbon::createFromFormat('H:i', $this->minTime);

            if ($inputTime->lt($minTime)) {
                $fail("Waktu harus {$this->minTime} atau lebih.");
            }
        } catch (\Exception $e) {
            $fail('Format waktu tidak valid.');
        }
    }
}
