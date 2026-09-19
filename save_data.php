<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $temp = $_POST['temp'];
    $turb = $_POST['turb'];
    $status = $_POST['status'];

    $sql = "INSERT INTO mesures (temp, turbidity, status) VALUES ('$temp', '$turb', '$status')";
    if ($conn->query($sql) === TRUE) { echo "OK"; } 
    else { echo "Error: " . $conn->error; }
}
?>