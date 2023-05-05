@extends('shared.main')
@section('content')

<div class="container mv4 mv7-ns">

  <div class="message-panel tc">

    <h1>การสร้างบัญชีของคุณเรียบร้อยแล้ว</h1>
    
    <div class="center w-90 w-100-ns">
      <h5>เราได้ส่งรายละเอียดการยันบัญชีไปยังอีเมลของคุณแล้ว โปรดยืนยันบัญชีของคุณเพื่อยืนยันว่านี่เป็นบัญชีที่ถูกต้อง</h5>
      <h5>
        <small>คุณควรได้รับอีเมลพร้อมรายละเอียดเพิ่มเติมเร็วๆนี้ หากยังไม่ถึงภายในไม่กี่นาทีให้ตรวจสอบโฟลเดอร์สแปมของคุณ</small>
      </h5>
    </div>

    <a href="{{URL::to('login')}}" class="btn c-btn c-btn-bg mt4">
      <i class="fa fa-sign-in"></i> เข้าสู่ระบบ
    </a>

  </div>

</div>

@stop