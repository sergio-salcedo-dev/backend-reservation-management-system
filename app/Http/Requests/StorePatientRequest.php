<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:patients,email',
            'phone' => 'required|string|max:15',
            'dni' => 'required|string|max:9|unique:patients,dni',
        ];
    }

    /** Custom message for validation */
    public function messages(): array
    {
        return [
            //
        ];
    }
}
