<?php

namespace App\Http\Requests;

use App\Enums\ApiCodeNo;
use App\Libs\ApiBusUtil;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseApiRequest extends FormRequest
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
     * Validation error messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'required' => trans('validation.required'),
            'unique' => trans('validation.unique'),
            'exists' => trans('validation.exists'),
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     */
    public function failedValidation(Validator $validator)
    {
        $failedRules = $validator->failed();
        $isRequiredFailed = false;
        foreach ($failedRules as $rules) {
            if (array_key_exists('Required', $rules)) {
                $isRequiredFailed = true;
                break;
            }
        }

        $errorResponse = $isRequiredFailed
            ? ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::REQUIRED_PARAMETER, $validator->getMessageBag())
            : ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::VALIDATE_PARAMETER, $validator->getMessageBag());

        throw new HttpResponseException($errorResponse);
    }
}
