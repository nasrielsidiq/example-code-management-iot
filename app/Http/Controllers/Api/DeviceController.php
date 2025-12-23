<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\SensorData;
use App\Models\LampControl;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DeviceController extends Controller
{
    public function checkConnection(Request $request): JsonResponse
    {
        $device = $this->authenticateDevice($request);
        if (!$device) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $device->update(['last_seen' => now()]);
        return response()->json(['status' => 'connected', 'device' => $device->name]);
    }

    public function storeSensorData(Request $request): JsonResponse
    {
        $device = $this->authenticateDevice($request);
        if (!$device) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'temperature' => 'required|numeric',
            'humidity' => 'required|numeric'
        ]);

        SensorData::create([
            'device_id' => $device->id,
            'temperature' => $request->temperature,
            'humidity' => $request->humidity
        ]);

        $device->update(['last_seen' => now()]);
        return response()->json(['status' => 'success']);
    }

    public function getLampStatus(): JsonResponse
    {
        $lamps = LampControl::all(['id', 'name', 'status']);
        return response()->json($lamps);
    }

    public function updateLampStatus(Request $request): JsonResponse
    {
        $device = $this->authenticateDevice($request);
        if (!$device) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'lamp_id' => 'required|exists:lamp_controls,id',
            'status' => 'required|boolean'
        ]);

        $lamp = LampControl::find($request->lamp_id);
        $lamp->update(['status' => $request->status]);

        return response()->json(['status' => 'success', 'lamp' => $lamp]);
    }

    private function authenticateDevice(Request $request): ?Device
    {
        $apiKey = $request->header('API-KEY');
        $deviceId = $request->header('Device-Id');

        if (!$apiKey || !$deviceId) {
            return null;
        }

        return Device::where('api_key', $apiKey)
                    ->where('device_id', $deviceId)
                    ->first();
    }
}
