<?php

namespace App\Core\Services\Layout\Footer;

use App\Core\Repositories\Eloquent\FooterRepository;
use App\Core\Services\Upload\UploadService;
use Illuminate\Http\UploadedFile;

class FooterService
{
    public function __construct(
        protected FooterRepository $repository,
        protected UploadService $uploadService
    ) {}


    public function getFooter()
    {
        return $this->repository->getFooter();
    }

    public function getFooterBranding()
    {
        return $this->repository->getFooterBranding();
    }
    public function updateBranding(array $data)
    {

        if (
            isset($data['logo']) &&
            $data['logo'] instanceof UploadedFile
        ) {

            $image = $this->uploadService->uploadImage(
                $data['logo'],
                'cypress/footer'
            );


            $data['logo'] = $image;
        }


        return $this->repository->updateBranding($data);
    }


    public function updateNewsletter(array $data)
    {
        return $this->repository->updateNewsletter($data);
    }


    public function updateSocial(array $data)
    {
        return $this->repository->updateSocial($data);
    }


    public function updateNavigation(array $data)
    {
        return $this->repository->updateNavigation($data);
    }


    public function updateBottomBar(array $data)
    {
        return $this->repository->updateBottomBar($data);
    }
}
