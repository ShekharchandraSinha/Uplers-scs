<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $method = $this->method();
        switch ($method) {
            case 'GET':
            case 'PUT':
            case 'DELETE':
                return [];

            case 'POST':
                return [
                    'first-name' => 'required',
                    'last-name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'mobile' => 'required|numeric|digits_between:10,13|unique:users,mobile',
                    'password' => 'required|min:6|confirmed',
                ];

            case 'PATCH':
                return [
                    'first-name' => 'required',
                    'last-name' => 'required',
                    'email' => 'required|email|unique:users,email,' . $this->id,
                    'mobile' => 'required|numeric|digits_between:10,13|unique:users,mobile,' . $this->id,
                    'password' => 'nullable|present|min:6|confirmed',
                ];

            default:
                return abort(405);
        }
    }
}
