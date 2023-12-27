<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$selectedDay = $_GET['selected-day']; // Retrieve selected day from query parameter

$servername = "";
$dbname = "";
$username = "";
$password = "";

// Create connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, temperature, reading_time, pressure, altitude, humidity FROM SensorData WHERE DATE(reading_time) = '$selectedDay' ORDER BY id DESC";

$result = $conn->query($sql);
$dataArray = array();

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $dataArray[] = $row;
    }
    $result->free();
} else {
    die("Error: " . $sql . "<br>" . $conn->error); // Use die to terminate and return an error message as plain text
}

$conn->close();

header('Content-Type: application/json'); // Set the response content type to JSON
echo json_encode($dataArray); // Encode the array as JSON and echo it
?>
