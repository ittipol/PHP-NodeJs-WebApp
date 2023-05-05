@extends('shared.main')
@section('content')

<style type="text/css">
  body {
    overflow: hidden;
  }
</style>

<div class="chat-left-sidenav">
  <div class="user-chat-list p-3">

    <div class="bb b--silver tc pb2 mb2">
      <h6>แชท</h6>
    </div>

    @foreach($users as $user)
      <div class="clearfix mv4">
        <div class="avatar-frame fl">
          <div class="online_status_indicator_{{$user['id']}} online-status-indicator @if($user['online']) is-online @endif"></div>
          <div class="avatar">
            <img src="/avatar/{{$user['id']}}?d=1">
          </div>
        </div>
        <div class="online-name fl">{{$user['name']}}</div>
      </div>
    @endforeach
  </div>
</div>

<div class="chat-section">

  <div class="chat-title">
    @foreach($users as $user)
      <div class="online_status_indicator_{{$user['id']}} online-status-indicator @if($user['online']) is-online @endif"></div>
    @endforeach
    <a href="{{$data['url']}}">
      [{{$label}}] — {{$data['title']}}
    </a>
  </div>

  <div class="typing-indicator">
    <span></span>
    <span></span>
    <span></span>
  </div>

  <div id="message_display" class="chat-thread clearfix"></div>

  <div class="chat-footer-section">
    <input type="text" id="message_input" class="chat-input">
    <button id="send_btn" class="chat-send-btn">
      <i class="fa fa-location-arrow"></i>
    </button>
  </div>
</div>

<script type="text/javascript" src="/assets/js/form/chat.js"></script>

<script type="text/javascript">
  $(document).ready(function(){
    const _chat = new Chat({!!$chat!!});
    _chat.init();

    const _userOnline = new UserOnline();
    _userOnline.init();
  });
</script>

@stop