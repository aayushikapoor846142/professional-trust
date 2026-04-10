<?php
namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

use Illuminate\Contracts\Validation\Rule;

class PasswordValidation implements Rule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return preg_match('/[a-z]/', $value) &&      // At least one lowercase letter
               preg_match('/[A-Z]/', $value) &&      // At least one uppercase letter
               preg_match('/[0-9]/', $value) &&      // At least one digit
               preg_match('/[@$!%*#?&]/', $value) && // At least one special character
               strlen($value) >= 8;                  // Minimum 8 characters
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.';
    }
}
