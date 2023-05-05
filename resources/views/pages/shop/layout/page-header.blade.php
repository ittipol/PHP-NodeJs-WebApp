<div class="page-header-top">
  <div class="banner banner-header page-shop-cover">

    @if(Auth::check() && (Auth::user()->id == $shop['created_by']))
    <label id="upload_page_cover_btn" class="page-upload-cover-btn pointer">
      <input type="file" class="dn">
      <div class="pv2 ph4"><i class="far fa-image"></i>รูปภาพหน้าปก</div>
    </label>
    @endif

    @if(!empty($shop['cover']))
    <div class="banner-bg dn db-l" style="background-image: url('{{$shop['cover']}}');"></div>
    <img src="{{$shop['cover']}}">
    @else
    <div class="banner-bg"></div>
    <img src="/assets/images/common/cover.png">
    @endif
  </div>

  <div class="c-page-container page-wrapper page-item-style">

    <div class="page-header-secondary">

      <div class="page-profile-image-frame">
        <div class="page-profile-image">
          <img @if(!empty($shop['profileImage'])) src="{{$shop['profileImage']}}" @endif>
        </div>
        
        @if(Auth::check() && (Auth::user()->id == $shop['created_by']))
        <label id="upload_page_profile_image_btn" class="upload-profile-image-btn pointer">
          <input type="file" class="dn">
          <i class="far fa-image f4 f3-ns"></i>
        </label>
        @endif
      </div>

      <div class="page-header-content">
        <div class="clearfix">
          <div class="w-100 w-60-ns fl pv3 ph2 bb bn-ns b--moon-gray">
            <h3 class="ma0">
              <a href="/shop/page/{{$shop['slug']}}" class="white no-underline">{{$shop['name']}}</a>
              <div class="online_status_indicator_{{$shop['id']}} online-status-indicator dib @if($shop['owner']['online']) is-online @endif"></div>
            </h3>
            @if(!empty($shop['locations']))
            <div class="location-wrapper">
              <i class="fas fa-map-marker f6 mr1 color-orange"></i>
              @foreach($shop['locations'] as $path)
                <small class="location-name">{{$path['name']}}</small>
              @endforeach
            </div>
            @endif
          </div>
          <div class="w-100 w-40-ns fl tr pv2">
            <a class="btn btn-facebook btn-share" href="https://www.facebook.com/sharer/sharer.php?u={{Request::fullUrl()}}" target="_blank">
              <i class="fab fa-facebook-f"></i>
            </a>
            <a class="btn btn-twitter btn-share" href="https://twitter.com/intent/tweet?url={{Request::fullUrl()}}&amp;text={{$shop['name']}}" target="_blank">
              <i class="fab fa-twitter"></i>
            </a>
            <a class="btn btn-googleplus btn-share" href="https://plus.google.com/share?url={{Request::fullUrl()}}" target="_blank">
              <i class="fab fa-google-plus-g"></i>
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>