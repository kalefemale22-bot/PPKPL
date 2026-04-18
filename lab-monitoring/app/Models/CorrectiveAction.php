<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorrectiveAction extends Model
{
    public $timestamps = false;
    protected $fillable = ['incident_ticket_id', 'description', 'recorded_by', 'created_at'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function ticket()
    {
        return $this->belongsTo(IncidentTicket::class, 'incident_ticket_id');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}