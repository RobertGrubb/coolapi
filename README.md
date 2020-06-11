# Cool API

A new PHP API framework that is simple and fast.


## .htaccess

Make sure you have rewrite mod enabled, and you place the following in `.httaccess` where your public folder is located:

```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```
