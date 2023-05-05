<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Redirect;

class ProfileEditRequest extends FormRequest
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

    public function messages()
    {
      return [
        'name.required' => 'ชื่อ นามสกุลห้ามว่าง',
        'name.max' => 'จำนวนตัวอักษรเกินกว่าที่กำหนด',
      ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
      return [
        'name' => 'required|max:255'
      ];
    }

    public function forbiddenResponse()
    {
      return Response::make('Permission Denied!', 403);
    }

    public function response(array $errors) {
      return Redirect::back()->withErrors($errors)->withInput();
    }
}
