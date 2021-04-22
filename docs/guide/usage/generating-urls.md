# Generating Urls



## Public Urls

Generate a public url for a file.

For private files, a signed or temporal url with a default 30s TTL is returned.

```php
use MOIREI\MediaLibrary\Api;
use MOIREI\MediaLibrary\Models\File;
...

$file = File::find('file-id');
$url = Api::getPublicUrl($file);
// Or
$url = Api::getPublicUrl($image, now()->addMinutes(60));
// Or
$url = (string)$file; // casting files/folders to string will return a public url
```



## Responsive Public Urls

You can also generate set of urls for responsive images.

For private files, the urls are signed with a default 30s TTL.

```php
$image = File::find('image-file-id');
$urls = Api::getResponsivePublicUrl($image);
// Or
$urls = Api::getResponsivePublicUrl($image, now()->addMinutes(60));

// $urls should contain keys with the keys in `images.sizes` config
```



## Protected Urls

Protected routes provide an additional layer of security. Actual protection is decided by the middleware you configure for the route group.

To fully set it up, define middles for `file.protected` in `route.middleware` of your config.

```php
$url = Api::getProtectedUrl($file);
```



## Local Urls

Get a url local to your application. The url will include `yourdomain.com` regardless of the file's storage driver.

For private files, the url is signed with a default 30s TTL.

```php
$url = Api::getUrl($file);
// Or
$url = Api::getUrl($file, now()->addMinutes(60));
```



## Download Urls

Generated url can be used to automatically download the file.

For private files, the url is signed with a default 30s TTL.

```php
$url = Api::getDowloadUrl($file);
// Or
$url = Api::getDowloadUrl($file, now()->addMinutes(60));
```



## Dynamic Image Sizes

All generated *image* urls pointing to your application domain (mostly `local` and `protected` urls) may be dynamically sized on demand.

Syntax:

`<url>/{width}/{height?}`



**Example original url**

`youdomain.com/media-library/get/public/<id>`

**Dynamically sized image original url**

`youdomain.com/media-library/get/public/<id>/100`

With width and height

`youdomain.com/media-library/get/public/<id>/100/100`



