<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneNumberValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if the value is numeric and has a length between 9 and 15
        if (!preg_match('/^[\d\s\(\)\-]{9,20}$/', $value) || !preg_match('/\d{9,15}/', preg_replace('/\D/', '', $value))) {
            $fail("The $attribute is the not valid.");
        }
    }

    public function passes($attribute, $value)
    {
        // Check if the value contains only numbers and has a length between 9 and 15
        return preg_match('/^[\d\s\(\)\-]{9,20}$/', $value) && preg_match('/\d{9,15}/', preg_replace('/\D/', '', $value));
    }

    public function message()
    {
        return 'The :attribute is the not valid.';
    }
}
