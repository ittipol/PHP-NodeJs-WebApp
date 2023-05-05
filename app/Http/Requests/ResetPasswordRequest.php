<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
          'password' => 'required|min:4|max:255|confirmed',
        ];
    }
}
