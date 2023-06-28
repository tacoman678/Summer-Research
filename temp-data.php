<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "---------------";
$dbname = "----------------";
$username = "---------------";
$password = "---------------";

// Create connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, temperature, reading_time, pressure, altitude, humidity FROM SensorData ORDER BY id DESC";

$result = $conn->query($sql);
$timeData = array();
$temperatureData = array();
$pressureData = array();
$altitudeData = array();
$humidityData = array();

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row_id = $row["id"];
        $row_temp = $row["temperature"];
        $row_reading_time = $row["reading_time"];
        $row_reading_time = date("Y-m-d H:i:s", strtotime("$row_reading_time - 3 hours")); // Adjust time zone if necessary
        $row_press = $row["pressure"];
        $row_alt = $row["altitude"];
        $row_hum = $row["humidity"];
        $timeData[] = $row_reading_time;
        $temperatureData[] = $row_temp;
        $pressureData[] = $row_press;
        $altitudeData[] = $row_alt;
        $humidityData[] = $row_hum;
    }
    $result->free();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

$data = array(
    'timeData' => $timeData,
    'temperatureData' => $temperatureData,
    'pressureData' => $pressureData,
    'altitudeData' => $altitudeData,
    'humidityData' => $humidityData
);

echo json_encode($data);
?>
