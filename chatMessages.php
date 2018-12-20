<?php
require 'session.php';
// Create connection
$conn = new mysqli('localhost','root','','monkeychat');
// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}
$chatID = $_SESSION['chatID'];
$messageCount = 0;
foreach ($conn->query("SELECT messageID FROM privateMessages WHERE chatID = {$chatID}") as $row){
	$id = $row['messageID'];
	if ($messageCount == 0){
		$earliestMessage = $id;
	}
	if ($messageCount == 500){ // when the number of messages reaches 501, the earliest message is deleted
		$conn->query("DELETE FROM privateMessages WHERE messageID = {$earliestMessage};");
	} else {
		$timeStamp = mysqli_fetch_row($conn->query("SELECT messageTime FROM privateMessages WHERE messageID = {$id}"))[0];
		$userID = mysqli_fetch_row($conn->query("SELECT senderID FROM privateMessages WHERE messageID = {$id}"))[0];
		$username = mysqli_fetch_row($conn->query("SELECT username FROM users WHERE userID = {$userID}"))[0];
		$message = mysqli_fetch_row($conn->query("SELECT messageBody FROM privateMessages WHERE messageID = {$id}"))[0];
		$messageIcon = mysqli_fetch_row($conn->query("SELECT userIcon FROM users WHERE userID = {$userID}"))[0];
		if (in_array(substr($message,1,-1) . '.png',scandir('images/stickers/')) && $message == ':' . substr($message,1,-1)
				. ':'){ // if the message is a sticker
			$stickerMessage = true;
		} else {
			$stickerMessage = false;
			$stickersList = [];
			$count = 0; // finds the number of files in the stickers directory
			foreach (scandir('images/stickers/') as $sticker){
				if ($count > 2){
					array_push($stickersList,':' . substr($sticker,0,-4) . ':');
				}
				$count += 1;
			}
			foreach ($stickersList as $stickerName){
				$message = str_replace($stickerName,'
					<img class="inlineSticker" src="images/stickers/' . substr($stickerName,1,-1) . '.png">',$message);
			}
		}
		if ($username == 'Bot'){ // styling for the Bot's messages
			$backgroundClass = 'messageBot';
			$nameClass = 'messageNameBot';
			$messageClass = 'messageBodyBot';
		} else if ($username == 'Dan'){ // styling for my messages
			$backgroundClass = 'messageMe';
			$nameClass = 'messageNameMe';
			$messageClass = 'messageBodyMe';
		} else { // styling for everyone else's messages
			if ($messageCount % 2 == 0){ // alternates the background colour of reqular user's messages
				$backgroundClass = 'message';
			} else {
				$backgroundClass = 'messageAlt';
			}
			$nameClass = 'messageName';
			$messageClass = 'messageBody';
		}
		// display message
		$fontID = mysqli_fetch_row($conn->query("SELECT fontID FROM privateChats WHERE chatID = {$chatID}"))[0];
		$font = mysqli_fetch_row($conn->query("SELECT val FROM chatStyles WHERE styleID = {$fontID}"))[0];
		$colourID = mysqli_fetch_row($conn->query("SELECT colourID FROM privateChats WHERE chatID = {$chatID}"))[0];
		$colour = mysqli_fetch_row($conn->query("SELECT val FROM chatStyles WHERE styleID = {$colourID}"))[0];
		if ($stickerMessage == false){ // text message
			echo '<div class="messageContainer">
					<div class="' . $backgroundClass . '">
						<div class="messageUserDetails"><img class="messageIcon" src="images/icons/' . $messageIcon . '.png">
							<p class="messagesNameBody">
							<span class="' . $nameClass . '">' . $username . ' [' . substr($timeStamp,11,8) . '] :&nbsp;
							</span>
							<span style="float:left;margin-left:50px;margin-bottom:5px;color:' . $colour . ';font-family:'
								. $font . '">' . $message . '</span>
							</p>
						</div>
					</div>
				</div>';
		} else if ($stickerMessage == true){ // sticker message
			echo '<div class="messageContainer">
					<div class="' . $backgroundClass . '">
						<img class="messageIcon" src="images/icons/' . $messageIcon . '.png">
						<p class="messagesNameBody">
						<span class="' . $nameClass . '">' . $username . ' [' . substr($timeStamp,11,8) . '] :&nbsp;</span>
						</p>
						<span style="float:left;margin-bottom:5px;color:' . $colour . ';font-family:' . $font . '">
						<img class="sticker" src="images/stickers/' . substr($message,1,-1) . '.png">
						</span>
					</div>
				</div>';
		}
	}
	$messageCount++;
}
?>