<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust based on your authorization logic
    }

    public function rules()
    {
        return [
            'email' => ['required', 'string', function ($attribute, $value, $fail) {
                // Check if the value is a valid email address
                if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return; // Valid email, no additional validation needed
                }

                // Check if the value is a valid employee_user_id
                if (!User::where('employee_user_id', $value)->exists()) {
                    $fail('The input is not a valid email or employee user ID.');
                }
            }],
            'password' => 'required|string',
        ];
    }
}
