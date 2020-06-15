<?php

require '../vendor/autoload.php';
require_once __DIR__ . '/../src/CoolApi.php';

$userRoutes = require_once __DIR__ . '/userRoutes.php';

use CoolApi\Instance;

// Instantiate Cool Api
$api = new Instance([

  // Base URI (For sub-directories)
  'baseUri' => '/coolapi/example/',

  'logging' => [
    'enabled' => true
  ],

  /**
   * Configuration for api keys
   */
  'apiKeys' => [
    'enabled' => true,
    'keyField' => 'key',
    'keys'     => [
      'test123' => [
        'origin' => 'www.facebook.com'
      ]
    ]
  ],

  /**
   * Configuration for Rate Limiting
   */
  'rateLimit' => [
    'enabled' => true,
    'limit'   => 5,
    'window'  => (60 * 1)
  ],

  /**
   * Configuration for storage
   */
  'storage'   => [
    'path' => __DIR__ . '/database'
  ]

]);

/**
 * Example of middleware, checks if user-agent
 * header is present.
 */
$middleware = function ($req, $res) {
  if (!$req->header('User-Agent')) return false;
  return true;
};

// Make use of routes from other files
$api->router->use('/user', $middleware, $userRoutes);

// Setup home route
$api->router->get('/', $middleware, function ($req, $res) {

  // Return an output
  $res->status(200)->output([
    'foo' => 'bar',
    'headers' => $req->headers(),
    'get' => $req->get(),
    'post' => $req->post()
  ]);

});

// Run the API
$api->run();
