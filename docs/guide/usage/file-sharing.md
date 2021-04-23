# Sharing Files

Apart from generating shareable urls to media files, your may want the ability to share files with your users dropbox or Google Drive style.

This package allows you to share files with your application users with ease. Users do not need a registered account to access files.

```php
use MOIREI\MediaLibrary\Models\SharedContent;
...

$file = File::find('file-id');
$shareable = SharedContent::make($file)
    		->downloads(2) // users can only download file(s) twice
    		->save();

// share a folder

$folder = Folder::find('file-id');
$shareable = SharedContent::make($folder)
            ->access('access-code') // secure with an access code
    		->save();
```

Since `SharedContent` is a `Model`, you can also fill it with an array
```php
$shareable->fill(
    $request->only([
        'name', 'description',
        'public', 'access_emails', 'access_type', 'access_keys',
        'expires_at', 'can_remove', 'can_upload',
        'max_downloads', 'max_upload_size', 'allowed_upload_types',
    ])
);
```


**Get the shareable url**

```php
$url = $shareable->url();
```

Visit url for a visual access to shared file(s);



### Share Files

```php
use MOIREI\MediaLibrary\Models\File;
...

$shareable = File::find('file-id')->share()->save();
```



### Share Folders

```php
use MOIREI\MediaLibrary\Models\Folder;
...

$folder = Folder::find('folder-id');
$shareable = $folder->share()
            ->uploadSize(1000000) // limit upload to 1MB
	        ->canUpload()
            ->uploadTypes([
                'image' => ['png', 'jpg'],
                'docs' => ['*'],
            ])
            ->save();
```



## Limiting Access

If secrete, provided codes are hashed before saved.

```php
$access_type = 'secret'; // `secret` or `token`
$shareable->access($access_type, [
        'code-1',
        'code-2'
    ])
	->canUpload()
    ->uploadTypes('image/png', 'video/*')
    ->allow($user1, $user2)
    ->deny([$admin1, $user3])
    ->save();
```



## Methods

The following methods are chainable.

| Method                           | Description                                                  |
| -------------------------------- | ------------------------------------------------------------ |
| `access(...)`                    | Limit access with codes or passwords. Pass array of codes/passwords or the access `type` and then an array of codes/passwords. |
| `accessType(string $type)`       | Explicitly set the access code type.                         |
| `email(string|array ...$emails)` | Further limit access by emails User must provide this email to access shared files |
| `canUpload(bool $value = true)`  | Whether the user can upload further content into the shared folder |
| `canRemove(bool $value = true)`  | Whether the user can remove items in the shared folder       |
| `downloads(int $value)`          | Limit the amount of downloads the user gets                  |
| `uploadSize(int $value)`         | Limit the total maximum upload size. Automatically enables uploads.<br />In bytes. |
| `allow(Model|array ...$users)`   | Model instances (users) allowed to access this shared content. Access is will be denided even if authenticated. |
| `deny(Model|array ...$users)`    | Model instances (users) **NOT** allowed to access this shared content. Access is will be denided even if authenticated. |

