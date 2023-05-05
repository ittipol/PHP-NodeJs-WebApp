<!doctype html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  @include('script.script')

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

  <title>404 — ไม่พบหน้าที่คุณกกำลังมองหา</title>
</head>
<body>

  <div class="c-404 tc mt6 mt7-ns">    
    <h1 class="oops">404</h1>
    <p class="info">ไม่พบหน้าที่คุณกกำลังมองหา</p>
    <br>
    <a href="/" class="c-404-btn"><i class="fa fa-angle-left"></i>ไปยังหน้าแรก</a>
    <br>
  </div>

  @if(Session::has('message.title'))
  <script type="text/javascript">
      const snackbar = new Snackbar();
      snackbar.setTitle('{{ Session::get("message.title") }}');
      snackbar.display();
  </script>
  @endif

</body>
</html>