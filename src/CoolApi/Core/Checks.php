<?php

namespace CoolApi\Core;

class Checks {

  // Parent instance holder
  private $instance;

  // The root directory of the api
  private $root;

  /**
   * Class construction
   */
  public function __construct ($instance) {

    // Set the parent instance
    $this->instance = $instance;

    // Initialize the authorization layer
    $this->initialize();
  }

  private function initialize () {
    $this->root = \CoolApi\Core\Utilities::root();
  }

  /**
   * Runs all checks that are needed for the API
   * to run correctly
   *
   * @TODO: Add check for .htaccess
   */
  public function run () {

    /**
     * Check to make sure logging is enabled for the API.
     */
    if ($this->instance->config->logging['enabled'] === true) {

      $logPath = $this->instance->config->logging['path'];

      /**
       * Check logs path existance and whether
       * or not it is writable.
       */
      if (!is_dir("{$logPath}"))
        throw new \Exception("{$logPath} does not exist.");

      if (!is_writable("{$logPath}"))
        throw new \Exception("{$logPath} is not writable.");
    }

    /**
     * Check if .htaccess exists.
     */
    if (!file_exists("{$this->root}/.htaccess"))
      throw new \Exception("{$this->root}/.htaccess does not exist");

    /**
     * If rate limiting is enabled, we must test the storage
     * path for 1. if it exists, and 2. if it is writable.
     */
    if ($this->instance->config->rateLimit['enabled'] === true) {
      if (!$this->instance->config->storage['path'])
        throw new \Exception("LimitRequests is enabled, you must set a storagePath.");

      if (!is_dir($this->instance->config->storage['path']))
        throw new \Exception("Storage path is not a directory.");

      if (!is_writable($this->instance->config->storage['path']))
        throw new \Exception("Storage path is not writable.");
    }
  }
}
