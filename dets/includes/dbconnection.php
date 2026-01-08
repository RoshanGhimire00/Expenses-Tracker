<?php
$host = "localhost";
$username = "root";
$password = ""; // Empty for default XAMPP
$database = "detsdb";

// Create connection
$con = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Optional: Set charset to utf8
mysqli_set_charset($con, "utf8");

// For debugging (remove in production)
// echo "Connected successfully to database: " . $database;
?>