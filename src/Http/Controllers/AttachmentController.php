<?php

namespace MOIREI\MediaLibrary\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use MOIREI\MediaLibrary\Api;
use MOIREI\MediaLibrary\AttachmentUpload;
use Illuminate\Support\Facades\Validator;
use MOIREI\MediaLibrary\Models\Attachment;

class AttachmentController extends Controller
{
    /**
     * Store an attachment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $upload = new AttachmentUpload(
            $request->file('file'),
        );

        if (!$upload->checkType())
            abort(422, __('Forbidden file format'));

        if (!$upload->checkSize())
            abort(422, __('File size limit exceeded') . $upload->filename);

        if (config('media-library.clean_file_name'))
            $upload->cleanFilename(config('media-library.clean_file_name.special_characters', false));

        $upload->location(Api::resolveLocation(config('media-library.attachments.location', 'attachments')));
        $upload->disk(config('media-library.attachments.disk', 'local'));

        $attachment = $upload->save();

        return response()->json([
            'id' => $attachment->id,
            'alt' => $attachment->alt,
            'url' => $attachment->url,
        ]);
    }

    /**
     * Purge persisted or pending attachment by URL.
     *
     * @param  string $url
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $url)
    {
        if (Api::isUuid($url)) {
            $attachment = Attachment::findOrFail($url);
        } else {
            $attachment = Attachment::where('url', $url)->firstOrFail();
        }
        return response()->json($attachment->purge());
    }
}
