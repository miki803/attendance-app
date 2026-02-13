<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceRequest extends FormRequest
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
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time'   => ['nullable', 'date_format:H:i'],
        ];
    }
    public function messages()
    {
        return [
            'end_time.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'start_time.date_format' => '出勤時間もしくは退勤時間が不適切な値です',
            'end_time.date_format' => '出勤時間もしくは退勤時間が不適切な値です',
        ];
    }
}
