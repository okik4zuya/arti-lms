<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/** Daily download totals per file name. No visitor data is stored. */
class DownloadCount extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'day', 'downloads'];

    protected function casts(): array
    {
        return ['day' => 'date'];
    }

    /** Adds one download to today's row in a single atomic statement. */
    public static function bump(string $name): void
    {
        static::query()->upsert(
            [['name' => $name, 'day' => today()->toDateString(), 'downloads' => 1]],
            ['name', 'day'],
            ['downloads' => DB::raw('downloads + 1')]
        );
    }
}
