<?php
session_name('Monkeychat');
session_start();
// Create connection
$conn = new mysqli('localhost','root','','monkeychat');
// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}
$requests = $conn->query("SELECT friendID FROM requests WHERE acceptID = {$_SESSION['userID']}")->num_rows;
echo $requests;
?>