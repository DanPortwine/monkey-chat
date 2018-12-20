<?php
require 'header.php';
if ($_POST['stickerName'] != '') {
	$conn->query("INSERT INTO privateMessages (senderID,chatID,messageBody) VALUES ('{$_SESSION['userID']}','{$_SESSION['chatID']}','{$_POST['stickerName']}');"); // inserts sticker data into database
	$latestMessage = mysqli_fetch_row($conn->query("SELECT messageTime FROM privateMessages WHERE messageID = {$conn->insert_id}"))[0]; // finds the timestamo of the new message
	$conn->query("UPDATE privateChats SET latestMessageTime = '{$latestMessage}' WHERE chatID = {$_SESSION['chatID']}"); // adds the latest message time to privatechats to enable ordering
} else {
	$_SESSION['message'] = 'You can not send an empty message';
}
echo '<script>window.location = "' . $_SESSION['page'] . '";</script>';
require 'footer.php';
?>