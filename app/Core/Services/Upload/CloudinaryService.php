<?php

namespace App\Core\Services\Upload;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;

class CloudinaryService
{
    protected Cloudinary $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => config('services.cloudinary.cloud_name'),
                'api_key' => config('services.cloudinary.api_key'),
                'api_secret' => config('services.cloudinary.api_secret'),
            ],
            'url' => [
                'secure' => true,
            ],
        ]);
    }


    public function upload(
        UploadedFile $file,
        string $folder = 'cypress'
    ) {
        return $this->cloudinary
            ->uploadApi()
            ->upload(
                $file->getRealPath(),
                [
                    'folder' => $folder,
                ]
            );
    }


    public function delete(string $publicId): void
    {
        $this->cloudinary
            ->uploadApi()
            ->destroy($publicId);
    }
}
