<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobSource extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'type',
        'adapter_key',
        'base_url',
        'api_endpoint',
        'auth_type',
        'config',
        'field_mapping',
        'is_active',
        'last_fetched_at',
        'last_fetch_status',
        'last_fetch_error',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
        'field_mapping' => 'array',
        'last_fetched_at' => 'datetime',
    ];

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'source_id');
    }
}
