<div class="bb b--moon-gray mb3">
  <div class="c-form-container">
    <h5 class="mb3">ธีม</h5>
  </div>
</div>

<div class="c-form-container mb4">
  <div class="row">
    <div class="col-12 clearfix">
      @foreach($themes as $key => $theme)
      <label class="theme-color {{$theme['name']}} fl">
        @if($key == 1)
        {{Form::radio('theme_color_id', $key, true)}}
        @else
        {{Form::radio('theme_color_id', $key, false)}}
        @endif
        <div class="theme-color-tile"></div>
      </label>
      @endforeach
    </div>
  </div>
</div>