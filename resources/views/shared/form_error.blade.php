@if(!empty($errors->all()))
  <div class="alert alert-danger">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <h4>พบข้อผิดพลาด</h4>
      @foreach ($errors->all() as $message)
        <p><i class="fa fa-genderless"></i> {{$message}}</p>
      @endforeach
  </div>
@endif