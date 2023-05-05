<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Response;
use Redirect;

class CheckoutRequest extends FormRequest
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
        'buyer_name.required' => 'ยังไม่ได้ป้อนชื่อผู้ซื้อ',
        'shipping_address.required' => 'ยังไม่ได้ป้อนที่อยู่สำหรับจัดส่ง',
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
        'buyer_name' => 'required',
        'shipping_address' => 'required',
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
