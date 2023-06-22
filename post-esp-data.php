<?php
$servername = "-----------";  // Server name or IP address
$dbname = "-----------";  // Database name
$username = "----------";  // Database username
$password = "---------";  // Database password
$api_key_value = "-----------";  // API key for authentication

$api_key = $temperature = $pressure = $altitude = $humidity = "";  // Initialize variables

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $api_key = test_input($_POST["api_key"]);  // Get the API key from the POST data
    
    if ($api_key == $api_key_value) {  // Check if the API key is valid
        $temperature = test_input($_POST["temperature"]);  // Get sensor data from POST data
        $pressure = test_input($_POST["pressure"]);
        $altitude = test_input($_POST["altitude"]);
        $humidity = test_input($_POST["humidity"]);

        // Create connection to the database
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "INSERT INTO SensorData (temperature, pressure, altitude, humidity)
        VALUES ('" . $temperature . "', '" . $pressure . "', '" . $altitude . "', '" . $humidity . "')";

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
