<?php

namespace App\Modules\Admin\GraphQL\Mutations;

use App\Core\Services\Home\HomepageService;

class HomepageMutation
{
    protected HomepageService $homepageService;

    public function __construct(
        HomepageService $homepageService
    ) {
        $this->homepageService = $homepageService;
    }


    public function updateHomepageSection(
        $_,
        array $args
    ) {

        $input = json_decode(
            json_encode($args['input']),
            true
        );

        return $this->homepageService->updateSection(
            $args['section'],
            $input,
            $args['image'] ?? null
        );
    }


public function updateHomepageItem(
    $_,
    array $args
) {

    $input = [];

    if (isset($args['input'])) {
        $input = json_decode(
            json_encode($args['input']),
            true
        );
    }


    $uploadFields = null;

    if (isset($args['uploadFields'])) {

        if (is_string($args['uploadFields'])) {

            $uploadFields = json_decode(
                $args['uploadFields'],
                true
            );

        } else {

            $uploadFields = json_decode(
                json_encode($args['uploadFields']),
                true
            );
        }
    }

    return $this->homepageService->updateItem(
        $args['section'],
        $args['field'],
        $input,
        $uploadFields,
        $args['folder'] ?? null,
        $args['image'] ?? null,
        $args['logo'] ?? null
    );
}
    public function deleteHomepageItem(
        $_,
        array $args
    ): bool {

        return $this->homepageService->deleteItem(
            $args['section'],
            $args['field'],
            $args['id']
        );
    }
}
