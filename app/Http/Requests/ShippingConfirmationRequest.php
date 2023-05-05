<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Response;
use Redirect;

class ShippingConfirmationRequest extends FormRequest
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
        'shipping_detail.required' => 'ยังไม่ได้ป้อนรายละเอียดการจัดส่ง'
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
        'shipping_detail' => 'required'
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
