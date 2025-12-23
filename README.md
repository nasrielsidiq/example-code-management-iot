# IoT Management System

Aplikasi Laravel untuk management IoT, monitoring data suhu dan kelembapan, serta kontrol 3 lampu.

## Instalasi

```bash
# Clone repository
git clone <repository-url>
cd test

# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate
php artisan db:seed

# Start server
php artisan serve
```

## Fitur

- **Monitoring Sensor**: Data suhu & kelembapan real-time
- **Device Management**: Status koneksi dan last seen device
- **Lamp Control**: Kontrol on/off 3 lampu via dashboard web
- **API Authentication**: Keamanan menggunakan API key dan device ID

## Penggunaan

### Web Dashboard
Akses dashboard di: `http://localhost:8000/dashboard`

### IoT Device Setup
1. Gunakan kode ESP32 di file `esp32_example.ino`
2. Update WiFi credentials dan server URL
3. Upload ke ESP32 dengan sensor DHT22

## API Documentation

Base URL: `http://localhost:8000/api/device`

### Authentication
Semua endpoint memerlukan headers:
```
API-KEY: your_api_key
Device-Id: your_device_id
```

### Endpoints

#### 1. Check Connection
```http
POST /api/device/check
```

**Response:**
```json
{
  "status": "connected",
  "device": "Temperature Sensor 1"
}
```

#### 2. Send Sensor Data
```http
POST /api/device/sensor-data
Content-Type: application/json

{
  "temperature": 25.5,
  "humidity": 60.2
}
```

**Response:**
```json
{
  "status": "success"
}
```

#### 3. Get Lamp Status
```http
GET /api/device/lamps
```

**Response:**
```json
[
  {
    "id": 1,
    "name": "Living Room Lamp",
    "status": false
  },
  {
    "id": 2,
    "name": "Kitchen Lamp", 
    "status": true
  },
  {
    "id": 3,
    "name": "Bedroom Lamp",
    "status": false
  }
]
```

#### 4. Update Lamp Status
```http
POST /api/device/lamps
Content-Type: application/json

{
  "lamp_id": 1,
  "status": true
}
```

**Response:**
```json
{
  "status": "success",
  "lamp": {
    "id": 1,
    "name": "Living Room Lamp",
    "status": true
  }
}
```

### Error Responses

#### 401 Unauthorized
```json
{
  "error": "Unauthorized"
}
```

#### 422 Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "temperature": ["The temperature field is required."]
  }
}
```

## Database Schema

### devices
- `id` - Primary key
- `device_id` - Unique device identifier
- `api_key` - Authentication key
- `name` - Device name
- `last_seen` - Last connection timestamp

### sensor_data
- `id` - Primary key
- `device_id` - Foreign key to devices
- `temperature` - Temperature value (decimal 5,2)
- `humidity` - Humidity value (decimal 5,2)
- `created_at` - Timestamp

### lamp_controls
- `id` - Primary key
- `name` - Lamp name
- `status` - On/off status (boolean)

## Hardware Requirements

- ESP32 microcontroller
- DHT22 temperature/humidity sensor
- 3 relay modules for lamp control
- WiFi connection

## Wiring ESP32

```
DHT22 -> GPIO 2
Lamp 1 Relay -> GPIO 5
Lamp 2 Relay -> GPIO 18
Lamp 3 Relay -> GPIO 19
```

## License

MIT License