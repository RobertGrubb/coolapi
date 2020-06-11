<?php

namespace CoolApi\Core;

class Router {

  private $instance;

  private $routes = [
    'get'    => [],
    'post'   => [],
    'put'    => [],
    'delete' => []
  ];

  /**
   * Class construction
   */
  public function __construct ($instance) {

    // Set the parent instance
    $this->instance = $instance;

    // Initialize the router
    $this->initialize();
  }

  /**
   * Sets a get route and stores the handler to be called.
   * @param  string $route   [description]
   * @param  function $handler
   */
  public function get ($route) {
    $numArgs = func_num_args();
    $middleware = null;
    $handler = null;

    /**
     * Depending on number of arguments,
     * we can determine if middleware is being passed
     */
    if ($numArgs === 3) {
      $middleware = func_get_arg(1);
      $handler    = func_get_arg(2);
    } else {
      $middleware = null;
      $handler    = func_get_arg(1);
    }

    $this->createRoute('get', $route, $middleware, $handler);
  }

  /**
   * Sets a post route and stores the handler to be called.
   * @param  string $route   [description]
   * @param  function $handler
   */
  public function post ($route, $handler) {
    $numArgs = func_num_args();
    $middleware = null;
    $handler = null;

    /**
     * Depending on number of arguments,
     * we can determine if middleware is being passed
     */
    if ($numArgs === 3) {
      $middleware = func_get_arg(1);
      $handler    = func_get_arg(2);
    } else {
      $middleware = null;
      $handler    = func_get_arg(1);
    }

    $this->createRoute('post', $route, $middleware, $handler);
  }

  /**
   * Sets a put route and stores the handler to be called.
   * @param  string $route   [description]
   * @param  function $handler
   */
  public function put ($route, $handler) {
    $numArgs = func_num_args();
    $middleware = null;
    $handler = null;

    /**
     * Depending on number of arguments,
     * we can determine if middleware is being passed
     */
    if ($numArgs === 3) {
      $middleware = func_get_arg(1);
      $handler    = func_get_arg(2);
    } else {
      $middleware = null;
      $handler    = func_get_arg(1);
    }

    $this->createRoute('put', $route, $middleware, $handler);
  }

  /**
   * Sets a delete route and stores the handler to be called.
   * @param  string $route   [description]
   * @param  function $handler
   */
  public function delete ($route, $handler) {
    $numArgs = func_num_args();
    $middleware = null;
    $handler = null;

    /**
     * Depending on number of arguments,
     * we can determine if middleware is being passed
     */
    if ($numArgs === 3) {
      $middleware = func_get_arg(1);
      $handler    = func_get_arg(2);
    } else {
      $middleware = null;
      $handler    = func_get_arg(1);
    }

    $this->createRoute('delete', $route, $middleware, $handler);
  }

  /**
   * Run the router
   *
   * Finds the route, and if it exists, will run
   * the handler with $req, and $res.
   *
   * If not found, it will return an error and a 400
   * response.
   */
  public function run () {
    $route  = $this->uri();
    $method = $this->method();

    // If the route does not exist
    if (!$route = $this->route($route, $method)) {

      $this->instance->response->status(400)->output([
        'error' => true,
        'message' => 'Route does not exist'
      ]);
    }

    // Set can run by default
    $canRun = true;

    // If middle ware is not null, run it.
    if (!is_null($route['middleware'])) {
      $canRun = $route['middleware'](
        $this->instance->request,
        $this->instance->response
      );
    }

    // If can't run, middleware did not return a error
    if (!$canRun) $this->instance->response->status(400)->output([
      'error' => true,
      'message' => 'Unknown error was returned'
    ]);

    // Call the handler
    $route['handler'](
      $this->instance->request,
      $this->instance->response
    );
  }

  private function createRoute ($method, $route, $middleware = null, $handler) {

    // Store the route
    $this->routes[$method][$route] = [
      'handler'    => $handler,
      'middleware' => $middleware
    ];
  }

  /**
   * Attempts to find the route in the class routes
   * array. if it does not find it, it will return false.
   * @param  string $route  [description]
   * @param  string $method [description]
   * @return false|array
   */
  private function route ($route, $method) {

    // Do checks for base_uri
    if ($this->instance->config->base_uri !== '/') {
      $route = ltrim($route, $this->instance->config->base_uri);
    }

    // If the route is empty, load the home route.
    $route = '/' . $route;

    // If not set, return false
    if (!isset($this->routes[(strtolower($method))][$route])) return false;

    // Return the route
    return $this->routes[(strtolower($method))][$route];
  }

  private function uri () {
    return $this->removeQueryStringVariables($_SERVER['REQUEST_URI']);
  }

  private function method () {
    return $_SERVER['REQUEST_METHOD'];
  }

  private function removeQueryStringVariables($uri) {
    if ($uri != '') {
      $parts = explode('&', $uri, 2);
      if (strpos($parts[0], '=') === false) $uri = $parts[0];
      else $uri = '';
    }
    return $uri;
  }

  private function initialize () {

    // $this->routes['get']['/'] = [
    //   'handler' => function () {
    //     echo "HOME";
    //   }
    // ];
  }
}
