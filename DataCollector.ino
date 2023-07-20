#include <WiFi.h>
#include <WiFiClientSecure.h>
#include <HTTPClient.h>
#include <Wire.h>
#include "FS.h"
#include "SD.h"
#include "SPI.h"
#include <Adafruit_Sensor.h>
#include <Adafruit_BME280.h>
#include <vector>

#define SEALEVELPRESSURE_HPA (1013.25)
const char* ssid = "----------";
const char* password = "---------";
const char* serverName = "-----------";
String apiKeyValue = "---------";
Adafruit_BME280 bme; // I2C
unsigned long startTime;
unsigned long startTimeout;
unsigned long timeout = 10000;
unsigned long elapsedTime = 0;
unsigned long offline = 0;
int wifiMode = A0;
int wifiModeValue = 0;
WiFiClientSecure *client = nullptr;
HTTPClient https;
u_int64_t sleepTime = 50000000; // 50 seconds in microseconds

void checkWifiConnection(){
  Serial.print("Connecting to ");
  Serial.println(ssid);
  WiFi.begin(ssid, password);
  startTimeout = millis();
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
    if(millis() - startTimeout >= timeout){
      Serial.println("Failed to connect");
      break;
    }
  }
}

void uploadDataToServer(String avgTemp, String avgPress, String avgAlt, String avgHum, String offline, String interval){
  https.begin(*client, serverName);
  https.addHeader("Content-Type", "application/x-www-form-urlencoded");
  String httpRequestData = "api_key=" + apiKeyValue + "&temperature=" + avgTemp + "&pressure=" + avgPress + "&altitude=" + avgAlt + "&humidity=" + avgHum + "&offline=" + offline + "&interval=" + interval;
  Serial.print("httpRequestData: ");
  Serial.println(httpRequestData);
  int httpResponseCode = https.POST(httpRequestData);
  if (httpResponseCode>0) {
    Serial.print("HTTP Response code: ");
    Serial.println(httpResponseCode);
  }
  https.end();
}

std::vector<String> parseDataString(const String& dataString) {
  std::vector<String> measurements;

  int startIndex = 0;
  int endIndex = 0;

  // Parse temperature
  startIndex = dataString.indexOf("temp=") + 5;
  endIndex = dataString.indexOf(" ", startIndex);
  String tempString = dataString.substring(startIndex, endIndex);
  measurements.push_back(tempString);

  // Parse pressure
  startIndex = dataString.indexOf("press=") + 6;
  endIndex = dataString.indexOf(" ", startIndex);
  String pressString = dataString.substring(startIndex, endIndex);
  measurements.push_back(pressString);

  // Parse altitude
  startIndex = dataString.indexOf("alt=") + 4;
  endIndex = dataString.indexOf(" ", startIndex);
  String altString = dataString.substring(startIndex, endIndex);
  measurements.push_back(altString);

  // Parse humidity
  startIndex = dataString.indexOf("hum=") + 4;
  endIndex = dataString.indexOf(" ", startIndex);
  String humString = dataString.substring(startIndex, endIndex);
  measurements.push_back(humString);

  // Parse offline
  startIndex = dataString.indexOf("offline=") + 8;
  endIndex = dataString.indexOf(" ", startIndex);
  String offlineString = dataString.substring(startIndex, endIndex);
  measurements.push_back(offlineString);

  // Parse interval
  startIndex = dataString.indexOf("interval=") + 9;
  endIndex = dataString.indexOf("\n", startIndex);
  String intervalString = dataString.substring(startIndex, endIndex);
  measurements.push_back(intervalString);

  return measurements;
}

void deepSleep(u_int64_t microseconds) {
  esp_sleep_enable_timer_wakeup(microseconds);
  esp_deep_sleep_start();
}

void readFile(fs::FS &fs, const char * path){
    Serial.printf("Reading file: %s\n", path);

    File file = fs.open(path);
    if(!file){
        Serial.println("Failed to open file for reading");
        return;
    }

    Serial.print("Read from file: ");
    while(file.available()){
        Serial.write(file.read());
    }
    file.close();
}

void writeFile(fs::FS &fs, const char * path, const char * message){
    Serial.printf("Writing file: %s\n", path);

    File file = fs.open(path, FILE_WRITE);
    if(!file){
        Serial.println("Failed to open file for writing");
        return;
    }
    if(file.print(message)){
        Serial.println("File written");
    } else {
        Serial.println("Write failed");
    }
    file.close();
}

void appendFile(fs::FS &fs, const char * path, const char * message){
    Serial.printf("Appending to file: %s\n", path);

    File file = fs.open(path, FILE_APPEND);
    if(!file){
        Serial.println("Failed to open file for appending");
        return;
    }
    if(file.print(message)){
        Serial.println("Message appended");
    } else {
        Serial.println("Append failed");
    }
    file.close();
}




void setup() {
  Serial.begin(115200);
  wifiModeValue = analogRead(A0);
  Serial.println(wifiModeValue);

  if(!SD.begin()){
      Serial.println("Card Mount Failed");
      return;
  }

  bool status = bme.begin(0x77);  
  if (!status) {
    Serial.println("Could not find a valid BME280 sensor");
    while (1);
  }

  if(wifiModeValue > 0){
    checkWifiConnection();
    client = new WiFiClientSecure;
    client->setInsecure(); // Don't use SSL certificate
  }
}

void loop() {
  File file = SD.open("/data.txt");
  if(file && wifiModeValue > 0){
    while (file.available()) {
      String data = file.readStringUntil('\n');
      std::vector<String> measuredValues = parseDataString(data);
      String temperature = measuredValues[0];
      String pressure = measuredValues[1];
      String altitude = measuredValues[2];
      String humidity = measuredValues[3];
      String offline = measuredValues[4];
      String interval = measuredValues[5];
      uploadDataToServer(temperature, pressure, altitude, humidity, offline, interval);
      delay(1000); // Delay between uploads to prevent overwhelming the server
    }
    file.close();
    SD.remove("/data.txt");
    offline = 0;
  }
  startTime = millis();
  double avgTemp = 0;
  double avgPress = 0;
  double avgAlt = 0;
  double avgHum = 0;
  int numReadings = 10;
  for(int i = 0; i < numReadings; i++){
    avgTemp += bme.readTemperature();
    avgPress += bme.readPressure() / 100.0F;
    avgAlt += bme.readAltitude(SEALEVELPRESSURE_HPA);
    avgHum += bme.readHumidity();
    delay(1000);
  }
  avgTemp /= numReadings;
  avgTemp = avgTemp * 1.8 + 32;
  avgPress /= numReadings;
  avgAlt /= numReadings;
  avgHum /= numReadings;

  if(wifiModeValue > 0){
    uploadDataToServer(String(avgTemp), String(avgPress), String(avgAlt), String(avgHum), "0", "0");
  }
  else {
    char buffer[50]; // Buffer to hold the converted string values
    int pressIntDigits = floor(avgPress);
    int pressDecDigits = 2;
    int altIntDigits = floor(avgAlt);
    int altDecDigits = 2;
    snprintf(buffer, sizeof(buffer), "temp=%.2f press=%.*f alt=%.*f hum=%.2f", avgTemp, pressDecDigits, avgPress, altDecDigits, avgAlt, avgHum);
    String dataString = buffer;
    Serial.println(dataString);
    elapsedTime = millis() - startTime;
    offline++;
    dataString += " offline=" + String(offline) + " interval=" + String(elapsedTime) + "\n";
    File data = SD.open("/data.txt");
    if(data){
      appendFile(SD, "/data.txt", dataString.c_str());
    }
    else{
      writeFile(SD, "/data.txt", dataString.c_str());
    }
  }
  deepSleep(sleepTime);
}