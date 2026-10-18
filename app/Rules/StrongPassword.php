<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * The one password rule of the learner app: at least 10 characters with a letter and a digit. It checks all three
 * together so the buyer gets a single message instead of one requirement at a time.
 */
class StrongPassword implements ValidationRule
{
    public const MESSAGE = 'Password belum memenuhi syarat.';

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = (string) $value;

        if (mb_strlen($value) < 10 || ! preg_match('/\pL/u', $value) || ! preg_match('/\pN/u', $value)) {
            $fail(self::MESSAGE);
        }
    }
}
