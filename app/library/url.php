<?php

namespace App\library;

class Url
{
  private static $urls = array();

  public static function url($url,$check=true) {
    $url = url($url);

    if($check) {
      $url = Url::addSlash($url);
    }
    
    return $url;
  }

  public static function addSlash($str) {
    if(substr($str, -1) != '/') {
      $str .= '/';
    }
    return $str;
  }

  public static function getUrls() {
    return Url::$urls;
  }

  public static function setUrl($url,$index) {

    preg_match_all('/{[\w0-9]+}/', $url, $matches);

    Url::$urls[$index] = array(
      'url' => url($url),
      'pattern' => $matches[0]
    );
  }

  public static function setAndParseUrl($url,$data) {

    preg_match_all('/{[\w0-9]+}/', $url, $matches);

    $url = array(
      'url' => url($url),
      'pattern' => $matches[0]
    );

    foreach ($url['pattern'] as $pattern) {
    
      $field = substr($pattern, 1,-1);

      if(!empty($data[$field])) {
        $url['url'] = str_replace($pattern, $data[$field], $url['url']);
      }

    }

    return $url['url'];

  }

  public static function clearUrls() {
    Url::$urls = array();
  }

  public static function parseUrl($data) {
    $urls = array();

    foreach (Url::$urls as $index => $url) {

      foreach ($url['pattern'] as $pattern) {
    
        $field = substr($pattern, 1,-1);

        if(!empty($data[$field])) {
          $url['url'] = str_replace($pattern, $data[$field], $url['url']);
        }

      }

      $urls[$index] = $url['url'];

    }

    return $urls;
  }

  public static function redirect($url,$ssl=false) {

    if((substr($url, 0,7) == 'http://') || (substr($url, 0,8) == 'https://')) {
      return Url::url('redirect?url='.$url);
    }

    if($ssl) {
      $url = 'https://'.$url;
    }else{
      $url = 'http://'.$url;
    }

    return Url::url('redirect?url='.$url);
  }

  // public function download() {
  //   //download/{code}
  // }

}