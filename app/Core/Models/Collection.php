<?php

namespace App\Core\Models;

use App\Core\Models\Entry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Collection extends Model
{
    protected $table = 'collections';

    protected $fillable = [
        'collection_name',
        'api_endpoint',
        'is_system_type',
    ];

    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class, 'collection_id');
    }
}
