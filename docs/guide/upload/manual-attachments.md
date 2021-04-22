# Manual Attachments

If your Eloquent models has  `richTextFields` fields and the `InteractsWithMedia` trait setup, you shouldn't have to manually handle attachments.


```php
use MOIREI\MediaLibrary\AttachmentUpload;
use MOIREI\MediaLibrary\AttachmentApi;
use MOIREI\MediaLibrary\Models\Attachment;
...

$file = $request->file('file');
$upload = new AttachmentUpload($file);

// Perform all checks like above
$upload->checkType();

// Set location, optional
$upload->location('images'); // relative to `attachments.location` in your config

$attachment = $upload->save();
$url = $attachment->url;
```

### With the `AttachmentApi`

```php
$url = AttachmentApi::store($file);

// Specify location and disk
// Location is relative to `attachments.location` in your config
$url = AttachmentApi::store($file, '/', 's3');
```



### Associate a model

Using the `richTextFields` field

```php
$product->description = '
	...
	<img src="$attachment->url" alt="$attachment->alt" >
	...
';
$product->save();
```

Else

```php
$attachment->attach($product);
```



## Persisting attachments

Attachments are pending on creation until manually or automatically persisted by a `richTextFields` field.

You shouldn't need to do this if your models has the `richTextFields` fields and the `InteractsWithMedia` trait.

```php
$attachment = Attachment::where('url', $url)->first();
$attachment->persist();
```

### With the `AttachmentApi`

```php
AttachmentApi::persist($url);
```

### With the an eloquent model

For this to work, the models must have the attachment url embedded in an `img` tag in one of its `richTextFields` fields.

This call will replace any existing model attachment with `$product`.

```php
AttachmentApi::persistAttachments($product);
```



### Purging attachments

```php
$attachment = Attachment::where('url', $url)->first();
$attachment->purge();
```

With the `AttachmentApi`

```php
AttachmentApi::purge($url);
// Or with an associated model
AttachmentApi::delete($product);
```



Purge state attachments

```php
Attachment::pruneStale();
// or specify age
Attachment::pruneStale(7); // older than 7 days
```







