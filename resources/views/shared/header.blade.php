<header>
  <ul id="gn-menu" class="gn-menu-main">
    <li class="gn-trigger">
      <a class="gn-icon gn-icon-menu">

        <div class="burger-wrapper">
          <div class="burger"></div>
        </div>
        <!-- <span>Menu</span> -->
        <!-- <i class="fas fa-bars"></i> -->
      </a>
      <nav class="gn-menu-wrapper">

        <div class="gn-scroller">
          <ul class="gn-menu">
            <li><a href="/" class="gn-icon fa-play">หน้าแรก</a></li>
            <li><a href="/ticket/new" class="gn-icon fa-plus">ขายตั๋ว วอชเชอร์ และอื่นๆ</a></li>
            @if(config('app.module_enabled.shop'))
            <li><a href="/shop" class="gn-icon fa-hotel">ร้านขายตั๋ว</a></li>
            @endif
            <li><a href="{{ config('app.blog_site_url') }}" target="_blank" class="gn-icon fa-rss">TicketEasys Blog</a></li>
            <li><a href="" target="_blank" class="gn-icon fa-facebook-square">Ticket Easys</a></li>
          </ul>
        </div>
        
      </nav>
    </li>

    <li class="brand-l">
      <a href="/"><img class="c-logo" src="/assets/images/logo/logo_x.jpg"></a>
    </li>

    <li>
      <a href="#" data-toggle="modal" data-c-modal-target="#modal_user_menu" class="avatar-frame pointer">
        @if(Auth::check())
        <img src="/avatar?d=1">
        @else
        <img src="/assets/images/common/avatar.png">
        @endif
      </a>
    </li>
  </ul>
</header>