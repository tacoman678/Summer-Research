# Summer-Research
# Sensor Data Collection and Display

This repository contains three code files that work together to collect sensor data from a BME280 sensor connected to an ESP32 board, and display the collected data in a web interface.

## Files

### 1. post-esp-data.php

This PHP file handles the HTTP POST requests sent by the ESP32 board to store the sensor data in a MySQL database. It verifies the API key, extracts the temperature, pressure, altitude, and humidity values from the request, and inserts them into the "SensorData" table of the database. The file also includes CORS headers to allow cross-origin requests.

[View post-esp-data.php](https://github.com/tacoman678/Summer-Research/blob/main/post-esp-data.php)

### 2. esp-data.php

This PHP file generates a web page that displays the collected sensor data in a table format. It retrieves the data from the MySQL database and dynamically populates the table rows with the latest readings. The file also uses the Chart.js library to create line charts for temperature, pressure, altitude, and humidity over time.

[View esp-data.php](https://github.com/tacoman678/Summer-Research/blob/main/esp-data.php)

### 3. DataCollector.ino

This Arduino sketch is designed to run on an ESP32 board. It collects sensor readings from the BME280 sensor, calculates the average values over multiple readings, converts the temperature to Fahrenheit, and sends an HTTP POST request to the server with the collected data. The sketch also includes WiFi connectivity to ensure the ESP32 is connected to the internet.

[View DataCollector.ino](https://github.com/tacoman678/Summer-Research/blob/main/DataCollector.ino)

## Usage

1. Upload the `DataCollector.ino` sketch to your ESP32 board using the Arduino IDE or your preferred method.
2. Update the WiFi network credentials (`ssid` and `password`) in the sketch to match your network.
3. Set up a web server with PHP and MySQL support. Update the server details (`$servername`, `$dbname`, `$username`, `$password`) in both PHP files accordingly.
4. Create the MySQL database with the required table structure. You can find the table schema in the `post-esp-data.php` file.
5. Deploy the PHP files (`post-esp-data.php` and `esp-data.php`) to your web server.
6. Power on the ESP32 board and monitor the serial output to ensure successful connection and data posting.
7. Access the `esp-data.php` file through a web browser to view the sensor data table and charts.

Make sure to adjust any other relevant settings or configurations based on your specific setup.

