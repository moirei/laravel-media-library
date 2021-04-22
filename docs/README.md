---
home: true
heroImage: /logo.png
heroText: Laravel Media Library
tagline: Laravel makes it very easy to manage files – this package makes a tiny bit easier.
actionText: Get Started →
actionLink: /installation/
features:
- title: 📟 File sharing
  details: Share public and private files with anyone. Comes with a basic and elegant UI.
- title: ❤ Eloquent models
  details: Associate any file types with Eloquent models. Can also define assiociation at an attribute level with in-built object and array casts.
- title: 📜 Simple APIs
  details: Exposed API endpoints and classes to easily integrate with any application.
- title: 📁 Directory system
  details: A directory system and namespacing for more structured storage and visualisating files.
- title: 🔒 Secure
  details: Public, signed, and protected endpoints for file sharing and downloads regardless of storage disks or permissions.
- title: 💪 Flexible
  details: Configure middleware for the whole package routes or per API endpoints for fine-grained permissions and authorization.
footer: MIT Licensed | Copyright © 2021 MOIREI
---

This package is intended to provide most media related needs for any Laravel application. File uploads or attachments may be associated with any Eloquent model.



## Example Usage

Assuming files content has already been uploaded and a pending attachments has been created.

### Associate files

```php
use MOIREI\MediaLibrary\Models\File;
...

[$file1, $file2] = File::take(2)->get();
$product = Product::find(1);
$product->attachFiles([$file1, $file2]);
```



### Use in attribute fields

**During create**

```php
$product = Product::create([
    'MOIREI MP202+',
    'image' => $file1,
    'gallery' => [$file1, $file2],
    'description' => $richtext, // rich text content with <img src=".." /> attachment urls
]);
```



**During update**

```php
$product = Product::find(1);
$product->image = $file;
$product->save();

...

$product->update([
    'gallery' => [$file1],
]);
```

