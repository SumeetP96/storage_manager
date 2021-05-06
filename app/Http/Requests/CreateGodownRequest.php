<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateGodownRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'name'      => 'required|max:255|unique:godowns,name',
            'alias'     => 'nullable|max:10',
            'contact_1' => 'nullable|different:contact_2|regex:/^[0-9]{6,10}/u',
            'contact_2' => 'nullable|different:contact_1|regex:/^[0-9]{6,10}/u',
            'email'     => 'nullable|email',
        ];
    }

    public function messages()
    {
        return [
            'alias.max'         => 'Not more than 10 letters.',
            'contact_1.regex'   => 'Invalid contact number.',
            'contact_2.regex'   => 'Invalid contact number.',
        ];
    }
}
