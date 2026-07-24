<?php

namespace App\Core\Repositories\Eloquent;

use App\Core\Models\Collection;
use App\Core\Models\Entry;
use App\Core\Models\EntryMeta;
use App\Core\Models\EntryRelation;
use Illuminate\Support\Facades\DB;

class HeaderRepository
{
    public function getHeader()
    {
        return Collection::where('api_endpoint', 'header')
            ->firstOrFail()
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
            $collection = Collection::where(
                'api_endpoint',
                'header'
            )->firstOrFail();
            $entry = Entry::create([
                'collection_id' => $collection->id
            ]);
            EntryMeta::create([
                'entry_id' => $entry->id,
                'meta_key' => 'type',
                'meta_value' => 'menu'
            ]);
            EntryMeta::create([
                'entry_id' => $entry->id,
                'meta_key' => 'label',
                'meta_value' => $data['label']
            ]);
            foreach ($data['children'] ?? [] as $child) {
                $childEntry = Entry::create([
                    'collection_id' => $collection->id
                ]);
                EntryMeta::create([
                    'entry_id' => $childEntry->id,
                    'meta_key' => 'type',
                    'meta_value' => 'submenu'
                ]);
                EntryMeta::create([
                    'entry_id' => $childEntry->id,
                    'meta_key' => 'label',
                    'meta_value' => $child['label']
                ]);
                EntryRelation::create([
                    'parent_entry_id' => $entry->id,
                    'child_entry_id' => $childEntry->id,
                    'relation_type' => 'submenu'
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
                    'meta_value' => $data['label']
                ]);
            $oldChildren = $entry->childEntries;
            foreach ($oldChildren as $child) {
                $child->metas()->delete();
                $child->delete();
            }
            DB::table('entry_relations')
                ->where('parent_entry_id', $entry->id)
                ->delete();
            foreach ($data['children'] ?? [] as $child) {
                $childEntry = Entry::create([
                    'collection_id' => $entry->collection_id
                ]);
                EntryMeta::create([
                    'entry_id' => $childEntry->id,
                    'meta_key' => 'type',
                    'meta_value' => 'submenu'
                ]);
                EntryMeta::create([
                    'entry_id' => $childEntry->id,
                    'meta_key' => 'label',
                    'meta_value' => $child['label']
                ]);
                EntryRelation::create([
                    'parent_entry_id' => $entry->id,
                    'child_entry_id' => $childEntry->id,
                    'relation_type' => 'submenu'
                ]);
            }
            return $entry->fresh([
                'metas',
                'childEntries.metas'
            ]);
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
        return DB::transaction(function () use ($image) {

            $entry = Entry::whereHas('metas', function ($query) {
                $query->where('meta_key', 'type')
                    ->where('meta_value', 'logo');
            })->firstOrFail();


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
        });
    }
    public function getLogo()
    {
        $entry = Entry::whereHas('metas', function ($query) {
            $query->where('meta_key', 'type')
                ->where('meta_value', 'logo');
        })
            ->with('metas')
            ->first();

        if (!$entry) {
            return null;
        }

        $logo = $entry->metas
            ->where('meta_key', 'image')
            ->first();

        return [
            'logo' => $logo?->meta_value,
        ];
    }
    public function updateCountdown(array $input)
    {
        return DB::transaction(function () use ($input) {

            $collection = Collection::where(
                'api_endpoint',
                'header'
            )->firstOrFail();


            $entry = $collection->entries()
                ->whereHas('metas', function ($query) {
                    $query->where('meta_key', 'type')
                        ->where('meta_value', 'countdown');
                })
                ->with('metas')
                ->first();


            if (!$entry) {

                $entry = Entry::create([
                    'collection_id' => $collection->id,
                    'title' => 'Header Countdown',
                    'status' => 'published',
                ]);


                $entry->metas()->createMany([
                    [
                        'meta_key' => 'type',
                        'meta_value' => 'countdown',
                    ],
                    [
                        'meta_key' => 'enabled',
                        'meta_value' => 'true',
                    ],
                    [
                        'meta_key' => 'target_date',
                        'meta_value' => '',
                    ],
                    [
                        'meta_key' => 'button_label',
                        'meta_value' => '',
                    ],
                    [
                        'meta_key' => 'button_href',
                        'meta_value' => '',
                    ],
                ]);
            }


            $data = [
                'enabled' => $input['enabled']
                    ? 'true'
                    : 'false',

                'target_date' => $input['target_date'],

                'button_label' => $input['button_label'],

                'button_href' => $input['button_href'],
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


            return [
                'success' => true,
                'message' => 'Header countdown updated successfully.',
            ];
        });
    }
    public function updateFavicon(array $image)
    {
        $collection = Collection::where('api_endpoint', 'header')
            ->firstOrFail();


        $entry = $collection->entries()
            ->whereHas('metas', function ($query) {
                $query->where('meta_key', 'type')
                    ->where('meta_value', 'favicon');
            })
            ->first();


        if (!$entry) {
            $entry = $collection->entries()->create([
                'status' => 'published'
            ]);

            $entry->metas()->create([
                'meta_key' => 'type',
                'meta_value' => 'favicon'
            ]);
        }


        $entry->metas()->updateOrCreate(
            [
                'meta_key' => 'url'
            ],
            [
                'meta_value' => $image['url']
            ]
        );


        $entry->metas()->updateOrCreate(
            [
                'meta_key' => 'public_id'
            ],
            [
                'meta_value' => $image['public_id']
            ]
        );


        return [
            'url' => $image['url'],
            'public_id' => $image['public_id']
        ];
    }
}
