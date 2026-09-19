<?php
$conn = new mysqli("localhost", "root", "", "smartwater");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }
?>