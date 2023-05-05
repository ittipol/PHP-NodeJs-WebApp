<?php

namespace App\library;

class Currency {

	public static function format($number,$currency = 'THB',$position = 'prefix') {
    // position
    // prefix
    // suffix

    if(!Validation::isCurrency($number)) {
      return null;
    }

    $pos = strpos($number, '.');

    if(empty($pos)) {
      $number = number_format($number, 2, '.', ',');
    }else {
      list($integer,$point) = explode('.', $number);

      if(strlen($point) >= 1) {
        $number = number_format($number, 2, '.', ',');     
      }
    }

    if($currency == '') {
      return $number;
    }

    return Currency::_build($number,$currency,$position);
	}

  public static function _build($number,$currency,$position) {

    switch ($position) {
      case 'suffix':
        $pattern = $number.' {currency}';
        break;
      
      default:
        $pattern = '{currency} '.$number;
        break;
    }

    return str_replace('{currency}', $currency, $pattern);

  }

  public static function vatRound($number) {

    $pos = strpos($number, '.');

    if(!empty($pos)) {

      list($number,$point) = explode('.', $number);

      if((int)$point > 0) {
        $number += 1;
      }

    }

    return (int)$number;
  }

}
