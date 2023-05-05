@extends('shared.main')
@section('content')

<div class="container mv4 mv7-ns">

  <div class="message-panel tc">

    <h1>การยืนยันบัญชีของคุณเรียบร้อยแล้ว</h1>
    
    <div class="center w-90 w-100-ns">
      <h5>บัญชีของคุณสามารถใช้งานได้แล้ว</h5>
    </div>

    <a href="{{URL::to('login')}}" class="btn mt4">
      <i class="fa fa-sign-in"></i> เข้าสู่ระบบ
    </a>

  </div>

</div>

@stop