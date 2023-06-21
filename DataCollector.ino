#include <WiFi.h>  // Library for WiFi functionality
#include <WiFiClientSecure.h>  // Library for secure WiFi client
#include <HTTPClient.h>  // Library for making HTTP requests
#include <Wire.h>  // Library for I2C communication
#include <Adafruit_Sensor.h>  // Library for sensor functionality
#include <Adafruit_BME280.h>  // Library for BME280 sensor

#define SEALEVELPRESSURE_HPA (1013.25)  // Define sea level pressure constant
const char* ssid = "-------";  // WiFi network name
const char* password = "------";  // WiFi network password
const char* serverName = "https://danbajda.com/post-esp-data.php";  // Server URL to send data to
String apiKeyValue = "---------";  // API key for authentication
Adafruit_BME280 bme;  // BME280 sensor object
int resetPin = 13;  // Pin number for resetting the device

void setup() {
  Serial.begin(9600);  // Initialize serial communication
  Serial.print("Connecting to ");
  Serial.println(ssid);
  WiFi.begin(ssid, password);  // Connect to WiFi network
  
  while (WiFi.status() != WL_CONNECTED) {  // Wait for WiFi connection
    delay(500);
    Serial.print(".");
  }
  
  Serial.println("");
  Serial.println("WiFi connected.");
  Serial.println("IP address: ");
  Serial.println(WiFi.localIP());  // Print local IP address
  
  bool status = bme.begin(0x77);  // Initialize BME280 sensor with I2C address
  
  if (!status) {
    Serial.println("Could not find a valid BME280 sensor");
    while (1);  // Hang indefinitely if sensor not found
  }
}

void loop() {
  double avgTemp = 0;
  double avgPress = 0;
  double avgAlt = 0;
  double avgHum = 0;
  
  for(int i = 0; i < 10; i++){  // Read sensor values 10 times and calculate average
    avgTemp += bme.readTemperature();
    avgPress += bme.readPressure() / 100.0F;
    avgAlt += bme.readAltitude(SEALEVELPRESSURE_HPA);
    avgHum += bme.readHumidity();
    delay(3000);
  }
  
  avgTemp /= 10;  // Calculate average values
  avgTemp = avgTemp * 1.8 + 32;  // Convert temperature to Fahrenheit
  avgPress /= 10;
  avgAlt /= 10;
  avgHum /= 10;
  
  if(WiFi.status()== WL_CONNECTED){  // Check WiFi connection status
    WiFiClientSecure *client = new WiFiClientSecure;  // Create a secure WiFi client
    client->setInsecure();  // Disable SSL certificate verification
    HTTPClient https;
    
    https.begin(*client, serverName);  // Initialize HTTPS connection to server
    
    https.addHeader("Content-Type", "application/x-www-form-urlencoded");  // Specify content-type header
    
    // Prepare HTTP POST request data
    char buffer[10];  // Buffer to hold the converted string
    char buffer1[10];
    char buffer2[10];
    char buffer3[10];
    dtostrf(avgTemp, 4, 2, buffer);  // Convert double to string with 4 digits and 2 decimal places
    dtostrf(avgPress, 4, 2, buffer1);
    dtostrf(avgAlt, 4, 2, buffer2);
    dtostrf(avgHum, 4, 2, buffer3);
    String httpRequestData = "api_key=" + apiKeyValue + "&temperature=" + String(buffer) + "&pressure=" + String(buffer1) + "&altitude=" + String(buffer2) + "&humidity=" + String(buffer3);
    Serial.print("httpRequestData: ");
    Serial.println(httpRequestData);
    
    int httpResponseCode = https.POST(httpRequestData);  // Send HTTP POST request
    
    if (httpResponseCode>0) {
      Serial.print("HTTP Response code: ");
      Serial.println(httpResponseCode);
    }
    else {
      Serial.print("Error code: ");
      Serial.println(httpResponseCode);
      pinMode(resetPin, OUTPUT);  // Reset the device if there is an error
      digitalWrite(resetPin, LOW);
      delay(100);
      digitalWrite(resetPin, HIGH);
    }
    
    https.end();  // Free resources
  }
  else {
    Serial.println("WiFi Disconnected");
    Serial.print("Connecting to ");
    Serial.println(ssid);
    WiFi.begin(ssid, password);  // Reconnect to WiFi network
    
    while (WiFi.status() != WL_CONNECTED) {  // Wait for WiFi connection
      delay(500);
      Serial.print(".");
    }
  }
  
  delay(60000);  // Delay for 60 seconds
}
