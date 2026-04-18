<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncidentTicket extends Model
{
    protected $fillable = ['condition_data_id', 'status', 'created_by'];

    public function conditionData()
    {
        return $this->belongsTo(ConditionData::class, 'condition_data_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function correctiveActions()
    {
        return $this->hasMany(CorrectiveAction::class);
    }
}