<?php

namespace App\Core\Repositories\Eloquent;

use App\Core\Models\Entry;
use App\Core\Models\EntryMeta;
use App\Core\Repositories\Contracts\HomepageRepositoryInterface;
use Illuminate\Support\Facades\DB;

class HomepageRepository extends BaseRepository implements HomepageRepositoryInterface
{
    public function model(): string
    {
        return Entry::class;
    }

    protected function getHomepageEntry(): Entry
    {
        $collection = $this->getCollection();

        $entry = $collection->entries()
            ->whereHas('metas', function ($query) {
                $query->where('meta_key', 'type')
                    ->where('meta_value', 'homepage');
            })
            ->first();

        if (!$entry) {
            $entry = $collection->entries()->create([
                'status' => 'published',
            ]);

            $entry->metas()->create([
                'meta_key' => 'type',
                'meta_value' => 'homepage',
            ]);
        }

        return $entry;
    }

    protected function getCaseStudyEntry(): Entry
    {
        $collection = DB::table('collections')
            ->where('api_endpoint', 'case-study')
            ->first();

        if (!$collection) {
            throw new \RuntimeException(
                'Case Study collection not found.'
            );
        }

        $entry = Entry::where('collection_id', $collection->id)
            ->where('status', 'published')
            ->first();

        if (!$entry) {
            $entry = Entry::create([
                'collection_id' => $collection->id,
                'status' => 'published',
            ]);
        }

        return $entry;
    }

    protected function getEntryBySection(string $key): Entry
    {
        if ($key === 'case_study_page') {
            return $this->getCaseStudyEntry();
        }

        return $this->getHomepageEntry();
    }

    public function getSection(string $key): array
    {
        $entry = $this->getEntryBySection($key);

        $meta = EntryMeta::where('entry_id', $entry->id)
            ->where('meta_key', $key)
            ->first();

        if (!$meta) {
            return [];
        }

        $data = json_decode(
            $meta->meta_value,
            true
        );

        return is_array($data) ? $data : [];
    }

    public function updateSection(string $key, array $data): array
    {
        return DB::transaction(function () use ($key, $data) {
            $entry = $this->getEntryBySection($key);

            $meta = EntryMeta::where('entry_id', $entry->id)
                ->where('meta_key', $key)
                ->first();

            $oldData = [];

            if ($meta) {
                $oldData = json_decode(
                    $meta->meta_value,
                    true
                ) ?? [];
            }

            $newData = array_merge(
                $oldData,
                $data
            );

            EntryMeta::updateOrCreate(
                [
                    'entry_id' => $entry->id,
                    'meta_key' => $key,
                ],
                [
                    'meta_value' => json_encode(
                        $newData,
                        JSON_UNESCAPED_UNICODE
                    ),
                ]
            );

            return $newData;
        });
    }

    public function deleteBusinessGrowthPackage(string $number)
    {
        $entry = $this->getHomepageEntry();

        $meta = $entry->metas()
            ->where('meta_key', 'business_growth')
            ->first();

        if (!$meta) {
            return [
                'packages' => []
            ];
        }

        $data = json_decode(
            $meta->meta_value,
            true
        ) ?? [];

        $data['packages'] = collect(
            $data['packages'] ?? []
        )
            ->reject(
                fn($item) =>
                ($item['number'] ?? null) === $number
            )
            ->values()
            ->toArray();

        $meta->update([
            'meta_value' => json_encode(
                $data,
                JSON_UNESCAPED_UNICODE
            )
        ]);

        return $data;
    }

    public function deleteSuccessStory($id): array
    {
        $data = $this->getSection(
            'success_stories'
        );

        $data['successStories'] = collect(
            $data['successStories'] ?? []
        )
            ->reject(
                fn($item) =>
                (string) $item['id'] === (string) $id
            )
            ->values()
            ->toArray();

        return $this->updateSection(
            'success_stories',
            $data
        );
    }

    public function deleteBenefit($id): array
    {
        $data = $this->getSection(
            'why_choose_cypress'
        );

        $data['benefits'] = collect(
            $data['benefits'] ?? []
        )
            ->reject(
                fn($item) =>
                (string) $item['id'] === (string) $id
            )
            ->values()
            ->toArray();

        $this->updateSection(
            'why_choose_cypress',
            $data
        );

        return [
            'success' => true,
            'message' => 'Benefit deleted successfully'
        ];
    }

    public function getAllSections(): array
    {
        $entry = $this->getHomepageEntry();

        return EntryMeta::where('entry_id', $entry->id)
            ->get()
            ->mapWithKeys(function ($meta) {
                return [
                    $meta->meta_key => json_decode(
                        $meta->meta_value,
                        true
                    ) ?? []
                ];
            })
            ->toArray();
    }
}