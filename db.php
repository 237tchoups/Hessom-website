<?php
$host = "localhost";
$user = "root";
$pass = ""; // default XAMPP password
$db   = "hessom_medical"; // replace with your database name

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>