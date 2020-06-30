<?php

namespace CoolApi\Core;

class Router {

  // The parent instance holder
  private $instance;

  /**
   * Stores registered routes
   */
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

    $this->createRoute('get', ($route[0] !== '/' ? '/' . $route : $route), $middleware, $handler);
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

    $this->createRoute('post', ($route[0] !== '/' ? '/' . $route : $route), $middleware, $handler);
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

    $this->createRoute('put', ($route[0] !== '/' ? '/' . $route : $route), $middleware, $handler);
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

    $this->createRoute('delete', ($route[0] !== '/' ? '/' . $route : $route), $middleware, $handler);
  }

  /**
   * Ability to provide an array of routes for a specific
   * path.
   * @param  string $prefix [description]
   * @param  array $routes [description]
   * @return boolean
   */
  public function use ($prefix, $routes) {
    $numArgs = func_num_args();
    $prefix = ($prefix[0] !== '/' ? '/' . $prefix : $prefix);
    $prefix = (substr($prefix, -1) !== '/' ? $prefix . '/' : $prefix);
    $middleware = null;
    $routes = $routes;

    // Check if middleware is provided.
    if ($numArgs === 3) {
      $middleware = func_get_arg(1);
      $routes    = func_get_arg(2);
    }

    // Routes is not an array, return false.
    if (!is_array($routes)) return false;

    // Count is 0, return false.
    if (count($routes) === 0) return false;

    // Iterate through each
    foreach ($routes as $route => $data) {

      // Assume it is a get route if not specified
      $method = (isset($data['method']) ? $data['method'] : 'get');

      // If handler does not exist, continue.
      if (!isset($data['handler'])) continue;

      $route = $prefix . ($route[0] === '/' ? ltrim($route, '/') : $route);
      $this->createRoute(strtolower($method), $route, $middleware, $data['handler']);
    }

    // Return true if we got this far.
    return true;
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

      // Log it
      $this->instance->logger->error("Route does not exist");

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
    if ($canRun === false) $this->instance->response->status(400)->output([
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

    // Do checks for baseUri
    if ($this->instance->config->baseUri !== '/') {
      $route = str_replace($this->instance->config->baseUri, '', $route);
    }

    // If the route is empty, load the home route.
    $route = '/' . $route;

    // Fail safe: remove double slashes at the beginning
    if (substr($route, 0, 2) === '//') $route = substr($route, 1);

    /**
     * Handle routes with parameters in the URL.
     * Example:
     */
    foreach ($this->routes[(strtolower($method))] as $regex => $data) {

      // Set the patter for the regex test
      $pattern = "@^" . preg_replace('/\\\:[a-zA-Z0-9\_\-]+/', '([a-zA-Z0-9\-\_]+)', preg_quote($regex)) . "$@D";

      // Test the route to make sure it matches
      if (preg_match($pattern, $route, $matches)) {

        $parts = explode('/', $regex);

        $params = [];

        // For each part of the url, find the vars.
        for ($i = 0; $i < count($parts); $i++) {

          // If this is a variable
          if (strpos($parts[$i], ':') !== false) {

            // Match the var to the matches from the URL.
            $params[str_replace(':', '', $parts[$i])] = isset($matches[(count($params) + 1)]) ? $matches[(count($params) + 1)] : null;
          }
        }

        // Set the parameters in the request class
        $this->instance->request->params = (object) $params;

        // Return the route
        return $this->routes[(strtolower($method))][$regex];
      }
    }

    // The route did not match any of the registered routes
    return false;
  }

  /**
   * Get the URI without any query parameters.
   */
  private function uri () {
    return $this->removeQueryStringVariables($_SERVER['REQUEST_URI']);
  }

  /**
   * Get the current request method
   */
  private function method () {
    return $_SERVER['REQUEST_METHOD'];
  }

  /**
   * Removed query parameters from a given URI.
   */
  private function removeQueryStringVariables($uri) {
    if ($uri != '') {
      $parts = explode('&', $uri, 2);
      if (strpos($parts[0], '=') === false) $uri = $parts[0];
      else $uri = '';
    }
    return $uri;
  }

  private function initialize () {

    /**
     * Do initialization here
     */
  }
}
