<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidEmail implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
       // Check if the value matches the email regex pattern
       if (!preg_match('/^([a-zA-Z0-9._%+-]+)@([a-zA-Z0-9.-]+\.[a-zA-Z]{2,})$/', $value)) {
        // If it doesn't match, call the $fail callback with the error message
        $fail('The ' . $attribute . ' must be a valid email address.');
    }
    }

    public function passes($attribute, $value)
    {
        return preg_match('/^([a-zA-Z0-9._%+-]+)@([a-zA-Z0-9.-]+\.[a-zA-Z]{2,})$/', $value);
        
    }

    public function message()
    {
        return 'The :attribute must be a valid email address.';
    }
}
