<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuerrillaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'required|max:50|unique:guerrillas,username',
            'email' => 'required|email|max:100|unique:guerrillas,email',
            'faction' => [
                'required',
                Rule::in(['China', 'USMC', 'MEC']),
            ]
        ];
    }
}
