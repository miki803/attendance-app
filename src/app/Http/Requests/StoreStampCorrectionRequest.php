<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreStampCorrectionRequest extends FormRequest
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
            'attendance_id' => ['required', 'integer', 'exists:attendances,id'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'remark' => ['required', 'string'],
            'requested_breaks' => ['array'],
            'requested_breaks.*.start' => ['nullable', 'date_format:H:i'],
            'requested_breaks.*.end' => ['nullable', 'date_format:H:i'],
        ];
    }
    public function messages()
    {
        return [
            // 出勤・退勤
            'start_time.required' => '出勤時間もしくは退勤時間が不適切な値です',
            'end_time.required' => '出勤時間もしくは退勤時間が不適切な値です',
            'end_time.after' => '出勤時間もしくは退勤時間が不適切な値です',

            // 休憩
            'requested_breaks.*.start.date_format' =>'休憩時間が不適切な値です',
            'requested_breaks.*.end.date_format' =>'休憩時間もしくは退勤時間が不適切な値です',

            // 備考
            'remark.required' => '備考を記入してください',
        ];
    }
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $start = $this->start_time;
            $end   = $this->end_time;

            foreach ($this->requested_breaks ?? [] as $break) {
                if (!$break['start'] || !$break['end']) continue;
                if ($break['start'] < $start || $break['start'] > $end) {
                    $validator->errors()->add(
                        'requested_breaks',
                        '休憩時間が不適切な値です'
                    );
                }
                if ($break['end'] > $end){
                    $validator->errors()->add(
                        'requested_breaks',
                        '休憩時間もしくは退勤時間が不適切な値です'
                    );
                }
            }
        });
    }
}
