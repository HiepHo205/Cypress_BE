<?php

namespace App\Core\Repositories\Eloquent;

use App\Core\Models\Collection;
use App\Core\Models\Entry;
use App\Core\Models\EntryMeta;
use Illuminate\Support\Str;

class FooterRepository
{
    protected function getCollection(): Collection
    {
        return Collection::firstOrCreate(
            [
                'api_endpoint' => 'footer',
            ],
            [
                'collection_name' => 'Footer',
                'is_system_type' => true,
            ]
        );
    }
    protected function getOrCreateEntry(string $type): Entry
    {
        $collection = $this->getCollection();

        $entry = $collection->entries()
            ->whereHas('metas', function ($query) use ($type) {
                $query->where('meta_key', 'type')
                    ->where('meta_value', $type);
            })
            ->first();

        if (!$entry) {

            $entry = $collection->entries()->create([
                'status' => 'published',
            ]);

            $entry->metas()->create([
                'meta_key' => 'type',
                'meta_value' => $type,
            ]);
        }

        return $entry;
    }

    public function getFooter()
    {
        return $this->getCollection()
            ->entries()
            ->with([
                'metas',
                'childEntries.metas'
            ])
            ->get();
    }
    public function getFooterBranding()
    {
        return $this->getOrCreateEntry('branding')
            ->load('metas');
    }

    public function updateBranding(array $data)
    {
        $entry = $this->getOrCreateEntry('branding');

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

        $entry->load('metas');

        return [
            'company_name' => $entry->metas
                ->where('meta_key', 'company_name')
                ->first()?->meta_value,

            'description' => $entry->metas
                ->where('meta_key', 'description')
                ->first()?->meta_value,
        ];
    }

    public function updateNewsletter(array $data)
    {
        $entry = $this->getOrCreateEntry('newsletter');

        foreach ($data as $key => $value) {

            EntryMeta::updateOrCreate(
                [
                    'entry_id' => $entry->id,
                    'meta_key' => $key,
                ],
                [
                    'meta_value' => is_array($value)
                        ? json_encode($value)
                        : $value,
                ]
            );
        }

        $entry->load('metas');

        $result = [];

        foreach ($entry->metas as $meta) {
            $result[$meta->meta_key] = $meta->meta_value;
        }

        return [
            'id' => (string) $entry->id,
            'title' => $result['title'] ?? null,
            'description' => $result['description'] ?? null,
            'placeholder' => $result['placeholder'] ?? null,
            'button_icon' => $result['button_icon'] ?? null,
        ];
    }
    public function updateSocial(array $data)
    {
        $entry = $this->getOrCreateEntry('social');

        $meta = EntryMeta::where([
            'entry_id' => $entry->id,
            'meta_key' => 'socials',
        ])->first();

        $oldSocials = [];

        if ($meta) {
            $oldData = json_decode($meta->meta_value, true);
            $oldSocials = $oldData['socials'] ?? [];
        }

        $socials = [];

        foreach ($data['socials'] ?? [] as $item) {

            $id = $item['id'] ?? Str::uuid()->toString();

            $oldItem = collect($oldSocials)->firstWhere('id', $id);

            $socials[] = [
                'id' => $id,
                'name' => $item['name'] ?? ($oldItem['name'] ?? ''),
                'url' => $item['url'] ?? ($oldItem['url'] ?? ''),
                'icon' => isset($item['icon']) && is_string($item['icon'])
                    ? $item['icon']
                    : ($oldItem['icon'] ?? null),
            ];
        }

        EntryMeta::updateOrCreate(
            [
                'entry_id' => $entry->id,
                'meta_key' => 'socials',
            ],
            [
                'meta_value' => json_encode([
                    'socials' => $socials,
                ]),
            ]
        );

        return [
            'socials' => $socials,
        ];
    }
    public function updateNavigation(array $data)
    {
        $entry = $this->getOrCreateEntry('navigation');

        $navigations = collect($data['navigations'] ?? [])
            ->map(function ($navigation) {
                if (empty($navigation['id'])) {
                    $navigation['id'] = (string) Str::uuid();
                }

                return $navigation;
            })
            ->values()
            ->toArray();

        EntryMeta::updateOrCreate(
            [
                'entry_id' => $entry->id,
                'meta_key' => 'navigation',
            ],
            [
                'meta_value' => json_encode([
                    'navigations' => $navigations,
                ]),
            ]
        );

        return [
            'id' => (string) $entry->id,
            'navigations' => $navigations,
        ];
    }
    public function updateBottomBar(array $data)
    {
        $entry = $this->getOrCreateEntry('bottom-bar');

        foreach ($data as $key => $value) {

            EntryMeta::updateOrCreate(
                [
                    'entry_id' => $entry->id,
                    'meta_key' => $key,
                ],
                [
                    'meta_value' => is_array($value)
                        ? json_encode($value)
                        : $value,
                ]
            );
        }

        $entry->load('metas');

        $meta = $entry->metas->pluck(
            'meta_value',
            'meta_key'
        );

        return [
            'id' => (string) $entry->id,
            'copyright' => $meta->get('copyright', ''),
            'legal_links' => json_decode(
                $meta->get('legal_links', '[]'),
                true
            ) ?? [],
        ];
    }
    public function getNavigation()
    {
        $entry = $this->getOrCreateEntry('navigation');

        $meta = EntryMeta::where([
            'entry_id' => $entry->id,
            'meta_key' => 'navigation',
        ])->first();

        if (!$meta) {
            return [
                'id' => (string) $entry->id,
                'navigations' => [],
            ];
        }

        $data = json_decode($meta->meta_value, true);

        return [
            'id' => (string) $entry->id,
            'navigations' => $data['navigations'] ?? [],
        ];
    }
    public function getNewsletter()
    {
        $entry = $this->getOrCreateEntry('newsletter');

        $entry->load('metas');

        $data = [];

        foreach ($entry->metas as $meta) {
            $data[$meta->meta_key] = $meta->meta_value;
        }

        return [
            'id' => (string) $entry->id,
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'placeholder' => $data['placeholder'] ?? null,
            'button_icon' => $data['button_icon'] ?? null,
        ];
    }
    public function getBottomBar()
    {
        $entry = $this->getOrCreateEntry('bottom-bar');

        $entry->load('metas');

        $data = [];

        foreach ($entry->metas as $meta) {
            $data[$meta->meta_key] = $meta->meta_value;
        }

        return [
            'id' => (string) $entry->id,
            'copyright' => $data['copyright'] ?? '',
            'legal_links' => isset($data['legal_links'])
                ? json_decode($data['legal_links'], true)
                : [],
        ];
    }
}
