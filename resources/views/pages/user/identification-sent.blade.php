@extends('shared.main')
@section('content')

<div class="container mv4 mv7-ns">

  <div class="message-panel tc">

    <h1>ส่งคำร้องขอไปยังอีเมลของคุณแล้ว</h1>
    
    <div class="center w-90 w-100-ns">
      <h5>คุณควรได้รับอีเมลพร้อมรายละเอียดเพิ่มเติมเร็วๆนี้ หากยังไม่ถึงภายในไม่กี่นาทีให้ตรวจสอบโฟลเดอร์สแปมของคุณ</h5>
    </div>

    <a href="{{URL::to('login')}}" class="btn mt4">
      <i class="fa fa-sign-in"></i> เข้าสู่ระบบ
    </a>

  </div>

</div>

@stop