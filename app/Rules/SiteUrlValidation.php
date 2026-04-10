<?php
namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
class SiteUrlValidation  implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Referer' => 'https://google.com',
                'Accept-Language' => 'en-US,en;q=0.9'
            ])->timeout(60)->get($value);
            if ($response->failed()) {
                $fail('The :attribute is not a valid url may be down or not exists');
            } 
        } catch (\Exception $e) {
            $fail('The :attribute is not a valid url may be down or not exists');
        }
    }

    public function passes($attribute, $value)
    {
        try {
            $response = Http::timeout(60)->get($value);
            if ($response->successful()) {
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function message()
    {
        // Define a custom error message
        return 'The :attribute is not a valid url may be down or not exists';
    }
}
