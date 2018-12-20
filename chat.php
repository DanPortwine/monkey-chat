<?php
require 'header.php';
// finds the id of the chat that the user is in
$_SESSION['page'] = explode('/',$_SERVER['REQUEST_URI'])[2];
$chatID = $_GET['chatID'];
// creates a session variable to store the id of the current chat
$_SESSION['chatID'] = $chatID;
// define the styles for the current chat
$fontID = mysqli_fetch_row($conn->query("SELECT fontID FROM privateChats WHERE chatID = {$chatID}"))[0];
$font = mysqli_fetch_row($conn->query("SELECT val FROM chatStyles WHERE styleID = {$fontID}"))[0];
$colourID = mysqli_fetch_row($conn->query("SELECT colourID FROM privateChats WHERE chatID = {$chatID}"))[0];
$colour = mysqli_fetch_row($conn->query("SELECT val FROM chatStyles WHERE styleID = {$colourID}"))[0];
$memberFound = false;
echo '<script>var memberFound = false;</script>';
// checks if the current user is in the chat or not
foreach ($conn->query("SELECT memberID FROM chatmembers WHERE chatID = {$chatID}") as $row){
	if ($row['memberID'] == $_SESSION['userID']){
		$memberFound = true;
		echo '<script>memberFound = true;</script>';
	}
}
// opens a popup for the user to choose to join or reject the chat if they are not already a member
if ($memberFound == false){
	echo '<script>$(`#acceptInvitation`).toggle();</script>';
}
?>
<script>
	loadPrivateChat();
</script>
<div id="privateHeaderBar">
	<button class="privateMenuButton" onclick="window.location=('private.php');">Back</button>
	<p id="privateTitle" style="color:<?php echo $colour; ?>;font-family:<?php echo $font;?>">
		<?php echo mysqli_fetch_row($conn->query("SELECT chatTitle FROM privateChats 
			WHERE chatID = {$_SESSION['chatID']}"))[0]?>
	</p>
	<div id="privateMenuButtons">
	<button class="privateMenuButton" onclick="$(`#membersList`).toggle();">Members</button>
	<button class="privateMenuButton" onclick="$(`#inviteFriend`).toggle();$(`#usernameToInvite`).focus();">Invite</button>
	<?php
	// checks whether the user is the creator of the chat and displays extra buttons that normal users can't see
	if (mysqli_fetch_row($conn->query("SELECT isCreator FROM chatmembers WHERE chatID = {$_SESSION['chatID']} 
		AND memberID = {$_SESSION['userID']}"))[0] == 1){
		echo '
			<button class="privateMenuButton" onclick="$(`#changeName`).toggle();$(`#titleChange`).focus();">Title</button>
			<button class="privateMenuButton" onclick="$(`#changeStyle`).toggle();">Style</button>
			<button class="privateMenuButton" onclick="$(`#deletePrivate`).toggle();">Delete</button>';
	} else {
		// only user's that arren't the creator can leave the chat
		echo '<button class="privateMenuButton" onclick="$(`#leaveChat`).toggle();">Leave</button>';
	}
	?>
	</div>
</div>
<div class="messages privateMessages" id="messages">
	<?php
	if ($memberFound == true){ // only displays the messages in the chat if the muser is a member of the chat
		$_SESSION['chatID'] = $chatID; 
		require 'messages.php'; //loads the messages section of the page
		// sets the scroll of the messages section to a very large amount to ensure that the newest message is always at the bottom
		echo '<script>
				$("#messages").scrollTop(100000000);
				$("#messageSendArea").toggle();
			</script>';
	}
	?>
</div>
<!-- area to enter and send text messages and stickers -->
<div id="messageSendArea" style="display:none">
	<p class="messageWarning">You can bookmark this chat to quickly return to it</p>
	<div class="messageBoxEntry">
		<form class="messagesEntry" method="post" action="sendMessage.php">
			<input class="messageBodyEntry" id="messageEntryBoxCool" type="text" maxlength="120" name="messageBody" 
				autofocus>
			<input class="sendMessageButton" type="submit" value="Send">
		</form>
		<button id="stickersButton">Stickers</button></div>
	</div>
</div>
<?php
require 'footer.php';
?>