<?php
include 'db_connect.php';
header('Content-Type: application/json');

$result = $conn->query("SELECT * FROM mesures ORDER BY id DESC LIMIT 1");

if ($result && $result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    // Raha mbola banga ny database dia mandefa sanda "par défaut"
    echo json_encode([
        "temp" => 0,
        "turbidity" => 0,
        "status" => "MIANDRY_DATA"
    ]);
}
?>