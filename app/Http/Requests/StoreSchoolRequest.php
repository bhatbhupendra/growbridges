<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['admin', 'agent', 'student']);
    }

    public function rules(): array
    {
        return [
            'school_name' => ['required', 'string', 'max:255'],
        ];
    }
}