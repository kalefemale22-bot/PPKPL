<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StorageRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_name',
        'temp_limit',
        'humidity_limit',
    ];

    /**
     * Relasi ke ConditionData (One-to-Many)
     */
    public function conditionData(): HasMany
    {
        return $this->hasMany(ConditionData::class, 'storage_room_id');
    }
}
