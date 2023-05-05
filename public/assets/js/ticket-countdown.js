class TicketCountdown {

  static init(elem,expireDate,now = null) {

    if(expireDate > 0) {

      let diff = expireDate - Math.ceil(Date.now()/1000);
      // let diff = expireDate - now;

      if(diff > 0) {
        $(elem).text(TicketCountdown.findRemainingDays(diff--));

        TicketCountdown.textColor(elem,diff);

        let handle = setInterval(function(){

          if(diff == 0) {
            clearInterval(handle);
            $(elem).text('บัตรหมดอายุการใช้งาน');
          }

          $(elem).text(TicketCountdown.findRemainingDays(diff--));

          TicketCountdown.textColor(elem,diff);
        },1000);
      }else {
        $(elem).text('บัตรหมดอายุการใช้งาน');
        // $(elem).css('color','rgba(96,125,139,.5)');
        $(elem).css('color','#FF5722');
      }

      
    }
    
  }

  static findRemainingDays(timeLeft) {

    let secs = timeLeft;
    let mins = parseInt(Math.floor(secs / 60));
    let hours = parseInt(Math.floor(mins / 60));
    let days = parseInt(Math.floor(hours / 24));

  //   // months = (int)floor(days / 30);
  //   // years = (int)floor(months / 12);

    let remaining = [];

    if(days == 0) {

      let remainingSecs = secs % 60;
      let remainingMins = mins % 60;
      let remainingHours = hours % 24;

      if(remainingHours != 0) {
        remaining.push(remainingHours+' ชั่วโมง');
      }

      if(remainingMins != 0) {
        remaining.push(remainingMins+' นาที');
      }

      if(remainingSecs != 0) {
        remaining.push(remainingSecs+' วินาที');
      }

    }else{

      remaining.push(days+' วัน');

      let remainingSecs = secs % 60;
      let remainingMins = mins % 60;
      let remainingHours = hours % 24;

      if(remainingHours != 0) {
        remaining.push(remainingHours+' ชั่วโมง');
      }

      if(remainingMins != 0) {
        remaining.push(remainingMins+' นาที');
      }

      if(remainingSecs != 0) {
        remaining.push(remainingSecs+' วินาที');
      }

    }

    return remaining.join(' ');
  }

  static textColor(elem,time) {

    if(time > 2592000) {
      $(elem).removeClass('ticket-countdown-red').addClass('ticket-countdown-green');
    }else {
      $(elem).removeClass('ticket-countdown-green').addClass('ticket-countdown-red');
    }

  }
}