<?php

namespace CoolApi\Core;

class Authorization {

  // Parent instance holder
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

  /**
   * Decides whether it needs to continue with key
   * validation or not.
   */
  public function initialize () {

    // Make sure apiKeys are enabled
    if ($this->instance->config->apiKeys['enabled'] === true) $this->validateKey();
  }

  /**
   * Attempts to validate an api key if it is required
   * by the instance configuration object.
   * @return boolean
   */
  private function validateKey () {

    // Attempt to get the key from 1 of the 3 sources
    $key = $this->getApiKey();

    // If no key, return unauthorized error.
    if (!$key) return $this->unauthorized();

    // If there are no keys provided in config
    if (!isset($this->instance->config->apiKeys['keys'])) return $this->unauthorized();

    // Make sure keys are provided in array format.
    if (!is_array($this->instance->config->apiKeys['keys'])) return $this->unauthorized();

    /**
     * Iterate through the keys, and validate it as a valid key.
     */
    foreach ($this->instance->config->apiKeys['keys'] as $apiKey => $keyData) {

      // If not an array, just match the key to the apiKey.
      if (!is_array($keyData)) if ($keyData === $key) return true;

      /**
       * If an array is provided, then an origin was most likely set for
       * the key itself.
       */
      if ($apiKey === $key) {

        // We have matched the key.
        $passes = true;

        // Check the origin
        if (isset($keyData['origin'])) $passes = $this->instance->cors->checkOrigin($keyData['origin']);
      }
    }

    // If passes was set to false, return the error.
    if (!$passes) return $this->unauthorized();

    // Finally, if here, then we found the key.
    return $passes;
  }

  /**
   * Attempts to find the api key from 1 of
   * the 3 sources. Auth header, GET, or POST.
   * @return boolean|string
   */
  private function getApiKey () {
    $keyField = $this->instance->config->apiKeys['keyField'];
    $key = false;

    // Is it in bearer token form?
    $bearerToken = $this->getBearerToken();

    // Is it passed as a get var?
    $getKey = $this->instance->request->get($keyField);

    // Is it provided as a post key?
    $postKey = $this->instance->request->post($keyField);

    // Set if any are provided
    if ($bearerToken) $key = $bearerToken;
    if ($getKey) $key = $getKey;
    if ($postKey) $key = $postKey;

    if (!$key) return false;
    return $key;
  }

  /**
   * Pluck the token from the header.
   * @return boolean|string
   */
  private function getBearerToken() {
    $authHeader = $this->instance->request->header('Authorization');
    if (!$authHeader) return false;
    if (substr($authHeader, 0, 7) !== 'Bearer ') return false;
    return trim(substr($authHeader, 7));
  }

  /**
   * Returns an error and exits the app immediately.
   */
  private function unauthorized () {
    $this->instance->logger->error('Missing valid api key in request.');

    $this->instance->response->status(401)->output([
      'error' => true,
      'message' => 'Unauthorized'
    ]);
  }
}
