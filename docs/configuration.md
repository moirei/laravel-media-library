# Configuration



## Publish the config



```bash
php artisan vendor:publish --tag=media-library-config
```

The configuration file will be placed in `config/media-library.php`

## Api Path
Allow the endpoint path in `config/cors.php`. The default is `media-library`.
```php
...
'paths' => [..., 'media-library/*'],
...
```


## Cors

### Namespace
For namespacing uploads and access, the package allows providing a namespace at a request level. Useful for multi-vendor or applications with strict upload content ownership.

For multi-vendor, feel free to add a gateway middleware in `route.middleware` to verify your users' namespace.

Allow the `Namespace` header in `config/cors.php`:

```php
...
'allowed_headers' => [
    ...
    'Namespace'
],
```


## Middleware

Guard your upload and mutation endpoints to only allow admin access.

This guards the protected file access routes, browsing and file/folder update endpoints.

```php
...
'route' => [
	...
    'middleware' => [
        ...
        'media.protected' => [
            'auth',
            'can',
        ]
    ],
],
```

To further protect other routes, define the values for each route name.

See all [routes](/routes).
