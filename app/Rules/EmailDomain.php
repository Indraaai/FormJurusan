<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EmailDomain implements ValidationRule
{
    public function __construct(
        private array $allowedDomains = []
    ) {
        if (empty($this->allowedDomains)) {
            $this->allowedDomains = config('formapp.respondent_allowed_domains', []);
        }
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $email = strtolower((string) $value);

        foreach ($this->allowedDomains as $domain) {
            $domain = strtolower(ltrim($domain, '@'));
            if (str_ends_with($email, '@' . $domain)) {
                return; // valid
            }
        }

        $fail('Email harus menggunakan domain: @' . implode(', @', $this->allowedDomains));
    }
}
