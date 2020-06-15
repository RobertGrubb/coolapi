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

    // Initialize the authorization layer
    $this->initialize();
  }

  private function initialize () {
    if ($this->instance->config->cors['enabled'] !== true) return;

    $passes = true;

    $this->origin = $this->instance->request->origin();

    if (is_array($this->instance->config->cors['whitelist'])) {
      if (count($this->instance->config->cors['whitelist']) >= 1) {
        $passes = $this->corsAllowed('whitelist');
      }
    }

    if (is_array($this->instance->config->cors['blacklist'])) {
      if (count($this->instance->config->cors['blacklist']) >= 1) {
        $passes = $this->corsAllowed('blacklist');
      }
    }

    var_dump($this->origin);
  }

  public function corsAllowed ($listType = 'whitelist') {
    if ($listType === 'whitelist') {

    }

    if ($listType === 'blacklist') {

    }
  }
}
