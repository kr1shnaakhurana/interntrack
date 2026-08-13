<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyVerification extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'company_id',
        'status',
        'verified_by',
        'documents_url',
        'remarks',
        'applied_at',
        'reviewed_at',
        'is_latest',
    ];

    protected $casts = [
        'is_latest' => 'boolean',
        'applied_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
