<?php

namespace App\library;

class ThemeColor
{
  private static $themes = array(
    1 => array(
      'name' => 'theme-color-blue',
      // 'code' => '#4285f4',
    ),
    2 => array(
      'name' => 'theme-color-red',
      // 'code' => '#db4437',
    ),
    3 => array(
      'name' => 'theme-color-yellow',
      // 'code' => '#f4b400',
    ),
    4 => array(
      'name' => 'theme-color-green',
      // 'code' => '#0f9d58',
    ),
    5 => array(
      'name' => 'theme-color-purple',
      // 'code' => '#673ab7',
    ),
    6 => array(
      'name' => 'theme-color-blue-grey',
      // 'code' => '#607D8B',
    ),
    7 => array(
      'name' => 'theme-color-pink',
      // 'code' => '#d81b60',
    ),
  );

  public static function getThemes() {
    return ThemeColor::$themes;
  }

  public static function getThemeById($id) {

    if(empty($id)) {
      $id = 1;
    }

    return ThemeColor::$themes[$id]['name'];
  }
}
