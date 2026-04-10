<?php
namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class InputSanitizeValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (
            !preg_match('/<[^>]*>/', $value)                     // Minimum 8 characters
        ) {
            $fail('The :attribute must have proper value unwated special characters not allowed');
        }
    }

    public function passes($attribute, $value)
    {
        // Combined regex to block HTML tags and special characters
        return !preg_match('/<[^>]*>/', $value);
    }

    public function message()
    {
        // Define a custom error message
       return 'The :attribute field must not contain HTML or script tags.';
    }

}
