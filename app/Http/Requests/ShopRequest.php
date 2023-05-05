<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Response;
use Redirect;

class ShopRequest extends FormRequest
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
        'name.required' => 'ยังไม่ได้ป้อนหัวข้อ / ชื่อสินค้า',
        'name.max' => 'จำนวนตัวอักษรเกินกว่าที่กำหนด',
        'contact.required' => 'ยังไม่ได้ป้อนรายละเอียด',
        'ShopToCategory.category_id.required' => 'ยังไม่ได้เลือกประเภทสินค้าที่ขายในร้านขายสินค้า'
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
        'contact' => 'required',
        'ShopToCategory.category_id' => 'required'
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
