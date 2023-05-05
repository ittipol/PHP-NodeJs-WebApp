<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Response;
use Redirect;

class CoinExchangeRequest extends FormRequest
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
        'amount.required' => 'ยังไม่ได้ป้อนจำนวนเงิน',
        'amount.numeric' => 'จำนวนเงินไม่ถูกต้อง',
        // 'description.required' => 'ยังไม่ได้ป้อนข้อความ',
        'method.required' => 'ยังไม่ได้ป้อนวิธีการโอน',
        'method.numeric' => 'วิธีการโอนไม่ถูกต้อง',
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
        'amount' => 'required|numeric',
        // 'description' => 'required',
        'method' => 'required|numeric',
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
