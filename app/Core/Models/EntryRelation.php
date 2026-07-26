<?php

namespace App\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntryRelation extends Model
{
    protected $table = 'entry_relations';


    protected $fillable = [
        'parent_entry_id',
        'child_entry_id',
        'relation_type'
    ];


    public function parentEntry(): BelongsTo
    {
        return $this->belongsTo(
            Entry::class,
            'parent_entry_id'
        );
    }


    public function childEntry(): BelongsTo
    {
        return $this->belongsTo(
            Entry::class,
            'child_entry_id'
        );
    }
}
