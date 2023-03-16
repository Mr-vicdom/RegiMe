<?php
$servername = "sql.freedb.tech:3306";
$username = "freedb_vicdom";
$password = "keB7?NPV**EfNW2";


// Create connection
$conn = mysqli_connect($servername, $username, $password);

// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully";
?>