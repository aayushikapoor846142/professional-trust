<?php
namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class InputStringValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (
            preg_match('/^[a-zA-Z\s]+$/', $value)                     // Minimum 8 characters
        ) {
            $fail('The :attribute may only contain letters and spaces.');
        }
    }

    public function passes($attribute, $value)
    {
        // Combined regex to block HTML tags and special characters
        return preg_match('/^[a-zA-Z\s]+$/', $value);
    }

    public function message()
    {
        // Define a custom error message
        return 'The :attribute may only contain letters and spaces.';
    }

}
