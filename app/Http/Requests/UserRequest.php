<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
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

    public function rules()
    {
        switch ($this->method()) {
            case 'PUT':
                $rules = [
                    'name' => 'required|string|between:2,100',
                    'email' => 'required|string|email|max:100|unique:users,email,' . $this->user->id,
                    'dni' => 'required|string|min:10|max:13|unique:users,dni,' . $this->user->id,
                    'phone' => 'required|string',
                    'birthday' => 'required|string'
                ];
                break;

            default:
                $rules = [
                    'clinic_id' => 'required|exists:clinics,id',
                    'name' => 'required|string|between:2,100',
                    'surname' => 'required|string|between:2,100',
                    'dni' => 'required|string|between:2,100|unique:users,dni',
                    'email' => 'required|string|email|max:100|unique:users,email',
                    'phone' => 'nullable|string',
                    'birthday' => 'nullable|string'
                ];
                break;
        }
        return $rules;
    }

    public function attributes()
    {
        return [
            'name' => 'Nombre Completo',
            'email' => 'Correo',
            'phone' => 'Telefono',
            'birthday' => 'Fecha de Nacimiento'
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
