<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()?->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'start_time' => ['required', 'date_format:H:i'],
            'end_time'   => ['required', 'date_format:H:i', 'after:start_time'],
            'breaks.*.start' => ['nullable', 'date_format:H:i'],
            'breaks.*.end'   => ['nullable', 'date_format:H:i', 'after:breaks.*.start'],
            'remark' => ['required', 'string'],
        ];
    }
    public function messages()
    {
        return [
            'end_time.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'breaks.*.end.after' => '休憩時間が不適切な値です',
            'remark.required' => '備考を記入してください',
        ];
    }
}
