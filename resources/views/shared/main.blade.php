<!doctype html>
<html>
<head>
  <!-- Meta data -->
  @include('script.meta')

  @include('script.tracking')

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
        const _user = new User();
        _user.setUser({{Auth::user()->id}});
        _user.setKey('{{Auth::user()->user_key}}');
        _user.init();
      @endif
    });

  </script>

 </head>
<body>

	@include('script.facebook')

  @include('shared.header')

	@yield('content')

  @include('shared.main-menu')

  <!-- ############ -->
  <div id="global_overlay" class="global-overlay"></div>
  <div id="global_loading_indicator" class="global-loading-indicator"></div>
  <!-- ############ -->

  <div id="chat_box_panel"></div>
  
  @if(Session::has('popup-feature'))
  
    @include('shared.modal.feature')

    <script type="text/javascript">
      Loading.show();

      $(document).ready(function(){
        modalFeature.clear();
        modalFeature.show();

        setTimeout(function(){
          Loading.hide();
          modalFeature.page(1);
        },500);

      });
    </script>
  @endif

  <script type="text/javascript">
    // if ("geolocation" in navigator) {
    //   /* geolocation is available */

    //   navigator.geolocation.getCurrentPosition(function(position) {

    //     var latitude  = position.coords.latitude;
    //         var longitude = position.coords.longitude;

    //         // output.innerHTML = '<p>Latitude is ' + latitude + '° <br>Longitude is ' + longitude + '°</p>';

    //         var img = new Image();
    //         // img.src = "https://maps.googleapis.com/maps/api/staticmap?center=" + latitude + "," + longitude + "&zoom=13&size=300x300&sensor=false";
    //         img.src = "https://maps.googleapis.com/maps/api/staticmap?center=" + latitude + "," + longitude + "&zoom=14&size=1024x300";
      
    //         // console.log(img);

    //         $('body').append(img);

    //         let geolocation = latitude+','+longitude;
    //         let request = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='+geolocation+'&sensor=false&key=AIzaSyAUdwm6VWiEE-1ZrVgY3bh1sNX21_deHZw';
    //         console.log(request);
    //   });
    // } else {
    //   /* geolocation IS NOT available */
    // }
  </script>

  <script type="text/javascript">
    new gnMenu(document.getElementById('gn-menu'));

    const snackbar = new Snackbar();
    snackbar.init();
    
    const _modal = new ModalDialog();
    _modal.init();

    const _cart = new Cart('{{ csrf_token() }}');
    _cart.init();

    const chatBox = new ChatBox('{{ csrf_token() }}');
    chatBox.init();

    const _userOnline = new UserOnline();
    _userOnline.init();

    $('.lazy').lazy({
      effect: "fadeIn",
      effectTime: 220,
      threshold: 0
    });

    $(".nano").nanoScroller();

    @if(Session::has('message.title'))
      snackbar.setTitle('{!!Session::get("message.title")!!}');
      snackbar.display();
    @endif

    @if(Session::has('modal.title'))
      _modal.show(_modal.create('{!!Session::get("modal.title")!!}','{!!Session::get("modal.message")!!}','{!!Session::get("modal.type")!!}'));
    @endif

    @if(Session::has('toast-notification.title'))
      const toastNotification = new ToastNotification();
      toastNotification.init();

      toastNotification.setTitle('{!! Session::get("toast-notification.title") !!}');

      @if(Session::has('toast-notification.options'))
        @foreach(Session::get('toast-notification.options') as $_key => $_option)
          toastNotification.set('{{ $_key }}','{!! $_option !!}'); 
        @endforeach
      @endif

      toastNotification.show();
    @endif

  </script>

  <script type="application/ld+json">
  {
    "@context": "http://schema.org",
    "@type": "Organization",
    "name" : "Ticket Easys",
    "url": "https://ticketeasys.com/",
    "logo": "https://ticketeasys.com/assets/images/logo/logo.jpg",
    "sameAs" : [ 
      ""
    ] 
  }
  </script>

</body>
</html>