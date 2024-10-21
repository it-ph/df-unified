<?php

namespace App\Http\Requests;

use App\Traits\ResponseTraits;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AuditLogStoreRequest extends FormRequest
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
            'qc_status' => ['required'],
            'for_rework' => ['required'],
            'num_times' => ['required'],
            'alignment_aesthetics' => ['required'],
            'c_alignment_aesthetics' => ['required'],
            'availability_formats' => ['required'],
            'c_availability_formats' => ['required'],
            'accuracy' => ['required'],
            'c_accuracy' => ['required'],
            'functionality' => ['required'],
            'c_functionality' => ['required'],
            'qc_comments' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'qc_status.required' => 'QC Status is required.',
            'for_rework.required' => 'Call for Rework is required.',
            'num_times.required' => 'Num of Times is required.',
            'alignment_aesthetics.required' => 'Alignment & Aesthetics is required.',
            'c_alignment_aesthetics.required' => 'Comments for Alignment & Aesthetics is required.',
            'availability_formats.required' => 'Availability and Formats is required.',
            'c_availability_formats.required' => 'Comments for Availability and Formats is required.',
            'accuracy.required' => 'Accuracy is required.',
            'c_accuracy.required' => 'Comments for Accuracy is required.',
            'functionality.required' => 'Functionality is required.',
            'c_functionality.required' => 'Comments for Functionality is required.',
            'qc_comments.required' => 'QC Comments is required.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = $this->failedValidationResponse($validator->errors());
        throw new HttpResponseException(response()->json($response, 200));
    }
}
