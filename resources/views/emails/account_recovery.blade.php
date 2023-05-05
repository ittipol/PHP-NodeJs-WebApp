<style type="text/css">
  .wrapper {
    /*background-color: rgb(228,231,237);*/
    padding: 20px 0 100px;
  }

  .container {
    width: 800px;
    margin: 0 auto;
    background-color: #fff;
    /*border-top: 4px solid rgb(204,204,204);*/
    border-top: 4px solid rgb(29,66,133);
  }

  .content {
    padding: 0 20px 20px;
  }

  .button {
    font-size: 16px;
    font-family: Helvetica,Helvetica neue,Arial,Verdana,sans-serif;
    font-weight: none;
    color: #ffffff;
    text-decoration: none;
    background-color: #3572b0;
    border-top: 11px solid #3572b0;
    border-bottom: 11px solid #3572b0;
    border-left: 20px solid #3572b0;
    border-right: 20px solid #3572b0;
    border-radius: 5px;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    display: inline-block;
  }

</style>

<div class="wrapper">
  <div class="container">
    <img src="<?php // echo $message->embed(''); ?>">
    <div class="content">
      <p>เราได้รับคำขอการรีเซ็ตรหัสผ่านของคุณ<br>หากต้องการรีเซ็ตรหัสผ่าน โปรดคลิกลิงก์ต่อไปนี้:</p>
      <br>
      <a href="{{$link}}" class="button">รีเซ็ตรหัสผ่าน</a>
      <br>
      <br>
      <p>หากคุณไม่ได้ร้องขอคำขอนี้คุณสามารถเพิกเฉยต่อข้อความนี้ได้อย่างปลอดภัย คำขอจะหมดอายุในไม่ช้า</p>
      <br>
      <div>ขอบคุณ<br>ทีมงาน...</div>
    </div>
  </div>
</div>