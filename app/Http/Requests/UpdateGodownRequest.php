<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGodownRequest extends FormRequest
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
            'name'      => 'required|max:255|unique:godowns,name,' . request()->id,
            'alias'     => 'nullable|max:10',
            'contact_1' => 'nullable|different:contact_2|max:10|regex:/^[0-9]/u',
            'contact_2' => 'nullable|different:contact_1|max:10|regex:/^[0-9]/u',
            'email'     => 'nullable|email',
        ];
    }

    public function messages()
    {
        return [
            'alias.max'         => 'Not more than 10 letters.',
            'contact_1.max'     => 'Not more than 10 digits.',
            'contact_2.max'     => 'Not more than 10 digits.',
            'contact_1.regex'   => 'Invalid contact number.',
            'contact_2.regex'   => 'Invalid contact number.',
        ];
    }
}
