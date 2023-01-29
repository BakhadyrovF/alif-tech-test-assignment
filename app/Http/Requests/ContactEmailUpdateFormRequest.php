<?php

namespace App\Http\Requests;

use App\Models\ContactEmail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactEmailUpdateFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email' => ['required', 'string', 'min:10', 'max:255', Rule::unique(
                ContactEmail::class,
                'email'
            )->ignore($this->route('email'))]
        ];
    }
}
