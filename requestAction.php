<?php
require 'header.php';
$requestID = $_POST['userID'];
// add the users' IDs into the friends table
if (isset($_POST['acceptRequest'])){
	$conn->query("INSERT INTO friends (requestID,acceptID) VALUES ('{$requestID}','{$_SESSION['userID']}')");
}
// delete the request
$conn->query("DELETE FROM requests WHERE requestID = {$requestID}");
echo '<script>window.location="profile.php"</script>;';
require 'footer.php';
?>