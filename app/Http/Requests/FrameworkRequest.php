<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class FrameworkRequest extends FormRequest
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
                    'icon' => 'required|image|mimes:jpeg,jpg,png|max:2048',
                    'title' => 'required',
                ];

            case 'PATCH':
                return [
                    'icon' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
                    'title' => 'required',
                ];

            default:
                return abort(405);
        }
    }
}
