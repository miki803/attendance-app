<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BreakRequest extends FormRequest
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
            'breaks.*.start' => ['nullable', 'date_format:H:i'],
            'breaks.*.end'   => ['nullable', 'date_format:H:i', 'after:breaks.*.start'],
        ];
    }
    public function messages()
    {
        return [
            'breaks.*.start.date_format' => '休憩時間が不適切な値です',
            'breaks.*.end.date_format'   => '休憩時間が不適切な値です',
            'breaks.*.end.after'         => '休憩時間が不適切な値です',
        ];
    }
}
