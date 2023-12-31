<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

$sql = "SELECT DISTINCT DATE(reading_time) AS reading_date FROM SensorData";

$result = $conn->query($sql);
$datesArray = array();

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $reading_date = $row["reading_date"];
        $datesArray[] = $reading_date;
    }
    $result->free();
} else {
    die("Error: " . $sql . "<br>" . $conn->error); // Use die to terminate and return an error message as plain text
}

$conn->close();

header('Content-Type: application/json'); // Set the response content type to JSON
echo json_encode($datesArray); // Encode the array as JSON and echo it
?>
