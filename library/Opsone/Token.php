<?php

class Opsone_Token
{
  public static function get($length = 40)
  {
    $token = sha1(uniqid(mt_rand(), true));

    return substr($token, 0, (int) $length);
  }
}