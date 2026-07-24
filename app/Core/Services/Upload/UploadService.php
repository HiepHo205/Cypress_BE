<?php

namespace App\Core\Services\Upload;

use Illuminate\Http\UploadedFile;

class UploadService
{
    public function __construct(
        protected CloudinaryService $cloudinary
    ) {}


    public function uploadImage(
        UploadedFile $file,
        string $folder
    ): array {

        $result = $this->cloudinary->upload(
            $file,
            $folder
        );


        return [
            'url' => $result['secure_url'],
            'public_id' => $result['public_id'],
        ];
    }


    public function replaceImage(
        UploadedFile $file,
        ?string $oldPublicId,
        string $folder
    ): array {

        if (!empty($oldPublicId)) {
            $this->cloudinary->delete($oldPublicId);
        }


        return $this->uploadImage(
            $file,
            $folder
        );
    }


    public function deleteImage(
        ?string $publicId
    ): void {

        if (!empty($publicId)) {
            $this->cloudinary->delete($publicId);
        }
    }
}