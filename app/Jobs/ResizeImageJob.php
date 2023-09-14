<?php

namespace App\Jobs;

use App\Models\Upload;
use App\Services\UploadService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ResizeImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $product;

    /**
     * Create a new job instance.
     */
    public function __construct($product)
    {
        $this->product = $product;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->product->images as $image) {
            $resizedImage = Image::make(UploadService::getUrl($image->id))->resize(500, null, function ($constraint) {
                $constraint->aspectRatio();
            });

            $uploadedPath =  Str::uuid()->toString() . '.' . $image->extension;
            $upload = Storage::disk($image->disk)->put($uploadedPath, $resizedImage->stream());
    
            if ($upload) {
                $this->product->images()->create([
                    'path'      => $uploadedPath,
                    'extension' => $image->extension,
                    'type'      => Upload::TYPE_RESIZE,
                    'disk'      => $image->disk,
                ]);
            }
        }
    }
}
