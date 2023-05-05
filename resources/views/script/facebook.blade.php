<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '{{env("FB_APP_ID")}}',
      xfbml      : true,
      version    : '{{env("GRAPH_VERSION")}}'
    });
    FB.AppEvents.logPageView();
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>