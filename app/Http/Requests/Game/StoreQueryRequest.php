<?php

namespace App\Http\Requests\Game;

use Illuminate\Foundation\Http\FormRequest;

class StoreQueryRequest extends FormRequest
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
            'sender_id' => ['required', 'not_in:0'],
            'sender_fleet' => ['required', 'not_in:0'],
            'user_id' => ['required']
        ];
    }
}
