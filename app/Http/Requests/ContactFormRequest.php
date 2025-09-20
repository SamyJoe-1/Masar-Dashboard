<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactFormRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|min:2',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max_digits:20',
            'subject' => 'required|string|in:general,technical,billing,partnership,other',
            'message' => 'required|string|min:10|max:2000',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.The name field is required.'),
            'name.min' => __('validation.The name must be at least 2 characters.'),
            'name.max' => __('validation.The name may not be greater than 255 characters.'),
            'email.required' => __('validation.The email field is required.'),
            'email.email' => __('validation.Please enter a valid email address.'),
            'phone.regex' => __('validation.Please enter a valid phone number.'),
            'subject.required' => __('validation.Please select a subject.'),
            'subject.in' => __('validation.Please select a valid subject.'),
            'message.required' => __('validation.The message field is required.'),
            'message.min' => __('validation.The message must be at least 10 characters.'),
            'message.max' => __('validation.The message may not be greater than 2000 characters.'),
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'name' => __('static_pages.Full Name'),
            'email' => __('static_pages.Email'),
            'phone' => __('static_pages.Phone Number'),
            'subject' => __('static_pages.Subject'),
            'message' => __('static_pages.Message'),
        ];
    }
}
