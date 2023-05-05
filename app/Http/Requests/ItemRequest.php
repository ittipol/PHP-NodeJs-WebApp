<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Response;
use Redirect;

class ItemRequest extends FormRequest
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
        'title.required' => 'ยังไม่ได้ป้อนหัวข้อ / ชื่อสินค้า',
        'title.max' => 'จำนวนตัวอักษรเกินกว่าที่กำหนด',
        'description.required' => 'ยังไม่ได้ป้อนรายละเอียด',
        'price.required' => 'ยังไม่ได้ป้อนราคาขาย',
        'price.regex' => 'ราคาขายไม่ถูกต้อง',
        'original_price.regex' => 'ราคาเดิมไม่ถูกต้อง',
        'contact.required' => 'ยังไม่ได้ป้อนช่องทางการติดต่อผู้ขาย',
        'ItemToCategory.category_id.required' => 'ยังไม่ได้เลือกหมวดหมู่ของสินค้า',
        'ItemToCategory.category_id.numeric' => 'ข้อมูลไม่ถูกต้อง',
        'ItemToLocation.location_id.required' => 'ยังไม่ได้ระบุตำแหน่งสินค้า',
        'ItemToLocation.location_id.numeric' => 'ข้อมูลไม่ถูกต้อง',
        // 'publishing_type.required' => 'ยังไม่ได้ระบุรูปแบบสินค้า',
        // 'publishing_type.numeric' => 'ยังไม่ได้ระบุรูปแบบสินค้า',
        // 'grading.required' => 'ยังไม่ได้ระบุการขาย',
        // 'grading.numeric' => 'ยังไม่ได้ระบุการขาย',
        // 'Preview.filename.required' => 'ยังไม่ได้เพิ่มรูปภาพ Preview'
        // 'theme_color_id.required' => 'ยังไม่ได้ระบุธีม',
        // 'theme_color_id.numeric' => 'ยังไม่ได้ระบุธีม',
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
        'title' => 'required|max:255',
        'description' => 'required',
        'price' => 'required|regex:/^[0-9]{1,3}(?:,?[0-9]{3})*(?:\.[0-9]{2})?$/',
        'original_price' => 'nullable|regex:/^[0-9]{1,3}(?:,?[0-9]{3})*(?:\.[0-9]{2})?$/',
        // 'contact' => 'required',
        'ItemToCategory.category_id' => 'required|numeric',
        // 'ItemToLocation.location_id' => 'required|numeric',
        // 'publishing_type' => 'required|numeric',
        // 'grading' => 'required|numeric',
        // 'Preview.filename' => 'required'
        // 'theme_color_id' => 'required|numeric',
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
