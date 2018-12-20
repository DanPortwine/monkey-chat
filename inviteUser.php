<?php
require 'session.php';
require 'connection.php';
// cheacks that the entry is not empty
if (empty($_POST['userToInvite'])){
	echo 'Username not entered';
}
$chatID = $_SESSION['chatID'];
$username = $_POST['userToInvite'];
if (isset(mysqli_fetch_row($conn->query("SELECT userID FROM users WHERE username = '{$username}'"))[0])){
	$userID = mysqli_fetch_row($conn->query("SELECT userID FROM users WHERE username = '{$username}'"))[0];
	// checks if the user has already been invited
	foreach ($conn->query("SELECT userID FROM chatinvites WHERE chatID = {$chatID}") as $row){
		if ($row['userID'] == $userID){
			echo 'You have already invited this user into this chat';
			$userInvited = true;
			break;
		}
	}
	// checks if the user is already a member of the caht
	foreach ($conn->query("SELECT memberID FROM chatmembers WHERE chatID = {$chatID}") as $row){
		if ($userID == $row['memberID']){
			echo 'User is already a member if this chat';
			$userMember = true;
			break;
		}
	}
	// invites the user to the chat
	if (empty($userInvited) && empty($userMember)){
		if ($conn->query("INSERT INTO chatinvites (chatID,userID) VALUES ({$chatID},{$userID})")){
			echo 'User invited successfully';
		}
	}
} else {
	echo 'User does not exist';
}
echo '<br><button class="closeAlert" id="userInvitedCloseButton" onclick="$(`#messagePopupText`).hide();$(`#messageEntryBoxCool`).focus();">Close</button>';
require 'footer.php';
?>