# Meteorological Data Collection System

## Overview

This project implements an ESP32-based system for capturing meteorological data using the Adafruit BME280 sensor. The collected data, including temperature, pressure, altitude, and humidity, is transmitted securely to a remote SQL database over a Wi-Fi network. The system ensures data integrity during intermittent connectivity and provides data logging on a microSD card for retrieval during network outages.

## Project Components

### 1. `temp.html`

- Web-based application for visualizing and analyzing temperature data.
- Utilizes Bulma CSS framework, Chart.js library, and a responsive design for optimal user experience.
- Provides a modal template for displaying data in a tabular format.

### 2. `post-esp-data.php`

- PHP script to handle HTTP POST requests from the ESP32.
- Validates API key and inserts sensor data into a SQL database.

### 3. `getData.php`

- PHP script to retrieve sensor data for a specific day from the SQL database.
- Returns data in JSON format for visualization.

### 4. `filtered-temp.php`

- PHP script to retrieve distinct dates with recorded sensor data.
- Used to populate buttons in the web application for available days.

### 5. `esp-data.php`

- HTML and JavaScript for displaying real-time sensor data from the ESP32.
- Uses Chart.js to create line charts for temperature, pressure, altitude, and humidity.

### 6. `DataCollector.ino`

- Arduino sketch for the ESP32 microcontroller.
- Collects sensor data, uploads it to the server, and logs offline data to a microSD card.
- Implements deep sleep mode for power efficiency.

