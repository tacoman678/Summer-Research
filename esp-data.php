<!DOCTYPE html>
<html>
  <head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>  <!-- Include Chart.js library -->
   </head>
<body>
  <canvas id="tempChart"></canvas>  <!-- Temperature Chart -->
  <canvas id="pressChart"></canvas>  <!-- Pressure Chart -->
  <canvas id="altChart"></canvas>  <!-- Altitude Chart -->
  <canvas id="humChart"></canvas>  <!-- Humidity Chart -->
  <table cellspacing="5" cellpadding="5">
    <tr> 
        <td>ID</td> 
        <td>Temperature (F)</td>
        <td>Pressure (hPa)</td>
        <td>Altitude (m)</td>
        <td>Humidity (%)</td>
        <td>Reading Time</td>
    </tr>

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
        $row_reading_time = date("Y-m-d H:i:s", strtotime("$row_reading_time - 3 hours"));  // Adjust time zone if necessary
        $row_press = $row["pressure"];
        $row_alt = $row["altitude"];
        $row_hum = $row["humidity"];
        $timeData[] = $row_reading_time;
        $temperatureData[] = $row_temp;
        $pressureData[] = $row_press;
        $altitudeData[] = $row_alt;
        $humidityData[] = $row_hum;
        echo '<tr> 
                <td>' . $row_id . '</td> 
                <td>' . $row_temp . '</td>  
                <td>' . $row_press . '</td> 
                <td>' . $row_alt . '</td> 
                <td>' . $row_hum . '</td>
                <td>' . $row_reading_time . '</td>
              </tr>';
    }
    $result->free();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
</table>

<script>
    var timeData = <?php echo json_encode(array_reverse($timeData)); ?>;  // Reverse the timeData array for proper chronological order
    var temperatureData = <?php echo json_encode(array_reverse($temperatureData)); ?>;
    var pressureData = <?php echo json_encode(array_reverse($pressureData)); ?>;
    var altitudeData = <?php echo json_encode(array_reverse($altitudeData)); ?>;
    var humidityData = <?php echo json_encode(array_reverse($humidityData)); ?>;

    // Temperature vs Time Chart
    var ctx1 = document.getElementById('tempChart').getContext('2d');
    var tempChart = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: timeData,
            datasets: [{
                label: 'Temperature (degrees F)',
                data: temperatureData,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Time'
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Temperature (degrees F)'
                    }
                }
            }
        }
    });

    // Pressure vs Time Chart
    var ctx2 = document.getElementById('pressChart').getContext('2d');
    var pressChart = new Chart(ctx2, {
        type: 'line',
        data: {
            labels: timeData,
            datasets: [{
                label: 'Pressure (hPa)',
                data: pressureData,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Time'
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Pressure (hPa)'
                    }
                }
            }
        }
    });

    // Altitude vs Time Chart
    var ctx3 = document.getElementById('altChart').getContext('2d');
    var altChart = new Chart(ctx3, {
        type: 'line',
        data: {
            labels: timeData,
            datasets: [{
                label: 'Altitude (m)',
                data: altitudeData,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Time'
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Altitude (m)'
                    }
                }
            }
        }
    });

    // Humidity vs Time Chart
    var ctx4 = document.getElementById('humChart').getContext('2d');
    var humChart = new Chart(ctx4, {
        type: 'line',
        data: {
            labels: timeData,
            datasets: [{
                label: 'Humidity (%)',
                data: humidityData,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Time'
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Humidity (%)'
                    }
                }
            }
        }
    });
</script>
</body>
</html>
