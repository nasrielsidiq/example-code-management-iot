<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\SensorData;
use App\Models\LampControl;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $devices = Device::withCount('sensorData')->get();
        $latestData = SensorData::with('device')->latest()->take(10)->get();
        $lamps = LampControl::all();
        
        return view('dashboard', compact('devices', 'latestData', 'lamps'));
    }

    public function toggleLamp(Request $request, LampControl $lamp)
    {
        $lamp->update(['status' => !$lamp->status]);
        return response()->json(['status' => $lamp->status]);
    }
}
