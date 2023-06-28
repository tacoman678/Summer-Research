var timeData;
var temperatureData;
var pressureData;
var altitudeData;
var humidityData;

function getData() {
    // AJAX request to fetch data from the server
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var data = JSON.parse(this.responseText);
            timeData = data.timeData.reverse(); // Reverse the timeData array for proper chronological order
            temperatureData = data.temperatureData.reverse();
            pressureData = data.pressureData.reverse();
            altitudeData = data.altitudeData.reverse();
            humidityData = data.humidityData.reverse();
            updateCharts();
            populateTable();
        }
    };
    xhr.open("GET", "data.php", true);
    xhr.send();
}

function updateCharts() {
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
}

function populateTable() {
    var table = document.querySelector('table');
    for (var i = 0; i < timeData.length; i++) {
        var row = table.insertRow(-1);
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);
        var cell4 = row.insertCell(3);
        var cell5 = row.insertCell(4);
        var cell6 = row.insertCell(5);
        cell1.innerHTML = i + 1;
        cell2.innerHTML = temperatureData[i];
        cell3.innerHTML = pressureData[i];
        cell4.innerHTML = altitudeData[i];
        cell5.innerHTML = humidityData[i];
        cell6.innerHTML = timeData[i];
    }
}

getData();
