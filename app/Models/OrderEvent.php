<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** What happened to an order. Never stores a key, a set-password token or a password. */
class OrderEvent extends Model
{
    use HasVersion7Uuids;

    public $timestamps = false;

    protected $fillable = ['order_id', 'user_id', 'event', 'detail', 'created_at'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
