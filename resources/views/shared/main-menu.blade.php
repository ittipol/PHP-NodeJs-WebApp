<div id="main_btn_group" class="main-btn-group @if(Auth::check()) main-btn-active @endif">
  <!-- <a href="#" data-toggle="modal" data-c-modal-target="#model_shop_menu" class="main-btn icon">
    <div class="main-btn-inner">
      <i class="fas fa-hotel"></i>
    </div>
  </a> -->
  @if(Auth::check() && config('app.module_enabled.cart'))
  <a href="/order" data-toggle="modal" data-c-modal-target="#model_order_menu" class="main-btn @if($_has_order) on @endif icon">
    <div class="main-btn-inner">
      <div id="order_badge" class="count-badge">{{ $_total_order }}</div>
      <i class="fas fa-tag"></i>
    </div>
  </a>
  @endif
  @if(Auth::check())
  <a id="event_notification_icon" href="#" data-toggle="modal" data-c-modal-target="#modal_event_notification_list" class="main-btn icon">
    <div class="main-btn-inner">
      <div id="notification_badge" class="count-badge"></div>
      <i class="fas fa-bell"></i>
    </div>
  </a>
  <a id="message_notification_icon" href="#" data-toggle="modal" data-c-modal-target="#modal_message_notification_list" class="main-btn icon">
    <div class="main-btn-inner">
      <div id="message_notification_badge" class="count-badge"></div>
      <i class="fas fa-comments"></i>
    </div>
  </a>
  @endif
  <a href="/ticket/new" class="main-btn icon">
    <div class="main-btn-inner">
      <i class="fas fa-plus"></i>
    </div>
  </a>
</div>

@include('shared.modal-user-menu')

@if(Auth::check())
@include('shared.modal-message-notification')
@include('shared.modal-event-notification')
@endif

@include('shared.modal-order-menu')

@if(config('app.module_enabled.cart'))
@include('shared.cart')
@endif