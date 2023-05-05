<!doctype html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  @include('script.script')

  <script type="text/javascript">

    const _io = new IO(io('{{env('SOCKET_URL')}}'));

    $(document).ready(function(){
      @if(Auth::check())
        const _user = new User({{Auth::user()->id}},'{{Auth::user()->user_key}}');
        _user.init();
      @endif
    });

  </script>

  <style type="text/css">

    .c-404 {
      position: relative;
      padding: 20px;
    }
    .c-404 .oops {
      color: #4B4F4B;
      font-size: 8em;
      letter-spacing: 0.05em;
      margin-top: 30px;
      margin-bottom: 20px;
    }
    .c-404 .info {
      color: #4B4F4B;
      padding: 5px;
      font-size: 1.25rem;
    }
    .c-404 .c-404-btn {
      background: #FF5659;
      color: #fff;
      text-transform: uppercase;
      padding: 10px 40px;
      border-radius: 50px;
      text-decoration: none;
    }
    .c-404 .c-404-btn:hover {
      background: #FF2629;
    }
    .c-404 .c-404-btn .fa-angle-left {
      font-size: 1.2em;
      margin-right: 15px;
    }
    .c-404 img {
      padding: 10px;
    }
</style>

  <title>เกิดข้อผิดพลาด | Ticket Easys</title>
</head>
<body>

  @include('shared.header')

  @include('shared.modal-user-menu')

  <div class="c-404 tc mt5">

     <div class="mv7">
       <div class="message-panel tc">
         <div class="center w-90 w-100-ns">
           <h4 class="f3 f2-ns">{{$message}}</h4>
         </div>
         <br>
         <a href="/" class="c-404-btn"><i class="fa fa-angle-left"></i>ไปยังหน้าแรก</a>
       </div>
     </div>

  </div>

  @if(Session::has('message.title'))
  <script type="text/javascript">
      const snackbar = new Snackbar();
      snackbar.setTitle('{{ Session::get("message.title") }}');
      snackbar.display();
  </script>
  @endif

  <script type="text/javascript">

    const _modal = new ModalDialog();
    _modal.init();

    new gnMenu(document.getElementById('gn-menu'));
  </script>

</body>
</html>