<?php

namespace App\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntryMeta extends Model
{
    protected $table = 'entry_meta';

    protected $fillable = [
        'entry_id',
        'meta_key',
        'meta_value',
    ];

    public function entry(): BelongsTo
    {
        return $this->belongsTo(Entry::class, 'entry_id');
    }
}
