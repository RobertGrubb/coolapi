<?php

namespace CoolApi;

// Define globals if not already.
defined('COOLAPI_PATH') or define('COOLAPI_PATH',  __DIR__);

class Instance {

  public $config = null;

  public $router;
  public $response;
  public $request;

  public $auth;

  public function __construct ($initialConfig) {

    // Set the initial config
    $this->initialize(array_merge([

      'logFile' => 'api.log',

      // URL Configuration
      'base_uri' => '/',

      // Authorization Configuration
      'requireKey' => false,
      'keyField'   => 'key',
      'keys'       => []

    ], $initialConfig));

    // Do checks for paths, etc
    (new \CoolApi\Core\Checks($this))->run();

    $this->logger   = new \CoolApi\Core\Logger($this);
    $this->router   = new \CoolApi\Core\Router($this);
    $this->response = new \CoolApi\Core\Response();
    $this->request  = new \CoolApi\Core\Request();
    $this->auth     = new \CoolApi\Core\Authorization($this);
  }

  public function run () {
    $this->logger->info('Instance is running');
    $this->router->run();
  }

  private function initialize ($initial = []) {
    $this->config = (object) $initial;

    // Re format the base_uri
    if (isset($this->config->base_uri)) {
      $this->set('base_uri', $this->config->base_uri);
    }
  }

  public function get ($var) {
    return $this->config->{$var} || false;
  }

  public function set ($var, $val) {

    // If it's a base_uri, remove the trailing slash
    if ($var === 'base_uri') {
      $val = rtrim($val, '/');
    }

    $this->config->{$var} = $val;
  }
}
