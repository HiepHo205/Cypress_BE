<?php

namespace App\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Entry extends Model
{
    protected $fillable = [
        'collection_id',
        'status',
        'created_by'
    ];


    public function metas(): HasMany
    {
        return $this->hasMany(
            EntryMeta::class,
            'entry_id'
        );
    }


    public function childRelations(): HasMany
    {
        return $this->hasMany(
            EntryRelation::class,
            'parent_entry_id'
        );
    }


    public function parentRelations(): HasMany
    {
        return $this->hasMany(
            EntryRelation::class,
            'child_entry_id'
        );
    }


    public function childEntries()
    {
        return $this->belongsToMany(
            Entry::class,
            'entry_relations',
            'parent_entry_id',
            'child_entry_id'
        );
    }


    public function parentEntries()
    {
        return $this->belongsToMany(
            Entry::class,
            'entry_relations',
            'child_entry_id',
            'parent_entry_id'
        );
    }
}
