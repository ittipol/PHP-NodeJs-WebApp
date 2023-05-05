<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Response;
use Redirect;

class RegisterRequest extends FormRequest
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
        'name.required' => 'กรุณากรอกชื่อ นามสกุล',
        'email.required' => 'กรุณากรอกอีเมล',
        'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
        'email.unique' => 'อีเมลถูกใช้งานแล้ว',
        'password.required' => 'กรุณากรอกรหัสผ่าน',
        'password.min' => 'รัสผ่านต้องมีอย่างน้อย 4 อักขระ',
        'password.confirmed' => 'รหัสผ่านไม่ตรงกัน',
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
        'name' => 'required|max:255',
        'email' => 'required|email|unique:users,email|max:255',
        'password' => 'required|min:4|max:255|confirmed',
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
