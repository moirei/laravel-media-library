# API

## Browse Files

Get a list folders and files in a location.

```php
use MOIREI\MediaLibrary\Api;
...

// Api::browse(...) returns collection
$items = Api::browse('/accessories/chargers/')->toArray();

// Specify namespace and disk
$items = Api::browse('/chargers', 'MOIREI', 'local')->toArray();
```

## Creating Folders
```php
// Get folder if it exists
$folder = Api::resolveFolder(Api::joinPaths('accessories', 'chargers'));

// Get folder or create one if it doesn't exist
$folder = Api::assertFolder('accessories/chargers');
```



## Moving Files & Folders

**Note**: The `location` field must not be updated directly. Trying to do so will throw and `MOIREI\MediaLibrary\Exceptions\MediaLocationUpdateException` exception.



### Move Files

```php
use MOIREI\MediaLibrary\Api;
use MOIREI\MediaLibrary\Models\File;
use MOIREI\MediaLibrary\Models\Folder;
...

[$file] = File::all();
[$folder1, $folder2] = Folder::all();

// Use a folder model
Api::move($file, $folder1);
$folder1->fresh();
dump($folder1->toArray()); // the files field should contain $file

// Use a path name, relative to your `folder` config
// Asserts the path and creates all the directories if it doesn't exist
Api::move($file, 'products/chargers');
// Api::move($file, 'products/chargers', $namespace);

$folder = Api::resolveFolder('products/chargers');
// $folder = Api::resolveFolder('products/chargers', $namespace);

dump($folder->toArray()); // the files field should contain $file
```



### Move Folders

```php
[$folder1, $folder2] = Folder::all();

// Use a folder model
Api::moveFolder($folder1, $folder2);
$folder2->fresh();
dump($folder2->toArray()); // the files field should contain $folder1

// Asserts the path and creates all the directories if it doesn't exist
Api::moveFolder($folder1, 'products/chargers');
// Api::moveFolder($folder1, 'products/chargers', $namespace);
$folder = Api::resolveFolder('products/accessories');
// $folder = Api::resolveFolder('products/accessories', $namespace);
dump($folder->toArray()); // the folders field should contain $folder1
```



## Facades

| Class                                            | Description                                                  |
| ------------------------------------------------ | ------------------------------------------------------------ |
| `MOIREI\MediaLibrary\Facades\MediaApi`           | For handling and validating file uploads and folder resolution. |
| `MOIREI\MediaLibrary\Facades\MediaAttachmentApi` | For handling and validating attachment uploads.              |



## Artisan Commands

All commands accept  the following arguments:

| Flag        | Description                                           |
| ----------- | ----------------------------------------------------- |
| `--dry-run` | List items that will be removed without removing them |
| `--force`   | Force operation in production environment             |

**Clean empty folders**

The `days` argument is optional; limit results after the days

```bash
php artisan media:clean:empty-folders --days=7
```

**Clean lonely files**

The `days` argument is optional; limit results after the days

```bash
php artisan media:clean:lonely-files --days=7
```

**Expired shared content**

```bash
php artisan media:clean:expired-shareables
```

**Pending attachment**

The `days` argument is optional; limit results after the days

```bash
php artisan media:clean:attachments --days=7
```



**Run all clean commands**

Attempts to run all the above commands according your `clean_ups` configuration.

```bash
php artisan media:clean
```





