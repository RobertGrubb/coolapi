<?php

require '../vendor/autoload.php';
require_once __DIR__ . '/../src/CoolApi.php';

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
    'enabled' => false,
    'keyField' => 'key',
    'keys' => [
      'asdgioadsg32tegas'
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

$middleware = function ($req, $res) {
  // $res->status(400)->output([
  //   'error' => true,
  //   'message' => 'Middleware error'
  // ]);
};

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

// Setup home route
$api->router->get('/user/:id', function ($req, $res) {

  // Return an output
  $res->status(200)->output([
    'foo' => 'bar',
    'id'  => $req->param('id')
  ]);
});

// Run the API
$api->run();
