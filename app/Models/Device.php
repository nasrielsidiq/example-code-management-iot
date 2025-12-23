<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    protected $fillable = ['device_id', 'api_key', 'name', 'last_seen'];

    protected $casts = [
        'last_seen' => 'datetime',
    ];

    public function sensorData(): HasMany
    {
        return $this->hasMany(SensorData::class);
    }
}
