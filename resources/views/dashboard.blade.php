<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IoT Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">IoT Management Dashboard</h1>
        
        <!-- Devices Status -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            @foreach($devices as $device)
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold">{{ $device->name }}</h3>
                <p class="text-sm text-gray-600">ID: {{ $device->device_id }}</p>
                <p class="text-sm">Data Points: {{ $device->sensor_data_count }}</p>
                <p class="text-sm">Last Seen: {{ $device->last_seen ? $device->last_seen->diffForHumans() : 'Never' }}</p>
            </div>
            @endforeach
        </div>

        <!-- Lamp Controls -->
        <div class="bg-white p-6 rounded-lg shadow mb-8">
            <h2 class="text-2xl font-bold mb-4">Lamp Controls</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($lamps as $lamp)
                <div class="flex items-center justify-between p-4 border rounded">
                    <span class="font-medium">{{ $lamp->name }}</span>
                    <button onclick="toggleLamp({{ $lamp->id }})" 
                            class="px-4 py-2 rounded {{ $lamp->status ? 'bg-green-500 text-white' : 'bg-gray-300' }}">
                        {{ $lamp->status ? 'ON' : 'OFF' }}
                    </button>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Latest Sensor Data -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-2xl font-bold mb-4">Latest Sensor Data</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2 text-left">Device</th>
                            <th class="px-4 py-2 text-left">Temperature (°C)</th>
                            <th class="px-4 py-2 text-left">Humidity (%)</th>
                            <th class="px-4 py-2 text-left">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($latestData as $data)
                        <tr class="border-b">
                            <td class="px-4 py-2">{{ $data->device->name }}</td>
                            <td class="px-4 py-2">{{ $data->temperature }}</td>
                            <td class="px-4 py-2">{{ $data->humidity }}</td>
                            <td class="px-4 py-2">{{ $data->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function toggleLamp(lampId) {
            fetch(`/lamps/${lampId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                location.reload();
            });
        }
    </script>
</body>
</html>