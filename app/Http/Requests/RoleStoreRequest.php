<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class RoleStoreRequest extends FormRequest
{
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
        switch ($this->method()) {
            case 'PUT':
                $rules = [
                    'name' => 'required|string|between:2,100|unique:roles,name,' . $this->role->id,
                ];
                break;

            default:
                $rules = [
                    'name' => 'required|string|between:2,100|unique:roles'
                ];
                break;
        }
        return $rules;
    }

    public function attributes()
    {
        return [
            'name' => 'Nombre',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(
            response()->json(
                ['errors' => $errors],
                400
            )
        );
    }
}
