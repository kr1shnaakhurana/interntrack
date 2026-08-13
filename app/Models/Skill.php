<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'category',
    ];

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_skills', 'skill_id', 'student_id')
                    ->withPivot('proficiency');
    }

    public function jobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class, 'job_skills', 'skill_id', 'job_id')
                    ->withPivot('importance');
    }
}
