<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class AdminAttendanceRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()?->is_admin;
    }

    public function rules()
    {
        return [
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time'   => ['nullable', 'date_format:H:i', 'after:start_time'],

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

    /**
     * 追加の業務ロジックチェック
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $startTime = $this->start_time;
            $endTime   = $this->end_time;
            $breaks    = $this->breaks ?? [];

            // 出勤・退勤が両方ある場合のみチェック
            if ($startTime && $endTime) {

                $start = Carbon::createFromFormat('H:i', $startTime);
                $end   = Carbon::createFromFormat('H:i', $endTime);

                foreach ($breaks as $break) {

                    if (!empty($break['start'])) {
                        $breakStart = Carbon::createFromFormat('H:i', $break['start']);

                        // 休憩開始 < 出勤
                        if ($breakStart->lt($start)) {
                            $validator->errors()->add(
                                'breaks',
                                '休憩時間もしくは退勤時間が不適切な値です'
                            );
                            break;
                        }
                    }

                    if (!empty($break['end'])) {
                        $breakEnd = Carbon::createFromFormat('H:i', $break['end']);

                        // 休憩終了 > 退勤
                        if ($breakEnd->gt($end)) {
                            $validator->errors()->add(
                                'breaks',
                                '休憩時間もしくは退勤時間が不適切な値です'
                            );
                            break;
                        }
                    }
                }
            }
        });
    }
}
