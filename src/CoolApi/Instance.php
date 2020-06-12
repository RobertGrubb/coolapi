<?php

namespace CoolApi;

/**
 * Main class for CoolApi. This is responsible for
 * spinning up instances of classes that the API
 * relies on in order to work correctly.
 *
 * This class can be called with an initialConfig, please
 * refer to the README for me information on that.
 */
class Instance {

  // Holds the configuration variables
  public $config = null;

  /**
   * The router instance holder
   * @var CoolApi\Core\Router
   */
  public $router;

  /**
   * The response instance holder
   * @var CoolApi\Core\Response
   */
  public $response;

  /**
   * The request instance holder
   * @var CoolApi\Core\Request
   */
  public $request;

  /**
   * The logger instance holder
   * @var CoolApi\Core\Logger
   */
  public $logger;

  /**
   * Class constructor
   * @param array $customConfig
   */
  public function __construct ($customConfig) {

    $initialConfig = [

      /**
       * base uri configuration
       */
      'baseUri' => '/',

      /**
       * Log configuration
       */
      'logging' => [
        'enabled' => true,
        'path'    => \CoolApi\Core\Utilities::root() . '/logs/',
        'file'    => 'api.log'
      ],

      /**
       * Configuration for api keys
       */
      'apiKeys'    => [
        'enabled'  => false,
        'keyField' => 'key',
        'keys'     => []
      ],

      /**
       * Configuration for Rate Limiting
       */
      'rateLimit' => [
        'enabled' => true,
        'limit'   => 100,
        'window'  => (60 * 15)
      ],

      /**
       * Configuration for storage
       * (rateLimit requires a storage path)
       */
      'storage' => [
        'path'  => false
      ]

    ];

    // Set the initial config
    $this->initialize(\CoolApi\Core\Utilities::merge_config($initialConfig, $customConfig));

    // Do checks for paths, etc
    (new \CoolApi\Core\Checks($this))->run();

    // Set all class instances
    $this->logger   = new \CoolApi\Core\Logger($this);
    $this->router   = new \CoolApi\Core\Router($this);
    $this->response = new \CoolApi\Core\Response();
    $this->request  = new \CoolApi\Core\Request();

    // Run the authorization layer
    (new \CoolApi\Core\Authorization($this));

    // Run the rate limiter layer
    (new \CoolApi\Core\RateLimiter($this));
  }

  /**
   * Runs the main methods that the API depends on.
   */
  public function run () {
    $this->router->run();
  }

  /**
   * Sets up the API configuration object.
   * This also does any necessary initial reformatting
   * of config variables like baseUri.
   */
  private function initialize ($initial = []) {
    $this->config = (object) $initial;

    // Re format the baseUri
    if (isset($this->config->baseUri)) {
      $this->set('baseUri', $this->config->baseUri);
    }

    // Fix the logging path
    if (isset($this->config->logging['path'])) {
      if (substr($this->config->logging['path'], -1) !== '/') {
        $this->config->logging['path'] = $this->config->logging['path'] . '/';
      }
    }
  }

  /**
   * Get a config variable.
   */
  public function get ($var) {
    return $this->config->{$var} || false;
  }

  /**
   * Set a config variable.
   */
  public function set ($var, $val) {

    // If it's a baseUri, remove the trailing slash
    if ($var === 'baseUri') $val = rtrim($val, '/');

    $this->config->{$var} = $val;
  }
}
