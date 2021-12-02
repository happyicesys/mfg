<?php

namespace App\Traits;

use Livewire\WithFileUploads;
use Storage;

trait HasAttachment
{
    use WithFileUploads;

    public function createAttachment($model, $file, $dir)
    {
        $url = $file->storePublicly($dir, 'digitaloceanspaces');
        $fullUrl = Storage::url($url);
        $model->attachments()->create([
            'url' => $url,
            'full_url' => $fullUrl,
        ]);
    }

    public function deleteAttachment(Attachment $attachment)
    {
        $deleteFile = Storage::disk('digitaloceanspaces')->delete($attachment->url);
        if($deleteFile){
            $attachment->delete();
        }
    }

    public function downloadAttachment(Attachment $attachment)
    {
        return Storage::disk('digitaloceanspaces')->download($attachment->url);
    }
}
