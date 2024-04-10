<?php

namespace App\Http\Requests\Game;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRequest extends FormRequest
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
        $method = $this->method();
        if($method == 'PUT'){
            return [
                'name' => 'string|unique:users',
                'email' => 'string|email|unique:users',
                'password' => 'string|min:8'
            ];
        }else{
            return [
                'name' => 'string|unique:users',
                'email' => 'string|email|unique:users',
                'password' => 'string|min:8'
            ];
        }
    } 

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([

            'success' => false,

            'message' => 'Validation errors',

            'data' => $validator->errors()

        ]));

    }

    public function messages()
    {
        return [
            'name.unique' => 'This username is already used.',
            'email.unique' => 'This email is already used.'
        ];

    }
}
