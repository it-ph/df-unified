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
            'account_no' => ['required'],
            'account_name' => ['required'],
            'site_id' => ['required'],
            'platform' => ['required'],
            'developer_id' => ['required'],
            'request_type_id' => ['required'],
            'request_volume_id' => ['required'],
            'agreed_sla' => ['required'],
            'addon_comments' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'client_id.required' => 'Client is required.',
            'account_no.required' => 'Account No. is required.',
            'account_name.required' => 'Account Name is required.',
            'site_id.required' => 'Site ID is required.',
            'platform.required' => 'Platform is required.',
            'developer_id.required' => 'Developer is required.',
            'request_type_id.required' => 'Type of Request is required.',
            'request_volume_id.required' => 'Num Pages is required.',
            'agreed_sla.required' => 'Agreed SLA is required. Request SLA not found.',
            'addon_comments.required' => 'Additional Comments is required.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = $this->failedValidationResponse($validator->errors());
        throw new HttpResponseException(response()->json($response, 200));
    }
}
