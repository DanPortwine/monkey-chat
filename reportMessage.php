<?php
require 'session.php';
require 'connection.php';
// set up which table to query to get the reported message
$chat = substr($_SESSION['page'],0,-4) . 'chat';
$messageIDQuery = 'SELECT messageID FROM ' . $chat;
if ($chat != 'globalchat' and $chat != 'parentchat'){
	$chat = 'privatemessages';
}
// get details of reported message
$messageID = $_POST['messageID'];
$messageBody = mysqli_fetch_row($conn->query("SELECT messageBody FROM {$chat} WHERE messageID = {$messageID}"))[0];
$offenderID = mysqli_fetch_row($conn->query("SELECT userID FROM {$chat} WHERE messageId = {$messageID}"))[0];
$reporterID = $_SESSION['userID'];
$userType = mysqli_fetch_row($conn->query("SELECT userType FROM users WHERE userID = {$reporterID}"))[0];
$message = "";
if ($offenderID == $reporterID){
	$message = 'You can not report yourself!';
} else {
	// child reports go to reportedmessage table
	if ($userType == 'child'){
		$conn->query("INSERT INTO reportedmessages (messageBody,offenderID,reporterID) VALUES ('{$messageBody}','{$offenderID}','{$reporterID}')");
		$message = 'You have successfully reported this message to your parent account';
	// parent reports open the report popup
	} else if ($userType == 'parent'){
		$message = 'Select the appropriate reason for reporting:<br>
				<form class="" action="confirmReport.php" method="post">
					<select class="alertSelect" name="reason">
						<option value="1">Abuse</option>
						<option value="2">Advertising</option>
						<option value="3">Fake account</option>
						<option value="4">Grooming</option>
						<option value="5">Harassment</option>
						<option value="6">Identity distribution</option>
						<option value="7">Spam</option>
						<option value="8">Swearing</option>
					</select>
					<input type="number" style="display:none" name="offenderID" value="' . $offenderID . '">
					<input type="number" style="display:none" name="reportID" value="' . $reportID . '">
					<input type="text" style="display:none" name="messageBody" value="' . $messageBody . '">
					<input class="closeAlert" type="submit" value="Report">
				</form>
		';
	}
	echo $_SESSION['page'] . $chat . $message . '<br><button class="closeAlert" onclick="$(`#messagePopupText`).hide().html(``);">Cancel</button>';
}
?>