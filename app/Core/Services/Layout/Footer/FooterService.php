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

            $data['logo'] = $image['url'];
            $data['logo_public_id'] = $image['public_id'];
        }

        return $this->repository->updateBranding($data);
    }

    public function updateNewsletter(array $data)
    {
        if (
            isset($data['button_icon']) &&
            $data['button_icon'] instanceof UploadedFile
        ) {

            $image = $this->uploadService->uploadImage(
                $data['button_icon'],
                'cypress/footer/newsletter'
            );

            $data['button_icon'] = $image['url'];
        }

        return $this->repository->updateNewsletter($data);
    }
    public function updateSocial(array $data)
    {
        foreach ($data['socials'] as $index => $social) {

            if (
                isset($social['icon']) &&
                $social['icon'] instanceof UploadedFile
            ) {

                $image = $this->uploadService->uploadImage(
                    $social['icon'],
                    'cypress/footer/socials'
                );

                $data['socials'][$index]['icon'] = $image['url'];
                $data['socials'][$index]['icon_public_id'] = $image['public_id'];
            }
        }

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
    public function getNavigation()
    {
        return $this->repository->getNavigation();
    }
    public function getNewsletter()
    {
        return $this->repository->getNewsletter();
    }
    public function getBottomBar()
    {
        return $this->repository->getBottomBar();
    }
}
