<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ReedemPointsRequest extends FormRequest
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
    public function rules()
    {
        return [
            'amount' => 'required|integer|min:1|max:500'
        ];
    }

    public function messages()
    {
        return [
            'amount.required' => 'The amount is required.',
            'amount.integer'  => 'The amount must be an integer.',
            'amount.min'      => 'The amount must be at least 1.',
            'amount.max'      => 'The amount must not exceed 500.',
        ];
    }

}
