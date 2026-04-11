<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConditionData extends Model
{
    use HasFactory;

    protected $fillable = [
        'storage_room_id',
        'inputted_by',
        'temperature',
        'humidity',
        'indicator_color',
    ];

    /**
     * Relasi ke StorageRoom
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(StorageRoom::class, 'storage_room_id');
    }

    /**
     * Relasi ke User pembuat
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inputted_by');
    }
}
