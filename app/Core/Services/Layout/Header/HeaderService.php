<?php

namespace App\Core\Services\Layout\Header;

use App\Core\Repositories\Eloquent\HeaderRepository;
use App\Core\Services\Upload\UploadService;
use Illuminate\Http\UploadedFile;

class HeaderService
{
    public function __construct(
        protected HeaderRepository $repository,
        protected UploadService $uploadService
    ) {}


    public function getHeader()
    {
        $entries = $this->repository->getHeader();

        $logo = null;
        $cta = null;
        $menus = [];


        foreach ($entries as $entry) {

            $meta = $entry->metas->pluck(
                'meta_value',
                'meta_key'
            );


            switch ($meta['type'] ?? null) {


                case 'logo':

                    $logo = $meta['image'] ?? null;

                    break;


                case 'cta':

                    $cta = [
                        'label' => $meta['label'] ?? null,
                        'href' => $meta['href'] ?? null,
                    ];

                    break;


                case 'menu':

                    $children = [];


                    foreach ($entry->childEntries as $child) {

                        $childMeta = $child->metas->pluck(
                            'meta_value',
                            'meta_key'
                        );


                        $children[] = [
                            'id' => $child->id,
                            'label' => $childMeta['label'] ?? null,
                            'href' => $childMeta['href'] ?? null,
                        ];
                    }


                    $menus[] = [
                        'id' => $entry->id,
                        'label' => $meta['label'] ?? null,
                        'children' => $children,
                    ];


                    break;
            }
        }


        return [
            'logo' => $logo,
            'menus' => $menus,
            'cta' => $cta,
        ];
    }


    public function createMenu(array $data)
    {
        return $this->repository->createMenu($data);
    }


    public function updateMenu(int $id, array $data)
    {
        return $this->repository->updateMenu(
            $id,
            $data
        );
    }


    public function deleteMenu(int $id)
    {
        return $this->repository->deleteMenu($id);
    }


    public function updateLogo(UploadedFile $logo)
    {
        $image = $this->uploadService->uploadImage(
            $logo,
            'cypress/header'
        );


        return $this->repository->updateLogo($image);
    }
}
