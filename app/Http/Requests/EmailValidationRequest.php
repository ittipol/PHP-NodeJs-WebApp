<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailValidationRequest extends FormRequest
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
        'email.required' => 'โปรดป้อนอีเมลของคุณเพื่อร้องขอการรีเซ็ตรหัสผ่าน',
        'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
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
            'email' => 'required|email',
        ];
    }
}
