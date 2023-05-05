<!doctype html>
<html>
<head>
  <!-- Meta data -->
  @include('script.meta')

  @include('script.script')

  <script type="text/javascript">

    // var _socket = io('{{env('SOCKET_URL')}}');
    // _socket.on('connect', function(){
    //   console.log('_connect');
    // });
    // _socket.on('event', function(data){
    //   console.log('_event');
    // });
    // _socket.on('disconnect', function(){
    //   console.log('_disconnect');
    // });

    const _io = new IO(io('{{env('SOCKET_URL')}}'));

    $(document).ready(function(){
      @if(Auth::check())
        const _user = new User({{Auth::user()->id}},'{{Auth::user()->user_key}}');
        _user.init();
      @endif

    });

  </script>

 </head>
<body>

	@include('script.facebook')

  @include('shared.header-secondary')

	@yield('content')

  <!-- ############ -->
  <div id="global_overlay" class="global-overlay"></div>
  <div id="global_loading_indicator" class="global-loading-indicator"></div>
  <!-- ############ -->

  <script type="text/javascript">

    // new gnMenu(document.getElementById('gn-menu'));

    @if(Session::has('message.title'))
      const snackbar = new Snackbar();
      snackbar.setTitle('{!!Session::get("message.title")!!}');
      snackbar.display();
    @endif

    @if(Session::has('modal.title'))

      const _modal = new ModalDialog();
      _modal.init();

      _modal.show(_modal.create('{!!Session::get("modal.title")!!}','{!!Session::get("modal.message")!!}','{!!Session::get("modal.type")!!}'));
    @endif

  </script>

</body>
</html>