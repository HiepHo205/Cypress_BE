<?php

namespace App\Core\Repositories\Eloquent;

use App\Core\Models\Collection;
use App\Core\Models\Entry;
use App\Core\Models\EntryMeta;
use App\Core\Models\EntryRelation;
use Illuminate\Support\Facades\DB;

class HeaderRepository
{
    protected function getCollection(): Collection
    {
        return Collection::firstOrCreate(
            [
                'api_endpoint' => 'header',
            ],
            [
                'collection_name' => 'Header',
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


    public function getHeader()
    {
        return $this->getCollection()
            ->entries()
            ->with([
                'metas',
                'childEntries.metas'
            ])
            ->get();
    }

    public function createMenu(array $data)
    {
        return DB::transaction(function () use ($data) {

            $collection = $this->getCollection();


            $entry = Entry::create([
                'collection_id' => $collection->id,
                'status' => 'published',
            ]);


            $entry->metas()->createMany([
                [
                    'meta_key' => 'type',
                    'meta_value' => 'menu',
                ],
                [
                    'meta_key' => 'label',
                    'meta_value' => $data['label'],
                ],
            ]);


            foreach ($data['children'] ?? [] as $child) {

                $childEntry = Entry::create([
                    'collection_id' => $collection->id,
                    'status' => 'published',
                ]);


                $childEntry->metas()->createMany([
                    [
                        'meta_key' => 'type',
                        'meta_value' => 'submenu',
                    ],
                    [
                        'meta_key' => 'label',
                        'meta_value' => $child['label'],
                    ],
                    [
                        'meta_key' => 'url',
                        'meta_value' => $child['url'] ?? null,
                    ],
                ]);


                EntryRelation::create([
                    'parent_entry_id' => $entry->id,
                    'child_entry_id' => $childEntry->id,
                    'relation_type' => 'submenu',
                ]);
            }


            return $entry->fresh([
                'metas',
                'childEntries.metas'
            ]);
        });
    }


    public function updateMenu(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {

            $entry = Entry::findOrFail($id);


            $entry->metas()
                ->where('meta_key', 'label')
                ->update([
                    'meta_value' => $data['label'],
                ]);


            foreach ($entry->childEntries as $child) {

                $child->metas()->delete();

                DB::table('entry_relations')
                    ->where('child_entry_id', $child->id)
                    ->delete();

                $child->delete();
            }


            DB::table('entry_relations')
                ->where('parent_entry_id', $entry->id)
                ->delete();


            $collection = $this->getCollection();


            foreach ($data['children'] ?? [] as $child) {

                $childEntry = Entry::create([
                    'collection_id' => $collection->id,
                    'status' => 'published',
                ]);


                $childEntry->metas()->createMany([
                    [
                        'meta_key' => 'type',
                        'meta_value' => 'submenu',
                    ],
                    [
                        'meta_key' => 'label',
                        'meta_value' => $child['label'],
                    ],
                    [
                        'meta_key' => 'url',
                        'meta_value' => $child['url'] ?? null,
                    ],
                ]);


                EntryRelation::create([
                    'parent_entry_id' => $entry->id,
                    'child_entry_id' => $childEntry->id,
                    'relation_type' => 'submenu',
                ]);
            }


            $entry = $entry->fresh([
                'metas',
                'childEntries.metas'
            ]);

            $label = $entry->metas
                ->where('meta_key', 'label')
                ->first()?->meta_value;


            $children = $entry->childEntries->map(function ($child) {

                return [
                    'id' => $child->id,
                    'label' => $child->metas
                        ->where('meta_key', 'label')
                        ->first()?->meta_value,

                    'url' => $child->metas
                        ->where('meta_key', 'url')
                        ->first()?->meta_value,
                ];
            })->values();


            return [
                'id' => $entry->id,
                'label' => $label,
                'children' => $children,
            ];
        });
    }


    public function deleteMenu(int $id)
    {
        return DB::transaction(function () use ($id) {

            $entry = Entry::findOrFail($id);


            foreach ($entry->childEntries as $child) {

                $child->metas()->delete();

                DB::table('entry_relations')
                    ->where('child_entry_id', $child->id)
                    ->delete();

                $child->delete();
            }


            DB::table('entry_relations')
                ->where('parent_entry_id', $entry->id)
                ->delete();


            $entry->metas()->delete();

            $entry->delete();


            return true;
        });
    }
    public function updateLogo(array $image)
    {
        $entry = $this->getOrCreateEntry('logo');


        EntryMeta::updateOrCreate(
            [
                'entry_id' => $entry->id,
                'meta_key' => 'image',
            ],
            [
                'meta_value' => $image['url'],
            ]
        );


        EntryMeta::updateOrCreate(
            [
                'entry_id' => $entry->id,
                'meta_key' => 'public_id',
            ],
            [
                'meta_value' => $image['public_id'],
            ]
        );


        return [
            'logo' => $image['url'],
        ];
    }


    public function getLogo()
    {
        $entry = $this->getOrCreateEntry('logo');

        $entry->load('metas');


        return [
            'logo' => $entry->metas
                ->where('meta_key', 'image')
                ->first()?->meta_value,
        ];
    }

    public function updateFavicon(array $image)
    {
        $entry = $this->getOrCreateEntry('favicon');


        foreach ($image as $key => $value) {

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


        return $image;
    }

    public function updateCountdown(array $input)
    {
        $entry = $this->getOrCreateEntry('countdown');

        $data = [
            'enabled' => $input['enabled']
                ? 'true'
                : 'false',

            'target_date' => $input['target_date'] ?? null,
        ];

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


        if (!empty($input['cta'])) {

            $this->updateCTA([
                'label' => $input['cta']['label'] ?? null,
                'href' => $input['cta']['href'] ?? null,
            ]);
        }

        return [
            'id' => (string)$entry->id,
            ...$data,
        ];
    }

    public function updateCTA(array $data)
    {
        $entry = $this->getOrCreateEntry('cta');


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


        return [
            'id' => (string)$entry->id,
            ...$data,
        ];
    }
}
