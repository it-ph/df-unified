<?php

namespace App\Http\Requests;

use App\Traits\ResponseTraits;
use Facades\App\Http\Helpers\CredentialsHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class JobStoreRequest extends FormRequest
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
        $user = CredentialsHelper::get_set_credentials();
        return [
            'client_id' => in_array('admin', $user['roles']) ? ['required'] : 'nullable',
            'name' => ['required'],
            'site_id' => ['required'],
            'platform' => ['required'],
            'developer_id' => ['required'],
            'request_type_id' => ['required'],
            'request_volume_id' => ['required'],
            // 'request_sla_id' => ['required'],
            'agreed_sla' => ['required'],
            'salesforce_link' => ['required'],
            'special_request' => ['required'],
            'comments_special_request' => ['required'],
            'addon_comments' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'client_id.required' => 'Client is required.',
            'name.required' => 'Job Name is required.',
            'site_id.required' => 'Site ID is required.',
            'platform.required' => 'Platform is required.',
            'developer_id.required' => 'Developer is required.',
            'request_type_id.required' => 'Type of Request is required.',
            'request_volume_id.required' => 'Num Pages is required.',
            // 'request_sla_id.required' => 'Request SLA is required.',
            'agreed_sla.required' => 'Agreed SLA is required. Request SLA not found.',
            'salesforce_link.required' => 'Salesforce Link is required.',
            'special_request.required' => 'Special Request is required.',
            'comments_special_request.required' => 'Comments for Special Request is required.',
            'addon_comments.required' => 'Additional Comments is required.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = $this->failedValidationResponse($validator->errors());
        throw new HttpResponseException(response()->json($response, 200));
    }
}
