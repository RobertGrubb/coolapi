<?php

namespace CoolApi\Core;

class Cors {

  // Parent instance holder
  private $instance;

  // Holds the origin of the request
  private $origin;

  /**
   * Class construction
   */
  public function __construct ($instance) {

    // Set the parent instance
    $this->instance = $instance;
  }

  public function initialize () {

    // If not enabled, stop here.
    if ($this->instance->config->cors['enabled'] !== true) return;

    // Run the Cors layer
    $this->run();
  }

  public function run () {

    // Set passes to true by default.
    $passes = true;

    // Set the origin from the request class
    $this->origin = $this->instance->request->origin();

    /**
     * Look into the whitelist configuration, if it allows all
     * return true now. If it is an array of whitelisted domains
     * return true if the origin matches.
     */
    if (!is_array($this->instance->config->cors['whitelist']) &&
      $this->instance->config->cors['whitelist'] === '*') {
      $passes = true;
    } else {
      if (count($this->instance->config->cors['whitelist']) >= 1) $passes = $this->corsAllowed('whitelist');
    }

    /**
     * Look into the blacklist, if provided, and the origin matches one
     * from the list, then go ahead and stop it here.
     */
    if ($this->instance->config->cors['blacklist'] !== false) {
      if (is_array($this->instance->config->cors['blacklist'])) {
        $passes = $this->corsAllowed('blacklist');
      }
    }

    // If it does not pass, return an unauthorized error.
    if (!$passes) $this->instance->response->status(401)->output([
      'error' => true,
      'message' => 'Unauthorized request'
    ]);
  }

  public function checkOrigin ($allowedOrigin) {
    if (strpos($allowedOrigin, $this->origin) !== false) return true;
    else return false;
  }

  /**
   * Check the origin against the whitelist or blacklist.
   *
   * Return true if it is on the whitelist, false if it's on the
   * blacklist.
   */
  public function corsAllowed ($listType = 'whitelist') {

    // If in the whitelist, return true.
    if ($listType === 'whitelist') {
      foreach ($this->instance->config->cors['whitelist'] as $url) {
        if (strpos($url, $this->origin) !== false) return true;
      }

      return false;
    }

    // If in the blacklist, return false.
    if ($listType === 'blacklist') {
      foreach ($this->instance->config->cors['blacklist'] as $url) {
        if (strpos($url, $this->origin) !== false) return false;
      }

      return true;
    }
  }
}
