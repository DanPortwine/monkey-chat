<?php
require 'session.php';
require 'connection.php';
$chat = substr($_SESSION['page'],0,-4) . 'chat';
$messageIDQuery = 'SELECT messageID FROM ' . $chat;
if ($chat != 'globalchat' and $chat != 'parentchat'){ // if the chat is not global or parent
	$chat = 'privatemessages';
	// alter the sql to accomodate private chats
	$messageIDQuery = 'SELECT messageID FROM ' . $chat . ' WHERE chatID = ' . $_SESSION['chatID'];
}
$rows = $conn->query($messageIDQuery)->num_rows; // finds the number of rows in the table
if ($rows > 0){
	if ($chat != 'privatemessages'){
		if ($rows == 90){ // when the number of messages in the chat reaches 90, the bot sends a warning message
			$conn->query("INSERT INTO {$chat} (messageBody,userID) 
				VALUES ('Chat will be reset in 10 messages time',2);");
		}
		if ($rows == 101){ // when the number of messages reaches 101, the chat is reset
			$conn->query("DELETE FROM {$chat};");
			$conn->query("ALTER TABLE {$chat} AUTO_INCREMENT = 0;");
		}
	}
	$messageCount = 0;
	foreach ($conn->query($messageIDQuery) as $row){
		$id = $row['messageID'];
		// collect message data from database
		$timeStamp = mysqli_fetch_row($conn->query("SELECT messageTime FROM {$chat} WHERE messageID = {$id}"))[0];
		$userID = mysqli_fetch_row($conn->query("SELECT userID FROM {$chat} WHERE messageID = {$id}"))[0];
		$username = mysqli_fetch_row($conn->query("SELECT username FROM users WHERE userID = {$userID}"))[0];
		$userType = mysqli_fetch_row($conn->query("SELECT userType FROM users WHERE userID = {$userID}"))[0];
		$message = mysqli_fetch_row($conn->query("SELECT messageBody FROM {$chat} WHERE messageID = {$id}"))[0];
		$messageIcon = mysqli_fetch_row($conn->query("SELECT userIcon FROM users WHERE userID = {$userID}"))[0];
		if (in_array(substr($message,1,-1) . '.png',scandir('images/stickers/')) && $message == ':' 
			. substr($message,1,-1) . ':'){ // if the message is a sticker
			$stickerMessage = true;
		} else {
			$stickerMessage = false;
			$stickersList = [];
			$count = 0; // finds the number of files in the stickers directory
			foreach (scandir('images/stickers/') as $sticker){
				if ($count > 1){
					array_push($stickersList,':' . substr($sticker,0,-4) . ':');
				}
				$count += 1;
			}
			foreach ($stickersList as $stickerName){
				$message = str_replace($stickerName,'<img class="inlineSticker" src="images/stickers/' 
					. substr($stickerName,1,-1) . '.png">',$message);
			}
		}
		if ($userType == 'bot'){ // styling for the Bot's messages
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
		if ($stickerMessage == false){ // text message
			echo '<div class="messageContainer">
					<div class="' . $backgroundClass . '">
						<div class="messageUserDetails">
							<img class="messageIcon" src="images/icons/' . $messageIcon . '.png">
							<p class="messagesNameBody ' . $messageClass . '">
							<span class="' . $nameClass . '">' . $username . ' [' . substr($timeStamp,11,8) . '] 
								:&nbsp;</span>' . $message . '
							</p>
						</div>';
						if ($_SESSION['userType'] != 'admin'){
							// fixed bug where multiple popups appearing
							// added $(`#messagePopupText`).text(``);
							echo '<button class="reportMessageButton" id="reportButton" 
									onclick="$(`#messagePopupText`).text(``); var id = ' . $id . '; 
									$.post(`popups.php`,{popupType: `report`,messageID: id},
									function(data,status){$(`#messagePopupText`).prepend(data).show();})">
									Report</button>';
						}
					echo '</div>
				</div>';
		} else if ($stickerMessage == true){ // sticker message
			echo '<div class="messageContainer">
					<div class="' . $backgroundClass . '">
						<img class="messageIcon" src="images/icons/' . $messageIcon . '.png">
						<p class="messagesNameBody">
						<span class="' . $nameClass . '">' . $username . ' [' . substr($timeStamp,11,8) . '] 
							:&nbsp;</span>
						<span class="' . $messageClass . '">
						</p><br><br>
						<img class="sticker" src="images/stickers/' . substr($message,1,-1) . '.png">
					</div>
				</div>';	
		}
		$messageCount++;
	}
}
?>