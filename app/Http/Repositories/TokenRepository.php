<?php

namespace App\Http\Repositories;

class TokenRepository
{
	public function generate($length = 64){
	  $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	  $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
	  $codeAlphabet .= "0123456789";

	  $token = '';
	  $len = strlen($codeAlphabet);

	  for ($i = 0; $i < $length; $i++) {
	    $token .= $codeAlphabet[rand(0,$len-1)];
	  };

	  return $token;
	}

	public function generateNumber($length = 64){
	  $codeAlphabet = "0123456789";

	  $token = '';
	  $len = strlen($codeAlphabet);

	  for ($i = 0; $i < $length; $i++) {
	    $token .= $codeAlphabet[rand(0,$len-1)];
	  };

	  return $token;
	}

	public function generateString($length = 64){
	  return base64_encode(random_bytes(32));
	}

	public function generateSecureKey($length = 64){
	  return bin2hex(random_bytes($length/2));
	}

	public function generateUrlSlug($length = 11){

	  $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-"; // 64 char

	  $token = '';
	  $len = strlen($codeAlphabet);

	  for ($i = 0; $i < $length; $i++) {
	    $token .= $codeAlphabet[rand(0,$len-1)];
	  };

	  return $token;
	}

}