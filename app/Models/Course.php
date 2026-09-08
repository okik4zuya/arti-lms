<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'published_at',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function access(): HasMany
    {
        return $this->hasMany(CourseAccess::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(Progress::class);
    }
}
