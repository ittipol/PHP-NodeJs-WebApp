<?php

namespace App\Http\Repositories;

class ValidationRepository
{
  protected $operators = [
      '=', '<', '>', '<=', '>=', '<>', '!=',
      'like', 'like binary', 'not like', 'between', 'ilike',
      '&', '|', '^', '<<', '>>',
      'rlike', 'regexp', 'not regexp',
      '~', '~*', '!~', '!~*', 'similar to',
      'not similar to',
  ];

  public static function isCurrency($number) {
    if(preg_match('/^[0-9]{1,3}(?:,?[0-9]{3})*(?:\.[0-9]{1,2})?$/', $number)) {
      return true;
    }
    return false;
  }

  public static function isPhoneNumber($phoneNumber) {
    if(preg_match('/^[0-9+][0-9\-]{3,}[0-9]$/', $phoneNumber)) {
      return true;
    }
    return false;
  }

  public static function isNumber($number) {
    if(preg_match('/^[0-9]+$/', $number)) {
      return true;
    }
    return false;
  }

  public static function isDecimal($number) {
    if(preg_match('/^[0-9]+(?:\.?[0-9]+)?$/', $number)) {
      return true;
    }
    return false;
  }

  public static function isZipcode($zipcode) {
    if(preg_match('/^[0-9]{5}$/', $zipcode)) {
      return true;
    }
    return false;
  }

  public static function isSqlOperators($operators) {
    if(in_array($operators, $this->operators)) {
      return true;
    } 
    return false;
  }

}

?>