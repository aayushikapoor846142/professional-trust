<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StringLimitRule implements ValidationRule
{
    /**
     * Validate the rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if the string contains any spaces
        if (strpos($value, ' ') !== false) {
            // Case 1: String contains spaces
            // Ensure the total length of the string (including spaces) does not exceed 40 characters
            if (strlen($value) > 40) {
                $fail("The $attribute may not exceed 40 characters in total, including spaces.");
            }
        } else {
            // Case 2: String contains no spaces
            // Ensure that the string does not exceed 15 characters
            if (strlen($value) > 15) {
                $fail("The $attribute may not exceed 15 characters if it contains no spaces.");
            }
        }
    }

    /**
     * Add passes method for validation logic
     */
    public function passes($attribute, $value)
    {
        // Check if the string contains any spaces
        if (strpos($value, ' ') !== false) {
            // Ensure the total length of the string (including spaces) does not exceed 40 characters
            return strlen($value) <= 40;
        } else {
            // Ensure that the string does not exceed 15 characters if there are no spaces
            return strlen($value) <= 15;
        }
    }

    /**
     * Custom error message.
     */
    public function message()
    {
        return 'The :attribute must not exceed 15 characters if it contains no spaces, and 40 characters in total if it contains spaces.';
    }
}
