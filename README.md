# Cool API

**NOTE: DEVELOPMENT IN PROGRESS**

A new PHP API framework that is simple and fast.

## Installation

Make sure you have rewrite mod enabled, and you place the following in `.httaccess` where your public folder is located:

```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```

**NOTE: The API will attempt to do this soon for you if the directory is writable**

`composer require robert-grubb/coolapi`

## Example of Usage

```
use CoolApi\Instance;

// Instantiate Cool Api
$api = new Instance();

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
$app->router->get('/test', function ($req, $res) { /** Code here **/ });
```

And is the same for POST, PUT, or DELETE

```
$app->router->post('/test', function ($req, $res) { /** Code here **/ });
$app->router->put('/test', function ($req, $res) { /** Code here **/ });
$app->router->delete('/test', function ($req, $res) { /** Code here **/ });
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

## Requiring an API Key in the request

You can use the following configuration to require an API key during the request to lockdown your API.

```
[
  // Require an api key
  'requireKey' => true,

  // What param does it look for in POST or GET
  'keyField' => 'key',

  // List of valid keys
  'keys' => [
    'asdgioadsg32tegas'
  ]
]
```

`keyField` is looked at only if the request does not include a Authorization: Bearar <Token> in the request. It will then look for a `?key=` in the url, or `$_POST['key']`.

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
$app->router->get('/test', $middleware, function ($req, $res) {
  // Do as you please here
});
```
