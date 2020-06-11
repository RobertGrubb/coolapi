# Cool API

**NOTE: DEVELOPMENT IN PROGRESS**

A new PHP API framework that is simple and fast.

## Installation

Make sure you have rewrite mod enabled, and you place the following in `.httaccess` where your public folder is located. **NOTE:** CoolApi will throw an exception if it's not found.

```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```

**NOTE: The API will attempt to do this soon for you if the directory is writable**

`composer require robert-grubb/coolapi`

## Configuration Explained

All values below are the defaults set by the API.

```
$config = [

  /**
   * The baseUri is necessary if you are in a sub-directory on
   * hosting. An example is: http://localhost/foo/bar/api
   *
   * If you specify /foo/bar/api in baseUri, it will be removed
   * from the URI when matching routes.
   */
  'baseUri' => '/',

  /**
   * Log configuration:
   *
   * Logger requires a path that is writable, and the API will
   * throw an exception if it is not. Below is the configuration
   * for the logger.
   */
  'logging' => [
    'enabled' => true,
    'path'    => \CoolApi\Core\Utilities::root() . '/logs/',
    'file'    => 'api.log'
  ],

  /**
   * Configuration for api keys
   *
   * With CoolApi, you can require api keys for access to your
   * API. Look below for the configuration.
   *
   * CoolApi looks for the api key in 3 places:
   * - Authorization: Bearer <api_key_here>
   * - $_GET['key']
   * - $_POST['key']
   *
   * Will accept a valid key from any of the above locations.
   */
  'apiKeys'    => [
    'enabled'  => false,
    'keyField' => 'key', // What it looks for in $_GET or $_POST
    'keys'     => [] // Array of key strings
  ],

  /**
   * Configuration for Rate Limiting
   *
   * CoolApi comes with an out-of-the-box solution for rate limiting.
   * In order for this to work properly, you must:
   * 1. Provide a storage path (Look below for storage config) for FilerDB.
   * 2. Enable it, and set a limit per window.
   */
  'rateLimit' => [
    'enabled' => false,
    'limit'   => 100, // Number of requests
    'window'  => (60 * 15) // In seconds (15 minutes)
  ],

  /**
   * Configuration for storage
   * (rateLimit requires a storage path)
   * Path: A writable directory somewhere on your filesystem.
   */
  'storage' => [
    'path'  => false
  ]

];
```

## Example of Usage

```
use CoolApi\Instance;

// Instantiate Cool Api
$api = new Instance($config);

// Setup home route
$api->router->get('/', function ($req, $res) {

  // Return an output
  $res->status(200)->output([
    'foo' => 'bar'
  ]);
});

// Run the API
$api->run();
```

## Routing

To add a route for CoolApi, it's as simple as the following:

```
$api->router->get('/test', function ($req, $res) { /** Code here **/ });
```

And is the same for POST, PUT, or DELETE

```
$api->router->post('/test', function ($req, $res) { /** Code here **/ });
$api->router->put('/test', function ($req, $res) { /** Code here **/ });
$api->router->delete('/test', function ($req, $res) { /** Code here **/ });
```

## Use of $req

Getting POST, or GET variables:

```
$req->post('var_name'); // Returns $_POST['var_name'] || false
$req->get('var_name'); // Returns $_GET['var_name'] || false
$req->post() // Gets all $_POST variables
$req->get() // Gets all $_GET variables
```

Getting headers from the request

```
$req->headers(); // Returns all headers in array form
```

Getting a specific header:

```
$req->header('User-Agent');
```

## Use of $res

Returning a normal response:

```
$res->output([
  'foo' => 'bar'
]); // Treats it as a normal status of 200, and outputs as JSON.
```

Setting the status:

```
$res->status(400)->output([]);
```

Setting the content type:

```
$res->status(200)->contentType('plain')->output('plain text');
$res->status(200)->contentType('html')->output('<html></html>');
```

## Using Middleware

This api is setup so you can use your own middleware in the routes. Below is an example:

```
$middleware = function ($req, $res) {
  if (!$req->header('User-Agent')) {
    $res->status(400)->output([
      'error' => true,
      'message' => 'You must use a browser'
    ]);
  }

  return true; // Returning true, or returning nothing at all will pass.
}

/**
 * Passing $middleware as the 2nd argument tells the api
 * this needs to be ran before the handler method. If the middleware
 * returns a bad status, or returns false, the handler will never
 * be reached as the middleware fails.
 *
 * If the middleware returns true, then that means the handler can
 * successfully be reached and ran.
 */
$api->router->get('/test', $middleware, function ($req, $res) {
  // Do as you please here
});
```
