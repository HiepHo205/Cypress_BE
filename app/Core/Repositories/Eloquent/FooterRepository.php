<?php

namespace App\Core\Repositories\Eloquent;

use App\Core\Models\Collection;
use App\Core\Models\Entry;
use App\Core\Models\EntryMeta;

class FooterRepository
{
    public function getFooter()
    {
        return Collection::where('api_endpoint', 'footer')
            ->firstOrFail()
            ->entries()
            ->with(['metas', 'childEntries.metas'])
            ->get();
    }

    public function getFooterBranding()
    {
        return $this->getEntry('branding')->load('metas');
    }

    public function updateBranding(array $data)
    {
        $entry = $this->getEntry('branding');

        foreach ($data as $key => $value) {
            EntryMeta::updateOrCreate(
                [
                    'entry_id' => $entry->id,
                    'meta_key' => $key,
                ],
                [
                    'meta_value' => $value,
                ]
            );
        }

        return $entry->load('metas');
    }

    public function updateNewsletter(array $data)
    {
        $entry = $this->getEntry('newsletter');

        foreach ($data as $key => $value) {
            EntryMeta::updateOrCreate(
                [
                    'entry_id' => $entry->id,
                    'meta_key' => $key,
                ],
                [
                    'meta_value' => is_array($value) ? json_encode($value) : $value,
                ]
            );
        }

        return $entry->load('metas');
    }

    public function updateSocial(array $data)
    {
        $entry = $this->getEntry('social');

        EntryMeta::updateOrCreate(
            [
                'entry_id' => $entry->id,
                'meta_key' => 'socials',
            ],
            [
                'meta_value' => json_encode($data),
            ]
        );

        return $entry->load('metas');
    }

    public function updateNavigation(array $data)
    {
        $entry = $this->getEntry('navigation');

        EntryMeta::updateOrCreate(
            [
                'entry_id' => $entry->id,
                'meta_key' => 'navigation',
            ],
            [
                'meta_value' => json_encode($data),
            ]
        );

        return $entry->load('metas');
    }

    public function updateBottomBar(array $data)
    {
        $entry = $this->getEntry('bottom-bar');

        foreach ($data as $key => $value) {
            EntryMeta::updateOrCreate(
                [
                    'entry_id' => $entry->id,
                    'meta_key' => $key,
                ],
                [
                    'meta_value' => is_array($value) ? json_encode($value) : $value,
                ]
            );
        }

        return $entry->load('metas');
    }

    private function getEntry(string $type): Entry
    {
        $collection = Collection::where('api_endpoint', 'footer')
            ->firstOrFail();

        $entry = $collection->entries()
            ->whereHas('metas', function ($query) use ($type) {
                $query->where('meta_key', 'type')
                    ->where('meta_value', $type);
            })
            ->first();

        if (!$entry) {
            $entry = Entry::create([
                'collection_id' => $collection->id,
                'status' => 'published',
            ]);

            EntryMeta::create([
                'entry_id' => $entry->id,
                'meta_key' => 'type',
                'meta_value' => $type,
            ]);
        }

        return $entry;
    }
}
