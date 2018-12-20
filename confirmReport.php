<?php
require 'header.php';
// defines the data about the report
$reason = $_POST['reason'];
$reporterID = $_SESSION['userID'];
$offenderID = $_POST['offenderID'];
$reportID = $_POST['reportID'];
$messageBody = $_POST['messageBody'];
$conn->query("INSERT INTO confirmedreports (messageBody,reasonID,offenderID,reporterID) VALUES ('{$messageBody}','{$reason}','{$offenderID}','{$reporterID}');");
$conn->query("DELETE FROM reportedmessages WHERE reportID = {$reportID}");
$_SESSION['message'] = 'Report successfully sent';
// the parent is redirected back to the page they were on
if ($_SESSION['parentReport'] == true){
	echo '<script>window.location=("' . $_SESSION['page'] . '");</script>';
} else {
	echo '<script>window.location=("profile.php");</script>';
}
require 'footer.php';
?>