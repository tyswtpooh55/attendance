<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ChangeRequestForm extends FormRequest
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
            'employee_id' => ['required', 'exists:employees,id'],
            'date' => ['required', 'date'],
            'work_id' => ['nullable', 'exists:works,id'],
            'work_in' => ['nullable', 'date_format:H:i'],
            'work_out' => ['nullable', 'date_format:H:i', 'after:work_in'],
            'breaking_in' => ['nullable', 'array'],
            'breaking_out' => ['nullable', 'array'],
            'reason' => ['required', 'string'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function($validator) {
            if ($this->input('delete-work')) {
                return;
            }

            $workIn = $this->input('work_in') ?Carbon::createFromFormat('H:i', $this->input('work_in')) : null;
            $workOut = $this->input('work_out') ? Carbon::createFromFormat('H:i', $this->input('work_out')) : null;

            foreach ($this->input('breaking_in', []) as $index => $breakingIn) {
                $breakingInTime = $breakingIn ? Carbon::createFromFormat('H:i', $breakingIn) : null;
                $breakingOutTime = $this->input('breaking_out')[$index] ? Carbon::createFromFormat('H:i', $this->input('breaking_out')[$index]) : null;

                if ($breakingInTime) {
                    if ($workIn && $breakingInTime->lte($workIn)) {
                        $validator->errors()->add('breaking_in' . $index, '休憩は勤務時間内に設定してください');
                    }
                }

                if ($breakingOutTime) {
                    if ($workOut && $breakingOutTime->gt($workOut)) {
                        $validator->errors()->add('breaking_out' . $index, '休憩は勤務時間内に設定してください');
                    }

                    if ($breakingOutTime->lte($breakingInTime)) {
                        $validator->errors()->add('breaking_out' . $index, '休憩終了は休憩開始より後に設定してください');
                    }
                }
            }
        });
    }

    public function messages()
    {
        return [
            'work_out.after' => '勤務終了は勤務開始より後に設定してください',
            'reason.required' => '変更理由を選択してください',
        ];
    }
}
