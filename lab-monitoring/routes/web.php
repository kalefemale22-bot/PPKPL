<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\IncidentTicket;
use App\Models\ConditionData;
use App\Models\StorageRoom;
use App\Models\CorrectiveAction;

Route::get('/', function () {
    return redirect('/monitoring');
});

Route::get('/monitoring', function () {
    return view('monitoring');
});

Route::get('/monitoring/incidents/{id?}', function ($id = null) {
    if ($id) {
        $ticket = IncidentTicket::with(['correctiveActions.recorder', 'creator', 'conditionData.room'])
            ->where('condition_data_id', $id)->firstOrFail();
        return view('incidents', compact('ticket'));
    }

    $tickets = IncidentTicket::with(['conditionData.room', 'creator'])->latest()->get();
    return view('incidents', compact('tickets'));
});

Route::get('/api/storage-rooms', function () {
    return response()->json([
        'status' => 'success',
        'data' => StorageRoom::all()
    ]);
});

Route::get('/api/condition-data', function () {
    return response()->json([
        'status' => 'success',
        'data' => ConditionData::with(['room', 'user'])->latest()->get()
    ]);
});

Route::post('/api/condition-data', function (Request $request) {
    $room = App\Models\StorageRoom::find($request->storage_room_id);
    
    $temp = (float) $request->temperature;
    $hum = (float) $request->humidity;
    $tLimit = (float) $room->temp_limit;
    $hLimit = (float) $room->humidity_limit;

    $color = 'green';

    if ($temp > ($tLimit + 2.0) || $hum > ($hLimit + 10.0)) {
        $color = 'red';
    } 
    elseif ($temp > $tLimit || $hum > $hLimit || $temp >= ($tLimit - 2.0)) {
        $color = 'yellow';
    }

    $condition = App\Models\ConditionData::create([
        'storage_room_id' => $request->storage_room_id,
        'inputted_by'     => $request->inputted_by,
        'temperature'     => $temp,
        'humidity'        => $hum,
        'indicator_color' => $color, 
    ]);

    if ($color !== 'green') {
        App\Models\IncidentTicket::create([
            'condition_data_id' => $condition->id,
            'status'            => 'open',
            'created_by'        => $request->inputted_by
        ]);
    }

    return response()->json(['status' => 'success', 'data' => $condition]);
});

Route::post('/api/incident-tickets/{id}/actions', function (Request $request, $id) {
    $ticket = IncidentTicket::findOrFail($id);

    $action = CorrectiveAction::create([
        'incident_ticket_id' => $id,
        'description'        => $request->description,
        'recorded_by'        => 1, 
    ]);

    if ($ticket->status == 'open') {
        $ticket->update(['status' => 'dalam_penanganan']);
    }

    return response()->json(['status' => 'success', 'data' => $action]);
});

Route::delete('/api/incident-tickets/{id}', function ($id) {
    $ticket = App\Models\IncidentTicket::findOrFail($id);
    
    App\Models\CorrectiveAction::where('incident_ticket_id', $id)->delete();
    
    $ticket->delete();

    return response()->json(['status' => 'success', 'message' => 'Tiket berhasil dibuang!']);
});