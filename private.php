<?php
require 'header.php';
$_SESSION['page'] = 'private';
// verifying the created private chat
if (isset($_POST['submit'])){
	// if all of the fields were filled out
	if (isset($_POST['chatTitle']) && isset($_POST['font']) && isset($_POST['colour'])){
		// create the new chat
		$conn->query("INSERT INTO privateChats (chatTitle,fontID,colourID) 
			VALUES ('{$_POST['chatTitle']}',{$_POST['font']},{$_POST['colour']})");
		$conn->query("INSERT INTO chatMembers (chatID,memberID,isCreator) 
			VALUES ({$conn->insert_id},{$_SESSION['userID']},1)");
	} else {
		$_SESSION['message'] = 'Not all fields were completed';
	}
	$_POST = array();
	// refreshes the page to update the chats list
	echo '<script>window.location=("private.php");</script>';
}
?>
<script>
	loadPrivate();
</script>
<!-- chat search bar -->
<form action="private.php" method="post">
	<input class="messageBodyEntry" id="chatSearch" type="text" maxlength="20" name="chatTitle" required autofocus>
	<input class="sendMessageButton" type="submit" name="submitSearch" value="Search">
</form>
<button id="searchResetButton" onclick="window.location=('private.php');">X</button>
<button class="pageButton" id="createChatButton" onclick="$('#createChat').toggle();$('#chatTitleInput').focus()">
	Create new chat</button>
<br><br>
<p class="subtitle" id="inviteTitle"></p>
<?php
function getChats($id){
	// allow the function to access the database connection
	global $conn;
	// gather the required data to be displayed
	$chatTitle = mysqli_fetch_row($conn->query("SELECT chatTitle FROM privateChats WHERE chatID = {$id}"))[0];
	$fontID = mysqli_fetch_row($conn->query("SELECT fontID FROM privateChats WHERE chatID = {$id}"))[0];
	$chatFont = mysqli_fetch_row($conn->query("SELECT val FROM chatStyles WHERE styleID = {$fontID}"))[0];
	$colourID = mysqli_fetch_row($conn->query("SELECT colourID FROM privateChats WHERE chatID = {$id}"))[0];
	$chatColour = mysqli_fetch_row($conn->query("SELECT val FROM chatStyles WHERE styleID = {$colourID}"))[0];
	// display the chat details
	echo '<form action="chat.php" method="get" name="openChat' . $id . '">';
		echo '<input style="display:none" type="number" name="chatID" value="' . $id . '">';
		echo '<div class="chatItem" onclick="document.forms[`openChat' . $id . '`].submit()">
				<p class="chatTitle" style="font-family:' . $chatFont . ';color:' . $chatColour . '">' 
					. $chatTitle . '</p>
			</div>
		</form>
		<hr class="chatDivider">';
}
// if the user has searched for a chat
if (isset($_POST['submitSearch'])){
	echo '<script>$("#searchResetButton").toggle();$("#createChatButton").css(`margin-left`,`100px`)</script>
	<div id="chatInvites">';
		// display the chats that the user has been invited to
		foreach($conn->query("SELECT inviteID FROM chatinvites WHERE userID = {$_SESSION['userID']}") as $row){
			echo '<script>$("#inviteTitle").text("Invitations");</script>';
			$inviteID = $row['inviteID'];
			$chatID = mysqli_fetch_row($conn->query("SELECT chatID FROM chatinvites WHERE inviteID = {$inviteID}"))[0];
			getChats($chatID);
		} 
	echo'</div>';
	// display the chats the user is a member of
	echo '<p class="subtitle">Chats:</p>';
	foreach ($conn->query("SELECT chatID FROM privatechats WHERE chatTitle LIKE '%{$_POST['chatTitle']}%' 
		ORDER BY latestMessage DESC") as $row){
		$id = $row['chatID'];
		if (in_array([$_SESSION['userID']],mysqli_fetch_all($conn->query("SELECT memberID FROM chatmembers 
			WHERE chatID = {$id}")))){
			getChats($id);
		}
	}
// if the user hasn't searched
} else {
	echo '<div id="chatInvites">';
		// display the chats that the user has been invited to
		foreach($conn->query("SELECT inviteID FROM chatinvites WHERE userID = {$_SESSION['userID']}") as $row){
			echo '<script>$("#inviteTitle").text("Invitations:");</script>';
			$inviteID = $row['inviteID'];
			$chatID = mysqli_fetch_row($conn->query("SELECT chatID FROM chatinvites WHERE inviteID = {$inviteID}"))[0];
			getChats($chatID);
		} 
	echo'</div>';
	// display the chats the user is a member of
	echo '<p class="subtitle">Chats:</p>';
	foreach ($conn->query("SELECT chatID FROM privateChats ORDER BY latestMessage DESC") as $row){
		$id = $row['chatID'];
		if (in_array([$_SESSION['userID']],mysqli_fetch_all($conn->query("SELECT memberID FROM chatmembers 
			WHERE chatID = {$id}")))){
			getChats($id);
		}
	}
}
require 'footer.php';
?>