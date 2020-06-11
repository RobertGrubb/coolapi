<?php

namespace CoolApi\Core;

class Authorization {

  private $instance;

  /**
   * Class construction
   */
  public function __construct ($instance) {

    // Set the parent instance
    $this->instance = $instance;

    // Initialize the authorization layer
    $this->initialize();
  }

  public function initialize () {
    if (!isset($this->instance->config->requireKey)) return false;
    if ($this->instance->config->requireKey === true) $this->validateKey();
  }

  private function validateKey () {
    $key = $this->getApiKey();

    if (!$key) return $this->unauthorized();

    if (!isset($this->instance->config->keys)) return $this->unauthorized();
    if (!is_array($this->instance->config->keys)) return $this->unauthorized();

    foreach ($this->instance->config->keys as $apiKey) {
      if ($key === $apiKey) return true;
    }

    return $this->unauthorized();
  }

  private function getApiKey () {
    $keyField = $this->instance->config->keyField;
    $key = false;

    $bearerToken = $this->getBearerToken();
    $getKey = $this->instance->request->get($keyField);
    $postKey = $this->instance->request->post($keyField);

    if ($bearerToken) $key = $bearerToken;
    if ($getKey) $key = $getKey;
    if ($postKey) $key = $postKey;

    if (!$key) return false;
    return $key;
  }

  private function getBearerToken() {
    $authHeader = $this->instance->request->header('Authorization');
    if (!$authHeader) return false;
    if (substr($authHeader, 0, 7) !== 'Bearer ') return false;
    return trim(substr($authHeader, 7));
  }

  private function unauthorized () {
    $this->instance->response->status(401)->output([
      'error' => true,
      'message' => 'Unauthorized'
    ]);
  }
}
