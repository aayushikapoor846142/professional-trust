<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|min:2|max:255|string_limit',
            'last_name' => 'required|min:2|max:255|string_limit',
            'country_code' => 'required',
            'phone_no' => 'string|max:15',
            'date_of_birth' => 'required',
            'gender' => 'required',
            'timezone' => 'required',
            'country_id' => 'nullable|integer',
            'state_id' => 'nullable|integer',
            'city_id' => 'nullable|integer',
            'address' => 'nullable|string|max:500',
            'zip_code' => 'nullable|string|max:20',
            'languages_known' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required',
            'first_name.min' => 'First name must be at least 2 characters',
            'first_name.max' => 'First name cannot exceed 255 characters',
            'last_name.required' => 'Last name is required',
            'last_name.min' => 'Last name must be at least 2 characters',
            'last_name.max' => 'Last name cannot exceed 255 characters',
            'country_code.required' => 'Country code is required',
            'phone_no.max' => 'Phone number cannot exceed 15 characters',
            'date_of_birth.required' => 'Date of birth is required',
            'gender.required' => 'Gender is required',
            'timezone.required' => 'Timezone is required',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'country_code' => 'country code',
            'phone_no' => 'phone number',
            'date_of_birth' => 'date of birth',
            'timezone' => 'timezone',
        ];
    }
} 