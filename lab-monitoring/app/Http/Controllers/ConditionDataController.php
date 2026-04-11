<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConditionDataRequest;
use App\Models\ConditionData;
use App\Models\StorageRoom;
use App\Models\Notification;
use Illuminate\Http\Request;

class ConditionDataController extends Controller
{
    public function index()
    {
        // Mengambil semua data dengan relasi room dan user, diurutkan dari yang terbaru
        $data = ConditionData::with(['room', 'user'])->latest()->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Data kondisi ruang berhasil diambil',
            'data' => $data
        ]);
    }

    public function store(StoreConditionDataRequest $request)
    {
        // a. Data tervalidasi oleh StoreConditionDataRequest
        $validated = $request->validated();

        // b. Ambil nilai limit suhu dan kelembaban dari storage_rooms
        $room = StorageRoom::findOrFail($validated['storage_room_id']);
        $temp_limit = $room->temp_limit;
        $hum_limit = $room->humidity_limit;
        
        $temp = $validated['temperature'];
        $hum = $validated['humidity'];

        // Menghitung persentase deviasi/kelebihan dari batas limit
        $temp_percent_over = ($temp - $temp_limit) / $temp_limit * 100;
        $hum_percent_over = ($hum - $hum_limit) / $hum_limit * 100;

        // c. Kalkulasi indicator_color berdasarkan persentase deviasi
        if ($temp_percent_over > 10 || $hum_percent_over > 10) {
            // Jika melebihi dari 10% di atas batas
            $indicator_color = 'red';
        } elseif ($temp_percent_over > 0 || $hum_percent_over > 0) {
            // Jika melebihi hingga 10% (di atas 0%, tapi maksimal 10%)
            $indicator_color = 'yellow';
        } else {
            // Suhu dan kelembaban <= limit (tidak ada deviasi)
            $indicator_color = 'green';
        }

        // d. Simpan data kondisi ke database
        $conditionData = ConditionData::create([
            'storage_room_id' => $validated['storage_room_id'],
            'inputted_by'     => $validated['inputted_by'],
            'temperature'     => $temp,
            'humidity'        => $hum,
            'indicator_color' => $indicator_color,
        ]);

        // e. Otomatis buat notifikasi jika kondisi indikator = red (kritis)
        if ($indicator_color === 'red') {
            Notification::create([
                'user_id' => $validated['inputted_by'], // Disini asumsikan notifikasi dikirim ke user penginput
                'message' => "Peringatan Kritis! Kondisi di ruangan {$room->room_name} melebihi dari batas toleransi. Suhu: {$temp}°C (Limit: {$temp_limit}°C), Kelembaban: {$hum}% (Limit: {$hum_limit}%)."
            ]);
        }

        // f. Response JSON
        return response()->json([
            'status' => 'success',
            'message' => 'Data kondisi ruang berhasil direkam',
            'data' => $conditionData->load(['room', 'user'])
        ], 201);
    }
}
