# Manual Uploads


```php
use MOIREI\MediaLibrary\Upload;
...

$upload = new Upload(
    $request->file('file'),
);

$upload->checkType(); // check if type is allowed, returns boolean
$upload->checkSize(); // check if size is below limits, returns boolean
$upload->cleanFilename(); // slugify filename

$location = 'products/chargers';
$namespace = 'Vendor-A'; // optional
$upload->location($location, $namespace);

$file = $upload->save();
```

### Using the Api

```php
$folder = Folder::find('folder-id');

$upload = Api::upload(
	$request->file('file')
);

$file = $upload
    	->folder($folder) // set location from folder
    	->name('Space Cat') // name the file model. Separate from filename/storage names
    	->checkSize(true) // throws an error if not valid
    	->checkSize(true) // throws an error if not valid
    	->disk('s3')
    	->private() // set file to private. Also accepts bool to make public
    	->save();
```

### Uploading from URL sources

Sources could be a public url or a local path.

```php
$upload = Upload::fromUrl('https://url');
// or
$upload = Api::upload('https://url');

$upload->save();
```
