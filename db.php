<?php
$host = "sql100.infinityfree.com";
$user = "if0_41150632";
$pass = "aswinvijith";
$db   = "if0_41150632_users";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>