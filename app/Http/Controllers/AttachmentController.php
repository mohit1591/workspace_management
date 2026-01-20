<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadAttachmentRequest;
use App\Models\Attachment;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function store(UploadAttachmentRequest $request, Item $item)
    {
        $file = $request->file('file');
        
        $path = $file->store('attachments', 'public');

        $attachment = Attachment::create([
            'workspace_id' => $item->workspace_id,
            'item_id' => $item->id,
            'uploaded_by' => $request->user()->id,
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        $attachment->load('uploader:id,name,email');

        return response()->json([
            'message' => 'File uploaded successfully',
            'attachment' => $attachment
        ], 201);
    }
}