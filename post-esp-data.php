<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "";
$dbname = "";
$username = "";
$password = "";
$api_key_value = "";

$api_key = $temperature = $pressure = $altitude = $humidity = $offline = $interval = "";  // Initialize variables

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $api_key = test_input($_POST["api_key"]);  // Get the API key from the POST data
    
    if ($api_key == $api_key_value) {  // Check if the API key is valid
        $temperature = test_input($_POST["temperature"]);  // Get sensor data from POST data
        $pressure = test_input($_POST["pressure"]);
        $altitude = test_input($_POST["altitude"]);
        $humidity = test_input($_POST["humidity"]);
        $offline = test_input($_POST["offline"]);
        $interval = test_input($_POST["interval"]);

        // Create connection to the database
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } else {
            echo "Connected successfully";
        }

        $sql = "INSERT INTO `SensorData` (`temperature`, `pressure`, `altitude`, `humidity`, `offline`, `delay`) VALUES ('" . $temperature . "', '" . $pressure . "', '" . $altitude . "', '" . $humidity . "', '" . $offline . "', '" . $interval . "');";

        echo "SQL query: " . $sql;

        if ($conn->query($sql) === TRUE) {  // Execute the SQL query
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $conn->close();  // Close the database connection
    } else {
        echo "Wrong API Key provided.";
    }
} else {
    echo "No data posted with HTTP POST.";
}

function test_input($data) {  // Function to sanitize input data
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Add CORS headers to allow cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
