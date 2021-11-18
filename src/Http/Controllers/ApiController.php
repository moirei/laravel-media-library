<?php

namespace MOIREI\MediaLibrary\Http\Controllers;

use App\Rules\SharedContentTypes;
use App\Rules\StorageDisk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use MOIREI\MediaLibrary\Api;
use MOIREI\MediaLibrary\Upload;
use Illuminate\Support\Facades\Storage;
use MOIREI\MediaLibrary\Models\SharedContent;

class ApiController extends Controller
{
    /**
     * Get file
     *
     * @param Model $file
     * @param int|string||null $width
     * @param int|string||null $height
     * @return \Illuminate\Http\Response
     */
    public function get(Model $file, int | string $width = null, int | string $height = null)
    {
        if (($file->type == 'image') && ($width || $height)) {
            $content = Api::getDynamicSize($file, $width, $height);
        } else {
            $content = Storage::disk($file->disk)->get(Api::joinPaths($file->location, $file->id, $file->filename));
        }

        return response($content, 200)
            ->header('Content-Type', $file->mimetype)
            ->header('Content-Disposition', 'inline');
    }

    /**
     * Get file but ensure public
     *
     * @param Model $file
     * @param int|string||null $width
     * @param int|string||null $height
     * @return \Illuminate\Http\Response
     */
    public function getPublic(Model $file, int | string $width = null, int | string $height = null)
    {
        if ($file->private) {
            abort(401);
        }

        return $this->get($file, $width, $height);
    }

    /**
     * Doanload file
     *
     * @param Model $file
     * @return \Illuminate\Http\Response
     */
    public function download(Model $file)
    {
        $path = Api::joinPaths($file->location, $file->id, $file->filename);
        $content = Storage::disk($file->disk)->get($path);

        return response()->make($content, 200, [
            'Content-Type'          => $file->mimetype,
            'Content-Length'        => Storage::disk($file->disk)->size($path),
            'Cache-Control'         => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Disposition'   => "attachment; filename=\"$file->filename\"",
            'Pragma'                => 'public',
        ]);
    }

    /**
     * Doanload file but ensure public
     *
     * @param Model $file
     * @return \Illuminate\Http\Response
     */
    public function downloadPublic(Model $file)
    {
        if ($file->private) {
            abort(401);
        }

        return $this->download($file);
    }

    /**
     * Stream file
     *
     * @param Model $file
     * @return \Illuminate\Http\Response
     */
    public function stream(Model $file)
    {
        $path = Api::joinPaths($file->location, $file->id, $file->filename);
        return response()->stream(function () use ($file, $path) {
            $stream = Storage::disk($file->disk)->readStream($path);
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type'          => $file->mimetype,
            'Content-Length'        => Storage::disk($file->disk)->size($path),
        ]);
    }

    /**
     * Stream file but ensure public
     *
     * @param Model $file
     * @return \Illuminate\Http\Response
     */
    public function streamPublic(Model $file)
    {
        if ($file->private) {
            abort(401);
        }

        return $this->stream($file);
    }

    /**
     * Upload file
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'name' => 'max:64',
            'location' => 'max:128',
            'description' => 'max:1024',
            'private' => 'boolean',
            'disk' => StorageDisk::class,
        ]);

        $upload = new Upload(
            $request->file('file'),
        );

        if (!$upload->checkType())
            abort(422, __('Forbidden file format'));

        if (!$upload->checkSize())
            abort(422, __('File size limit exceeded') . $upload->filename);

        if (config('media-library.clean_file_name'))
            $upload->cleanFilename(config('media-library.clean_file_name.special_characters', false));

        $location = $request->get('location', '/');
        $namespace = $request->header('Namespace');

        $upload->location($location, $namespace);

        if ($request->has('name')) {
            $upload->name($request->get('name'));
        }
        if ($request->has('description')) {
            $upload->description($request->get('description'));
        }
        if ($request->has('private')) {
            $upload->private($request->get('private'));
        }
        if ($request->has('disk')) {
            $upload->disk($request->get('disk'));
        }

        return response()->json(
            $upload->save()
        );
    }

    /**
     * Update file
     *
     * @param  \Illuminate\Http\Request $request
     * @param Model $file
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Model $file)
    {
        $request->validate([
            'name' => 'max:64',
            'description' => 'max:1024',
            'private' => 'boolean',
        ]);

        $file->update(
            $request->only(['name', 'description', 'private'])
        );
        $file->fresh();

        return response()->json($file);
    }

    /**
     * Move file
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Model $file
     * @return \Illuminate\Http\JsonResponse
     */
    public function move(Request $request, Model $file)
    {
        $request->validate([
            'location' => 'required|string'
        ]);

        $location = $request->get('location');
        $namespace = $request->header('Namespace');

        if (Api::isUuid($location)) {
            $folderClass = config('media-library.models.folder');
            $location = $folderClass::findOrFail($location);
            Api::move($file, $location);
        } else {
            Api::move($file, Api::joinPaths($namespace, $location));
        }

        $file->fresh();

        return response()->json($file);
    }

    /**
     * Delete file
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Model $file
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, Model $file)
    {
        $request->validate([
            'force' => 'boolean'
        ]);

        if ($request->get('force', false)) {
            $file->forceDelete();
        } else {
            $file->delete();
        }

        return response()->json($file);
    }

    /**
     * Create Folder
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function folderCreate(Request $request)
    {
        $request->validate([
            'name' => 'required|max:64',
            'location' => 'max:128',
            'description' => 'max:1024',
            'private' => 'boolean',
            'disk' => StorageDisk::class,
        ]);

        $location = $request->get('location', '/');
        $namespace = $request->header('Namespace');
        $folderClass = config('media-library.models.folder');


        if (Api::isUuid($location)) {
            $parent = $folderClass::findOrFail($location);
            // TODO: check folder namespace
        } else {
            $parent = Api::assertFolder(Api::joinPaths($namespace, $location));
        }

        if ($parent) {
            $path = Api::joinPaths($parent->location, $parent->name, $request->get('name'));
        } else {
            $path = Api::joinPaths($namespace, $location, $request->get('name'));
        }

        $folder = Api::assertFolder($path, !!$parent, $request->get('disk'));

        $data = $request->only(['description', 'private']);
        if (!empty($data)) {
            $folder->update($data);
            $folder->fresh();
        }

        return response()->json($folder);
    }

    /**
     * Update Folder
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Model $folder
     * @return \Illuminate\Http\JsonResponse
     */
    public function folderUpdate(Request $request, Model $folder)
    {
        $request->validate([
            'name' => 'max:64',
            'location' => 'max:128',
            'description' => 'max:1024',
            'private' => 'boolean',
        ]);

        $folder->update(
            $request->only(['name', 'description', 'private'])
        );
        $folder->fresh();

        return response()->json($folder);
    }

    /**
     * Move Folder
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Model $folder
     * @return \Illuminate\Http\JsonResponse
     */
    public function folderMove(Request $request, Model $folder)
    {
        $request->validate([
            'location' => 'required|string'
        ]);

        $location = $request->get('location');
        $namespace = $request->header('Namespace');

        if (Api::isUuid($location)) {
            $folderClass = config('media-library.models.folder');
            $location = $folderClass::findOrFail($location);
            // TODO: check folder namespace
            Api::moveFolder($folder, $location);
        } else {
            Api::moveFolder($folder, Api::joinPaths($namespace, $location));
        }

        $folder->fresh();

        return response()->json($folder);
    }

    /**
     * Delete Folder
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Model $folder
     * @return \Illuminate\Http\JsonResponse
     */
    public function folderDelete(Request $request, Model $folder)
    {
        $request->validate([
            'force' => 'boolean'
        ]);

        if ($request->get('force', false)) {
            $folder->forceDelete();
        } else {
            $folder->delete();
        }

        return response()->json($folder);
    }

    /**
     * Browse file location
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function browse(Request $request)
    {
        $request->validate([
            'location' => 'required|string'
        ]);

        $location = $request->get('location');
        $namespace = $request->header('Namespace');

        return response()->json(
            Api::browse($location, $namespace)
        );
    }

    /**
     * Get file downloadable link
     *
     * @param Model $file
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function downloadableLink(Request $request, Model $file)
    {
        $min_ttl = 60 * 30; // 30 mins
        $request->validate([
            'ttl' => "integer|min:$min_ttl",
        ]);
        $ttl = now()->addSeconds($request->get('ttl', $min_ttl));

        return response()->json([
            'url' => Api::getDowloadUrl($file, $ttl)
        ]);
    }

    /**
     * Get file shareable link
     *
     * @param Model $file
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function shareablebleLink(Request $request, Model $file)
    {
        $request->validate([
            'name' =>                 'string|max:64',
            'description' =>          'string|max:1024',
            'public' =>               'boolean',
            'access_emails' =>        'array|min:1',
            "access_emails.*"  =>     'distinct|email',
            'access_type' =>          SharedContentTypes::class,
            "access_keys"    =>       'exclude_if:public,true|required|array|min:1',
            "access_keys.*"  =>       'exclude_if:public,true|required|string|distinct|min:6',
            'expires_at' =>           'date|after:today',
            'can_remove' =>           'boolean',
            'can_upload' =>           'boolean',
            'max_downloads' =>        'integer|min:1',
            'max_upload_size' =>      'integer|min:1000',
            'allowed_upload_types' => 'array',
            // 'meta' => 'json',
        ]);

        $shareable = SharedContent::make($file);
        $shareable->fill(
            $request->only([
                'name', 'description',
                'public', 'access_emails', 'access_type', 'access_keys',
                'expires_at', 'can_remove', 'can_upload',
                'max_downloads', 'max_upload_size', 'allowed_upload_types',
            ])
        );

        $shareable->save();
        $shareable->fresh();

        return response()->json([
            'id' => $shareable->id,
            'url' => $shareable->url(),
        ]);
    }

    /**
     * Get folder shareable link
     *
     * @param Model $folder
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function shareablebleFolderLink(Request $request, Model $folder)
    {
        return $this->shareablebleLink($request, $folder);
    }
}
