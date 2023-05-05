<?php

namespace App\library;

class Format {

  public static function percent($number) {
    return round($number, 0, PHP_ROUND_HALF_UP);
  }

}
