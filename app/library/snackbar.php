<?php

namespace App\library;

use Session;

class Snackbar
{
  public static function message($title = '',$type = 'info') {
    Session::flash('message.title', $title);
    Session::flash('message.type', $type); 
  }

  public static function modal($title = '',$message= '',$type = 'popup') {
    Session::flash('modal.title', $title);
    Session::flash('modal.message', $message);
    Session::flash('modal.type', $type); 
  }

}
