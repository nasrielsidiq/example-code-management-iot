/*
 * ESP32 IoT Device Example
 * Sends temperature/humidity data and controls lamps
 */

#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <DHT.h>

// WiFi credentials
const char* ssid = "YOUR_WIFI_SSID";
const char* password = "YOUR_WIFI_PASSWORD";

// Server configuration
const char* serverURL = "http://your-laravel-app.com/api/device";
const char* apiKey = "sample_api_key_123";
const char* deviceId = "ESP32_001";

// DHT sensor
#define DHT_PIN 2
#define DHT_TYPE DHT22
DHT dht(DHT_PIN, DHT_TYPE);

// Lamp control pins
const int lampPins[] = {5, 18, 19};
const int numLamps = 3;

void setup() {
  Serial.begin(115200);
  
  // Initialize lamp pins
  for(int i = 0; i < numLamps; i++) {
    pinMode(lampPins[i], OUTPUT);
    digitalWrite(lampPins[i], LOW);
  }
  
  // Initialize DHT sensor
  dht.begin();
  
  // Connect to WiFi
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Connecting to WiFi...");
  }
  Serial.println("Connected to WiFi");
}

void loop() {
  // Check connection
  checkConnection();
  
  // Send sensor data every 30 seconds
  sendSensorData();
  
  // Check lamp status every 10 seconds
  updateLampStatus();
  
  delay(30000);
}

void checkConnection() {
  HTTPClient http;
  http.begin(String(serverURL) + "/check");
  http.addHeader("Content-Type", "application/json");
  http.addHeader("API-KEY", apiKey);
  http.addHeader("Device-Id", deviceId);
  
  int httpCode = http.POST("{}");
  if(httpCode == 200) {
    Serial.println("Connection OK");
  }
  http.end();
}

void sendSensorData() {
  float temperature = dht.readTemperature();
  float humidity = dht.readHumidity();
  
  if(isnan(temperature) || isnan(humidity)) {
    Serial.println("Failed to read from DHT sensor");
    return;
  }
  
  HTTPClient http;
  http.begin(String(serverURL) + "/sensor-data");
  http.addHeader("Content-Type", "application/json");
  http.addHeader("API-KEY", apiKey);
  http.addHeader("Device-Id", deviceId);
  
  String payload = "{\"temperature\":" + String(temperature) + ",\"humidity\":" + String(humidity) + "}";
  
  int httpCode = http.POST(payload);
  if(httpCode == 200) {
    Serial.println("Sensor data sent: T=" + String(temperature) + "°C, H=" + String(humidity) + "%");
  }
  http.end();
}

void updateLampStatus() {
  HTTPClient http;
  http.begin(String(serverURL) + "/lamps");
  http.addHeader("API-KEY", apiKey);
  http.addHeader("Device-Id", deviceId);
  
  int httpCode = http.GET();
  if(httpCode == 200) {
    String response = http.getString();
    DynamicJsonDocument doc(1024);
    deserializeJson(doc, response);
    
    for(int i = 0; i < numLamps && i < doc.size(); i++) {
      bool status = doc[i]["status"];
      digitalWrite(lampPins[i], status ? HIGH : LOW);
      Serial.println("Lamp " + String(i+1) + ": " + (status ? "ON" : "OFF"));
    }
  }
  http.end();
}