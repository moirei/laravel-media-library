<?php

return [

    /*
	|--------------------------------------------------------------------------
	| Attachment
	|--------------------------------------------------------------------------
	|
	| Configure file attachments for rich-text integration.
	*/
    'attachments' => [
        # disk to store
        'disk' => env('FILESYSTEM_DRIVER', 'public'),

        # location to store files. Relative to storage "folder" below.
        # Be sure not to use the folder for any other storage.
        'location' => '/.trix',

        # Set maximum upload image size
        'resize' => [1200, null, false, false],

        # whether or not to optimise uploads
        'optimize' => true,

        # Regex pattern to extract embedded richtext attachments
        'richtext_match' => [
            '/<img.*src="(.*?)"/s', # match html img tags
        ],

        # Allowed types
        'types' => [
            'video' => ['mp4'],
            'image' => ['*'],
        ],
    ],

    /*
	|--------------------------------------------------------------------------
	| Clean Ups
	|--------------------------------------------------------------------------
	|
	| Here, specify automatic deletion of unwanted files
	| You can specify file/folder age in days for each type. Example `expired-shareables:29`
	| Available clean types:
	| `empty-folders` - Empty folders with no files
	| `lonely-files` - Files associated with no models
	| `expired-shareables` - Expired shared content
	| `attachments` - Pending attachments
	*/
    'clean_ups' => [
        'schedule' => 'weekly',
        'clean' => [
            'empty-folders',
            'lonely-files:21',
            'attachments:1',
        ],
        'enabled' => false,
    ],

    /*
	|--------------------------------------------------------------------------
	| Clean File Names
	|--------------------------------------------------------------------------
	|
	| Indicate to retouch filename. This is the name used in file path/urls.
	| Recomended when storing PUBLIC images to avoid broken urls in <img> tags with whitespaces or special chars.
	| Set `clean_file_name` to false to disable
	*/
    'clean_file_name' => [
        'replace_spaces' => '-',
        'special_characters' => false,
    ],

    /*
	|--------------------------------------------------------------------------
	| Filesystem Disks (default)
	|--------------------------------------------------------------------------
	|
	| Used to create a folder or file if none was specified during uploads
	| Accepts all Laravel "Supported Drivers"
	| Files/folder and folder creation within an existing folder will however inherit its disk. Unless otherwise specified.
	*/
    'disk' => env('FILESYSTEM_DRIVER', 'public'),

    /*
	|--------------------------------------------------------------------------
	| Media Folder
	|--------------------------------------------------------------------------
	|
	| Specify a folder in which all media files should live.
	| For any used disk, this folder is created.
	*/
    'folder'  => 'media',

    /*
	|--------------------------------------------------------------------------
	| File Thumbnails
	|--------------------------------------------------------------------------
	|
	| Here you can speficy which non-image files to generate preview thumbnails for.
	| Does not apply to image files.
	|
	| Currenly not implemented!!
	*/
    'file_thumbs' => [

        # Crop additional image variations [ width, height, upSize, upWH ]
        'sizes' => [
            'xsmall' => [38, 38, true, false],
            'small' => [100, 100, true, false],
            'thumb' => [270, 270, true, false],
        ],

        # Allowed types
        'types' => [
            'video' => ['mp4'],
            'audio' => ['*'],
        ],
    ],

    /*
	|--------------------------------------------------------------------------
	| Imaging
	|--------------------------------------------------------------------------
	|
	| Configure image behaviour
	*/
    'images' => [

        # Auto optimise uploaded images
        # Currenly not implemented!!
        'optimize' => true,

        # Placeholder for pending or non-image files with image field
        'placeholder' => 'https://via.placeholder.com/32',

        # Allow you to resize original images by width\height. Using http:#image.intervention.io library.
        # Width and height can be integer or null. If one of them is null - will resize image proportionally.
        # supports image formats: http:#image.intervention.io/getting_started/formats.
        'resize' => [

            # `gd` or `imagick`
            'driver' => 'gd',

            # 0 - 100
            'quality' => 80,

            # Maximum width and height in pixels for the original image [ width, height, upSize, upWH ]
            # upSize {bool} - Crop image even if size will be larger. (If set to `false` - size image will be as original).
            # upWH {bool} - Crop even if width and height image less than limits.
            'original' => [1200, null, false, false],

            # Crop additional image variations [ width, height, upSize, upWH ]
            'sizes' => [
                'xsmall' => [38, 38, true, false],
                'small' => [100, 100, true, false],
                'thumb' => [270, 270, true, false],
                'medium' => [572, 572, true, true],
                'large' => [800, null, true, true],
            ],

            # One of the above keys in `sizes` to use as image image
            # Also used by the package
            'thumb' => 'thumb',
        ]
    ],

    /*
	|--------------------------------------------------------------------------
	| Size Limits
	|--------------------------------------------------------------------------
	|
	| Maximum upload size for each type
	| Add `Label` => `max_size` in bytes for needed types to enable limitation
	| If you want to disable the limitation - leave empty array
	*/
    'max_size' => [
        'image' => 2097152,
        'docs' => 5242880,
        # '*' => 5242880,
    ],

    /*
	|--------------------------------------------------------------------------
	| Models
	|--------------------------------------------------------------------------
	|
	| Media library models. Specify other classes for customized behaviour.
	*/
    'models' => [
        # The media library file model
        'file' => MOIREI\MediaLibrary\Models\File::class,

        # The media library folder model
        'folder' => MOIREI\MediaLibrary\Models\Folder::class,

        # The shared content model
        'shared' => MOIREI\MediaLibrary\Models\SharedContent::class,
    ],

    /*
	|--------------------------------------------------------------------------
	| Privacy (default)
	|--------------------------------------------------------------------------
	|
	| Controls if files/folders are public vs private
	*/
    'private' => false,

    /*
	|--------------------------------------------------------------------------
	| Route Configuration
	|--------------------------------------------------------------------------
	|
	| Configure the route group parameters.
	| Recommend setting auth or guarding middleware for protected routes
	| Additional configuration for the route group https:#lumen.laravel.com/docs/routing#route-groups
	*/
    'route' => [

        # Default route prefix
        # Set the endpoint prefix to which the media-library server responds.
        # example  `yourdomain.com/media`
        'prefix' => 'media-library',

        # Subdomain routing
        # see https:#laravel.com/docs/8.x/routing#route-group-subdomain-routing
        # 'domain' => 'media.yourdomain.com',

        # Apply middlewares to routes.
        # String values apply to all routes
        # Key-value pairs may take string or array as value
        'middleware' => [
            'throttle:60,1',
            # 'throttle:uploads',
            # 'media.protected' => ['auth']
            # 'file' => 'guest',
            # 'file.protected' => ['auth'],
        ],

        # Route name
        'name' => 'media.',

        # Disable all package routes and middleware
        'disabled' => false,
    ],

    /*
	|--------------------------------------------------------------------------
	| Shared Content (defaults)
	|--------------------------------------------------------------------------
	|
	| Configure the default behaviour of shared files and folders.
	*/
    'shared_content' => [

        # UI
        'ui' => [
            'title' => 'Media Library',
            'auth_page_links' => [
                ['title' => 'Contact us', 'href' => '/contact'],
            ],
        ],

        # Share defaults
        'defaults' => [
            # Make shared content publicly accessible to anyone
            'public' => false,

            # Limit the maximum downloads of a shared content
            # Set to 0 to unlimit
            'max_downloads' => 0,

            # Allow users to upload into a shared folder
            'can_upload' => false,

            # Limit the maximum upload size
            # Set to null to unlimit
            'max_upload_size' => 5242880,

            # The allowed file type the user can upload
            'allowed_upload_types' => [
                'image' => ['jpg', 'jpeg', 'png', 'gif', 'svg'],
                'docs' => ['*'],
            ],

            # If the shared content can be removed by the user
            'can_remove' => true,

            # The number of days after creation to automatically expire shared content
            # Set to null to disable
            'expire_after' => 7,
        ],
    ],

    /*
	|--------------------------------------------------------------------------
	| File Types
	|--------------------------------------------------------------------------
	|
	| Allowed files by types and extensions
	| Format: Label => ['array', 'of', 'extensions']
	| example ['*'] - save any file extensions to the specified type
	*/
    'types' => [
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'svg'],
        'docs' => ['doc', 'xls', 'docx', 'xlsx', 'pdf'],
        'audio' => ['mp3'],
        'video' => ['mp4'],
        # 'application' => ['gzip', 'json'],
        #'other' => ['*'],
    ],

    /*
	|--------------------------------------------------------------------------
	| Known Document File Types
	|--------------------------------------------------------------------------
	|
	| Known document types, a.k.a `application` types.
	| Files with these extensions are saves as type `docs`, not `application`
	*/
    'doc_types' => [
        'doc', 'docx', 'docm', 'dotx', 'dotm', 'docb', # word
        'xls', 'xlsx', 'xlsm', 'xltx', 'xltm', # excel
        'ppt', 'pptx', 'pptm', 'potx', 'potm', 'ppam', 'ppsx', 'ppsm', 'sldx', 'sldm', # power point
        'pdf', 'one',
    ]
];
