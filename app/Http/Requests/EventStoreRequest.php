<?php

namespace App\Http\Requests;

use App\Traits\ResponseTraits;
use Facades\App\Http\Helpers\CredentialsHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class EventStoreRequest extends FormRequest
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
            'title' => ['required'],
            'description' => ['required'],
            'event_type' => ['required'],
            // 'start' => ['required', 'before_or_equal:end'],
            // 'end' => ['required'],
            'color' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'client_id.required' => 'Client is required.',
            'title.required' => 'Event Title is required.',
            'description.required' => 'Event Description is required.',
            'event_type.required' => 'Event Type is required.',
            // 'start.required' => 'Start Day is required.',
            // 'end.required' => 'End Day is required.',
            'color.required' => 'Color is required.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = $this->failedValidationResponse($validator->errors());
        throw new HttpResponseException(response()->json($response, 200));
    }
}
