<?php
require 'header.php';
echo 'message to be processed';
// htmlspecialchars prevents html or js code from being run when sent as message
$message = htmlspecialchars(mysqli_real_escape_string($conn,$_POST['messageBody']));
$profanitiesFile = fopen('profanities.txt','r'); //opens the text file containing banned words
$profanities = array();
while (!feof($profanitiesFile)){ // goes through each line of the file until the last line
	array_push($profanities,fgets($profanitiesFile)); // appends each line to the array
}
fclose($profanitiesFile); // closes the text file containing the banned words
$clean = true;
foreach ($profanities as $prof){ // loops through the banned words (in the array)
	$profFound = stripos($_POST['messageBody'],substr($prof,0,-2)); // checks if the string of the current banned word is present in the message
	if ($profFound !== false){
		$clean = false;
		$_SESSION['message'] = 'You used a banned word so your message will not be sent!';
	}
}
if ($clean == true){ // if none of the banned words' strings are detected in the message
	$spacesRemoved = str_replace(' ','',$_POST['messageBody']); // removes the spaces from the message
	if (count($_POST['messageBody']) > 0 && $spacesRemoved != ''){ // if the message is not empty
		if (substr($_SESSION['page'],0,-4) == 'global' or substr($_SESSION['page'],0,-4) == 'parent'){
			$chat = substr($_SESSION['page'],0,-4) . 'Chat';
			$conn->query("INSERT INTO {$chat} (messageBody,userID) VALUES ('{$message}','{$_SESSION['userID']}');"); // inserts message into database	
		} else {
			$conn->query("INSERT INTO privateMessages (messageBody,userID,chatID) VALUES ('{$message}','{$_SESSION['userID']}','{$_SESSION['chatID']}');"); // inserts message into database
			$latestMessage = $conn->insert_id; // finds the timestamp of the new message
			$conn->query("UPDATE privateChats SET latestMessage = '{$latestMessage}' WHERE chatID = {$_SESSION['chatID']}"); // adds the latest message time to privatechats to enable ordering
		}
	} else {
		$_SESSION['message'] = 'You can not send an empty message';
	}
}
echo 'message processed';
//echo '<script>window.location = "' . $_SESSION['page'] . '";</script>'; // redirects to the page the user was on
require 'footer.php';
?>