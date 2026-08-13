<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'name',
        'website',
        'industry',
        'size',
        'location',
        'about',
        'logo_url',
        'is_claimed',
        'sector',
        'is_external',
    ];

    protected $casts = [
        'is_claimed' => 'boolean',
        'is_external' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(CompanyVerification::class, 'company_id');
    }

    public function latestVerification(): HasOne
    {
        return $this->hasOne(CompanyVerification::class, 'company_id')->where('is_latest', true);
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'company_id');
    }

    public function isVerified(): bool
    {
        return $this->latestVerification && $this->latestVerification->status === 'approved';
    }
}
