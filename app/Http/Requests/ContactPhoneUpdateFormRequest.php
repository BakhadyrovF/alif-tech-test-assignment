<?php

namespace App\Http\Requests;

use App\Models\ContactPhone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactPhoneUpdateFormRequest extends FormRequest
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
            'phoneNumber' => ['required', 'string', 'starts_with:+', 'max:50', Rule::unique(
                ContactPhone::class,
                'phone_number'
            )->ignore($this->route('phone'))]
        ];
    }
}
