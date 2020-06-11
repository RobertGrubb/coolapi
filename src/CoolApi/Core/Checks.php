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
    $root = dirname(\Composer\Factory::getComposerFile());
    $this->root = $root;
  }

  /**
   * Runs all checks that are needed for the API
   * to run correctly
   *
   * @TODO: Add check for .htaccess
   */
  public function run () {

    /**
     * Check logs path existance and whether
     * or not it is writable.
     */
    if (!is_dir("{$this->root}/logs"))
      throw new \Exception("{$this->root}/logs does not exist.");

    if (!is_writable("{$this->root}/logs"))
      throw new \Exception("{$this->root}/logs is not writable.");

    /**
     * Check if .htaccess exists.
     */
    if (!file_exists("{$this->root}/.htaccess"))
      throw new \Exception("{$this->root}/.htaccess does not exist");

    /**
     * If rate limiting is enabled, we must test the storage
     * path for 1. if it exists, and 2. if it is writable.
     */
    if ($this->instance->config->limitRequests) {
      if (!$this->instance->config->storagePath)
        throw new \Exception("LimitRequests is enabled, you must set a storagePath.");

      if (!is_dir($this->instance->config->storagePath))
        throw new \Exception("Storage path is not a directory.");

      if (!is_writable($this->instance->config->storagePath))
        throw new \Exception("Storage path is not writable.");
    }
  }
}
