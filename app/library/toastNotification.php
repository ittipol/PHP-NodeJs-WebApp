<?php

namespace App\library;

use Session;

class ToastNotification
{
  public static function show($title = '',$subTitle = '', $image = '', $button = []) {
    Session::flash('toast-notification.title', $title);
    Session::flash('toast-notification.options', [
    	'subTitle' => $subTitle,
    	'image' => $image,
    	'button' => json_encode($button),
    ]);
    // Session::flash('toast-notification.subTitle', $title);
    // Session::flash('toast-notification.images', $image);
    // Session::flash('toast-notification.button', json_encode($button));
  }
}
