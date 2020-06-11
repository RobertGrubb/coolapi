<?php

namespace CoolApi\Core;

class Request {

  // Retrieve get variables
  public function get($key = null) {
    if (is_null($key)) return $_GET;
    if (isset($_GET[$key])) {
      return $_GET[$key];
    } else {
      return false;
    }
  }

  // Retrieve POST variables
  public function post($key = null) {
    if (is_null($key)) return $_POST;
    if (isset($_POST[$key])) {
      return $_POST[$key];
    } else {
      return false;
    }
  }

  public function remoteIp () {
    return $_SERVER['SERVER_ADDR'];
  }

  // Retrieve a specific header
  public function header ($key) {
    $headers = $this->headers();

    foreach ($headers as $name => $value) {
      if ($name === $key) return $value;
    }

    return false;
  }

  // Get all headers
  public function headers () {
    if (!function_exists('getallheaders')) {
      $headers = '';

      foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) == 'HTTP_') {
          $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
      }

      return $headers;
    }

    return getallheaders();
  }
}
