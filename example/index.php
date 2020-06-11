<?php

require '../vendor/autoload.php';
require_once __DIR__ . '/../src/CoolApi.php';

use CoolApi\Instance;

// Instantiate Cool Api
$api = new Instance([

  // Base URI (For sub-directories)
  'base_uri' => '/coolapi/example/',

  // Require an api key
  'requireKey' => false,

  // List of valid keys
  'keys' => [
    'asdgioadsg32tegas'
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

// Run the API
$api->run();
