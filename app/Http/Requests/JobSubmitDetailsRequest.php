<?php

namespace App\Http\Requests;

use App\Traits\ResponseTraits;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class JobSubmitDetailsRequest extends FormRequest
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
            'template_followed' => ['required'],
            'template_issue' => ['required'],
            'comments_template_issue' => ['required'],
            'auto_recommend' => ['required'],
            'comments_auto_recommend' => ['required'],
            'img_localstock' => ['required'],
            'img_customer' => ['required'],
            'img_num' => ['required'],
            'shared_folder_location' => ['required'],
            'dev_comments' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'template_followed.required' => 'Template Followed is required.',
            'template_issue.required' => 'Any Issue with Template is required.',
            'comments_template_issue.required' => 'Comments for Issue in Template is required.',
            'auto_recommend.required' => 'Automation Recommended is required.',
            'comments_auto_recommend.required' => 'Comments for Automation Recommendation is required.',
            'img_localstock.required' => 'Image(s) used from Localstock is required.',
            'img_customer.required' => 'Image(s) provided by Customer is required.',
            'img_num.required' => 'Num of new images used is required.',
            'shared_folder_location.required' => 'Shared Folder Location is required.',
            'dev_comments.required' => 'Developer Comments is required.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = $this->failedValidationResponse($validator->errors());
        throw new HttpResponseException(response()->json($response, 200));
    }
}
