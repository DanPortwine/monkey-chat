<?php
require 'header.php';
// determines the account type of the user to be deleted
$accountType = mysqli_fetch_row($conn->query("SELECT userType FROM users WHERE userID = {$_POST['userID']}"))[0];
if ($accountType == 'child'){
	$conn->query("DELETE FROM childParent WHERE childID = {$_POST['userID']}");
	$conn->query("DELETE FROM users WHERE userID = {$_POST['userID']}");
	$conn->query("DELETE FROM globalChat WHERE userID = {$_POST['userID']}");
	echo '<script>window.location="profile.php";</script>';
} else if ($accountType == 'parent'){
	if ($conn->query("SELECT relationshipID FROM childParent WHERE parentID = {$_POST['userID']}")->num_rows > 0){
		// deletes the parent user's data in the right order if they have child account(s)
		foreach ($conn->query("SELECT childID FROM childParent WHERE parentID = {$_POST['userID']}") as $row){
			$conn->query("DELETE FROM childParent WHERE childID = {$row['childID']}");
			$conn->query("DELETE FROM users WHERE userID = {$row['childID']}");
			$conn->query("DELETE FROM globalChat WHERE userID = {$row['childID']}");
		}
	} else {
		// deletes the parent user's data in the right order if they don't have child account(s)
		$conn->query("DELETE FROM globalChat WHERE userID = {$_POST['userID']}");
		$conn->query("DELETE FROM users WHERE userID = {$_POST['userID']}");
	}
	echo '<script>window.location="logout.php";</script>';
}
require 'footer.php';
?>