<?php

namespace App\Http\Requests;

use App\Traits\ResponseTraits;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ClientStoreRequest extends FormRequest
{
    use ResponseTraits;

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
            'name' => ['required',Rule::unique('clients')->ignore($this->edit_id)],
            'start' => ['required'],
            'end' => ['required'],
            'sla_threshold' => ['required'],
            'sla_threshold_to' => ['required'],
            'sla_threshold_cc' => ['required'],
            'sla_missed_to' => ['required'],
            'sla_missed_cc' => ['required'],
            'new_job_cc' => ['required'],
            'qc_send_cc' => ['required'],
            'daily_report_to' => ['required'],
            'daily_report_cc' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Client is required.',
            'name.unique' => 'Client is already exists.',
            'start.required' => 'Start of Shift is required.',
            'end.required' => 'End of Shift is required.',
            'sla_threshold.required' => 'SLA Threshold is required.',
            'sla_threshold_to.required' => 'SLA Threshold Cross Email Recipients (TO) is required.',
            'sla_threshold_cc.required' => 'SLA Threshold Cross Email Recipients (CC) is required.',
            'sla_missed_to.required' => 'SLA Missed Email Recipients (TO) is required.',
            'sla_missed_cc.required' => 'SLA Missed Email Recipients (CC) is required.',
            'new_job_cc.required' => 'New Job Email Recipients (CC) is required.',
            'qc_send_cc.required' => 'SLA Missed Email Recipients (CC) is required.',
            'daily_report_to.required' => 'Daily Report Recipients (TO) is required.',
            'daily_report_cc.required' => 'Daily Report Recipients (CC) is required.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = $this->failedValidationResponse($validator->errors());
        throw new HttpResponseException(response()->json($response, 200));
    }
}
