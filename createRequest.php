<?php
require 'header.php';
$friendUserID = mysqli_fetch_row($conn->query("SELECT userID FROM users WHERE username = '{$_POST['username']}'"))[0]; // gets the userID of the user that is receiving the request
// checks if the user has already sent a friend request to the user
foreach ($conn->query("SELECT friendID FROM requests WHERE requestID = '{$_SESSION['userID']}'") as $row){
	$id = $row['friendID'];
	$acceptID = mysqli_fetch_row($conn->query("SELECT acceptID FROM requests WHERE friendID = {$id}"))[0];
	if ($acceptID == $friendUserID){
		$requestSent = true;
		break;
	}
}
// checks if the users are already friends
foreach ($conn->query("SELECT friendshipID FROM friends WHERE requestID = '{$_SESSION['userID']}'") as $row){
	$id = $row['friendshipID'];
	$acceptID = mysqli_fetch_row($conn->query("SELECT acceptID FROM friends WHERE friendshipID = {$id}"))[0];
	if ($acceptID == $friendUserID){
		$alreadyFriends = true;
		break;
	}
}
if ($conn->query("SELECT userID FROM users WHERE userID = {$friendUserID}") == true){ // checks that the user being requested exists
	$userAvailable = true;
}
// verifies whether the request can be sent
$friendUserType = mysqli_fetch_row($conn->query("SELECT userType FROM users WHERE userID = '{$friendUserID}'"))[0];
if (empty($requestSent) && empty($alreadyFriends) && empty($userAvailable)){
	$_SESSION['message'] = 'This user does not exist';
	echo '<script>window.location = "profile.php";</script>';
} else if ($_SESSION['userID'] == $friendUserID){
	$_SESSION['message'] = 'You can not be your own friend';
	echo '<script>window.location = "profile.php";</script>';
} else if ($userType != $friendUserType){
	if ($friendUserType == 'child'){
		$_SESSION['message'] = 'You can not be friends with children';
		echo '<script>window.location = "profile.php";</script>';
	} else if ($friendUserType == 'parent'){
		$_SESSION['message'] = 'You can not be friends with parents';
		echo '<script>window.location = "profile.php";</script>';
	}
} else if (isset($requestSent)){
	$_SESSION['message'] = 'You have already requested to be friends with this user';
	echo '<script>window.location = "profile.php";</script>';
} else if (isset($alreadyFriends)){
	$_SESSION['message'] = 'You are already friends with this user';
	echo '<script>window.location = "profile.php";</script>';
}
// sends the request if the conditions have been met
if ($userAvailable == true){
	$conn->query("INSERT INTO requests (requestID,acceptID) VALUES ('{$_SESSION['userID']}','{$friendUserID}');");
	$_SESSION['message'] = 'Request sent successfully';
	echo '<script>window.location = "profile.php";</script>';
}
require 'footer.php';
?>