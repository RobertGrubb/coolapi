<?php

namespace CoolApi\Core;

class Request {

  public $params;

  public function __construct () {
    $this->params = (object) [];
  }

  public function param ($var = null) {
    if (is_null($var)) return false;

    if (!isset($this->params->{$var})) return false;

    return $this->params->{$var};
  }

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
    $post = \CoolApi\Core\Utilities::getPost();

    if (is_null($key)) return $post;
    if (isset($post[$key])) {
      return $post[$key];
    } else {
      return false;
    }
  }

  public function remoteIp () {
    return $_SERVER['SERVER_ADDR'];
  }

  public function origin () {
    $headers = $this->headers();
    $origin = false;

    // Get from the origin header
    if (array_key_exists('Origin', $headers)) {
      $origin = $headers['Origin'];

    // If there is a referer header
    } else if (array_key_exists('Referer', $headers)) {
      $origin = $headers['Referer'];

    // Lastly, get the remote address
    } else {
      $origin = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : '127.0.0.1';
    }

    return $origin;
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
