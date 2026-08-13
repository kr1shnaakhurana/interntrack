<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interview extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'application_id',
        'round_number',
        'round_name',
        'interview_type',
        'scheduled_at',
        'interviewer',
        'location_meeting_link',
        'notes',
        'result',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'round_number' => 'integer',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id');
    }
}
