<?php
$servername = "127.0.0.1";
$username = "root";
$password = "team27pass";
$dbname = "team27";
$port = 3306;


$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    // echo "Connected successfully";
}
mysqli_query($conn, "SET SESSION sql_mode = ''");
?>
