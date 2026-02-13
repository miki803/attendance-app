<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

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
            'attendance_id' => ['nullable', 'integer', 'exists:attendances,id'],
            'date' => ['required', 'date'],
            // 出勤・退勤
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time'   => ['nullable', 'date_format:H:i'],
            'remark' => ['required', 'string'],
            // 休憩
            'requested_breaks' => ['nullable', 'array'],
            'requested_breaks.*.start' => ['nullable', 'date_format:H:i'],
            'requested_breaks.*.end'   => ['nullable', 'date_format:H:i'],
        ];
    }
    public function messages()
    {
        return [
            'attendance_id.required' => '勤怠IDが正しく送信されていません',
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

            // 出勤・退勤の前後チェック
            if ($start && $end && $start >= $end) {
                $validator->errors()->add(
                    'start_time',
                    '出勤時間もしくは退勤時間が不適切な値です'
                );
            }

            foreach ($this->requested_breaks ?? [] as $break) {
                if (!$break['start'] || !$break['end']) continue;
                // 出勤退勤が両方あるときだけチェック
                if ($start && $end) {
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

            }
        });
    }

    protected function prepareForValidation()
    {
        $breaks = $this->requested_breaks;

        if ($breaks) {
            foreach ($breaks as $i => $break) {
                $breaks[$i]['start'] = $break['start'] ?: null;
                $breaks[$i]['end']   = $break['end'] ?: null;
            }
        }

        $this->merge([
            'requested_breaks' => $breaks
        ]);
    }

}
