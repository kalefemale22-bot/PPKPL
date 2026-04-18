<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model IncidentTicket
 *
 * Merepresentasikan tiket insiden yang dibuat secara otomatis
 * ketika terdeteksi deviasi kondisi Level 2 (Kritis / Merah).
 *
 * @property int    $id
 * @property int    $storage_room_id
 * @property int    $condition_data_id
 * @property string $deviation_level   '1' = Peringatan, '2' = Kritis
 * @property string $status            'open' | 'closed'
 */
class IncidentTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'storage_room_id',
        'condition_data_id',
        'deviation_level',
        'status',
    ];

    protected $casts = [
        'deviation_level' => 'string',
    ];

    // -------------------------------------------------------------------------
    // Relasi
    // -------------------------------------------------------------------------

    /**
     * Tiket insiden dimiliki oleh satu ruang penyimpanan.
     */
    public function storageRoom(): BelongsTo
    {
        return $this->belongsTo(StorageRoom::class, 'storage_room_id');
    }

    /**
     * Tiket insiden dipicu oleh satu entri data kondisi.
     */
    public function conditionData(): BelongsTo
    {
        return $this->belongsTo(ConditionData::class, 'condition_data_id');
    }

    // -------------------------------------------------------------------------
    // Helper / Accessor
    // -------------------------------------------------------------------------

    /**
     * Mengembalikan label level deviasi yang ramah-baca dalam Bahasa Indonesia.
     */
    public function getDeviationLabelAttribute(): string
    {
        return match ($this->deviation_level) {
            '1'     => 'Peringatan',
            '2'     => 'Kritis',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Mengembalikan label status yang ramah-baca.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'open'   => 'Terbuka',
            'closed' => 'Selesai',
            default  => 'Tidak Diketahui',
        };
    }
}
