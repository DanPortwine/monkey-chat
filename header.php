<?php
require 'session.php';
require 'connection.php';
if (isset($_SESSION['username'])){ $username = $_SESSION['username']; }
if (isset($_SESSION['userID'])){ $userID = $_SESSION['userID']; }
if (isset($_SESSION['userIcon'])){ $userIcon = $_SESSION['userIcon']; }
if (isset($_SESSION['userType'])){ $userType = $_SESSION['userType']; }
// logged in check
if (isset($username) && isset($userID) && isset($userIcon) && isset($userType)){
	$loggedIn = true;
	$timeToReturn = mysqli_fetch_row($conn->query("SELECT timetoreturn FROM users 
		WHERE userID = '{$_SESSION['userID']}'"))[0];
	if (isset($timeToReturn) && $timeToReturn > date_format(new DateTime('now'),"Y-m-d H:i:s")){
		echo '<script>window.location="logout.php";</script>';
	}
} else {
	$loggedIn = false;
}
?>
<html>
	<head>
		<title>MonkeyChat</title>
		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="icon" href="images/stickers/monk2.png">
		<script src="jquery-3.2.1.min.js"></script>
		<script src="script.js"></script>
	</head>
	<body>
		<div class="alertBox" id="messagePopupText"></div>
		<!-- Change user icon popup window -->
		<div class="alertBox" id="updateIcon">
			<form id="iconChoice" method="post" action="updateIcon.php">
				<div class="iconsList">
					<?php
					for ($i=1; $i<=8; $i++){
						echo '<img class="iconTile" id="' . $i . '" src="images/icons/' . $i . '.png" 
							onclick="$(`#iconBoxInput`).val(`' . $i . '`);selectIcon(' . $i . ');">';
					}
					?>
				</div>
				<input class="iconInput" id="iconBoxInput" style="display:none" type="text" name="userIcon" maxlength="1" 
					value="0">
				<input class="closeAlert" type="submit" value="Update">
			</form>
			<button class="closeAlert" id="changeIconCloseButton">Cancel</button>
		</div>
		<!-- popup for choosing a sticker -->
		<div class="alertBox" id="stickerChoice">
			<div class="iconsList">
			<?php
			$count = 0; // finds the number of files in the stickers directory
			foreach (scandir('images/stickers/') as $sticker){
				if ($count > 1){
					echo '<img class="iconTile" src="images/stickers/' . $sticker . '" 
						onclick="var message = $(`#messageEntryBoxCool`).val();
						$(`#stickerBoxInput`).val(`:' . substr($sticker,0,-4) . ':`);selectIcon(' . $i . ');
						$(`#messageEntryBoxCool`).val(message+`:' . substr($sticker,0,-4) . ':`).focus();">';
				}
				$count += 1;
			}
			?>
			</div>
			<button class="closeAlert" id="chooseStickerCloseButton">Close</button>
		</div>
		<!-- popup for sending friend request -->
		<div class="alertBox" id="createRequestAlertBox">
			<form id="createNewRequest" method="post" action="createRequest.php">
				<!-- enter the requested user's username -->
				<input class="alertTextBox" type="text" placeholder="username" name="username" autofocus required>
				<input class="closeAlert" type="submit" value="Send">
			</form>
			<button class="closeAlert" id="friendRequestCloseButton">Cancel</button>
		</div>
		<!-- popup for deleting a friend -->
		<div class="alertBox" id="unfriend">
			<form method="post" action="deleteFriend.php">
				<p>Are you sure you want to unfriend this user?</p>
				<input id="deleteUserInput" type="text" value="<?php echo $_SESSION['id']; ?>" name="userID">
				<input class="closeAlert" type="submit" value="Yes">
			</form>
			<button class="closeAlert" id="stopDeleteFriend">No</button>
		</div>
		<!-- popup for confirming to delete users -->
		<div class="alertBox" id="delete">
			<form method="post" action="deleteUser.php">
				<p>Are you sure you want to delete this user?</p>
				<input id="deleteUserInput" type="text" value="<?php echo $_POST['userID']; ?>" name="userID">
				<input class="closeAlert" type="submit" value="Yes">
			</form>
			<button class="closeAlert" id="stopDeleteUser">No</button>
		</div>
		<!-- popup for creating a new child account -->
		<div class="alertBox" id="newChildAlertBox">
			<form id="createNewChildAccount" method="post" action="createChild.php">
				<input class="alertTextBox" type="text" placeholder="username" name="username" autofocus required>
				<input class="alertTextBox" type="password" placeholder="password" name="password" required>
				<div class="iconsList">
					<?php for ($i=1; $i<=8; $i++){ // there are 8 icons to choose from
						// when clicked the icon choice input box has its value set to the number of the iteration through the loop
						echo '<img class="iconTile" id="' . $i . '2" src="images/icons/' . $i . '.png" 
							onclick="$(`#iconChoiceInput`).val(`' . $i . '`);selectIcon(' . $i . '2)">';
					}?>
				</div>
				<input class="iconInput" id="iconChoiceInput" style="display:none" type="text" name="userIcon" 
					maxlength="1" value="0">
				<input class="closeAlert" type="submit">
			</form>
			<button class="closeAlert" id="createChildCloseButton">Cancel</button>
		</div>
		<!-- popup for changing the title of the chat -->
		<div class="alertBox" id="changeName">
			<form action="changeName.php" method="post">
				<input class="alertTextBox" id="titleChange" type="text" maxlength="10" placeholder="new title" 
					name="chatTitle" required>
				<br>
				<input class="closeAlert" type="submit" value="Update">
			</form>
			<button class="closeAlert" id="changeTitleCloseButton">Cancel</button>
		</div>
		<!-- pop up for inviting a friend to the chat -->
		<div class="alertBox" id="inviteFriend">
			<?php 
				if ($_SESSION['userType'] == 'child'){
						$parentID = mysqli_fetch_row($conn->query("SELECT parentID FROM childparent 
							WHERE childID = {$_SESSION['userID']}"))[0];
						$parentName = mysqli_fetch_row($conn->query("SELECT username FROM users 
							WHERE userID = {$parentID}"))[0];
						echo '<button class="closeAlert" onclick="$.post(`inviteUser.php`,{userToInvite: `' 
							. $parentName . '`},function(data,status){$(`#messagePopupText`).prepend(data).show();});">
							Parent</button><br><br>';
				}
			?>
			<input class="alertTextBox" id="usernameToInvite" type="text" placeholder="username">
			<button class="closeAlert" id="inviteUserButton">Invite</button>
			<button class="closeAlert" id="chatInviteCloseButton">Close</button>
		</div>
		<!-- popup to confirm leave a private chat -->
		<div class="alertBox" id="leaveChat">
			<p>Are you sure you want to leave this chat?</p>
			<button class="closeAlert" onclick="window.location=`leaveChat.php`;">Confirm</button>
			<button class="closeAlert" onclick="$(`#leaveChat`).hide();">Close</button>
		</div>
		<!-- popup for accepting invitation to chat -->
		<div class="alertBox" id="acceptInvitation">
			<?php
				if (isset($_SESSION['chatID'])){
					foreach ($conn->query("SELECT memberID FROM chatmembers 
						WHERE chatID = {$_SESSION['chatID']}") as $row){
						$userID = $row['memberID'];
						$username = mysqli_fetch_row($conn->query("SELECT username FROM users 
							WHERE userID = {$userID}"))[0];
						echo $username . '<br>';
					}
				}
			?>
			<button class="closeAlert" id="acceptInvitationButton">Accept</button><br>
			<button class="closeAlert" id="declineInvitationButton">Decline</button>
		</div>
		<!-- popup for deleting a chat -->
		<div class="alertBox" id="deletePrivate">
			<p>Are you sure you want to delete this chat?</p>
			<button class="closeAlert" id="deleteChatButton">Delete</button><br>
			<button class="closeAlert" id="cancelDeleteChatButton">Cancel</button>
		</div>
		<!-- popup for viewing all members in chat -->
		<div class="alertBox" id="membersList">
			<p>Members:</p>
			<?php
			foreach ($conn->query("SELECT memberID FROM chatmembers WHERE chatID = {$_SESSION['chatID']}") as $row){
				if (mysqli_fetch_row($conn->query("SELECT userType FROM users 
					WHERE userID = {$row['memberID']}"))[0] != 'bot'){
					echo mysqli_fetch_row($conn->query("SELECT username FROM users 
						WHERE userID = {$row['memberID']}"))[0] . ' - [' . 
						mysqli_fetch_row($conn->query("SELECT userType FROM users 
							WHERE userID = {$row['memberID']}"))[0] . '] <br>';
				}
			}
			?>
			<button class="closeAlert" id="membersCloseButton">Close</button>
		</div>
		<!-- popup for changing chat styles -->
		<div class="alertBox" id="changeStyle">
			<form action="updateStyles.php" method="post">
				<select class="alertSelect" name="font">
					<option disabled selected>-- Font --</option>
					<option value="17">Algerian</option>
					<option value="9">Arial</option>
					<option value="16">Chiller</option>
					<option value="11">Courier</option>
					<option value="15">Jokerman</option>
					<option value="10">Times New Roman</option>
					<option value="13">Trebuchet MS</option>
					<option value="12">Verdana</option>
				</select>
				<select class="alertSelect" name="colour">
					<option disabled selected>-- Text colour --</option>
					<option value="1">Black</option>
					<option value="2">White</option>
					<option value="3">Red</option>
					<option value="4">Green</option>
					<option value="5">Cyan</option>
					<option value="6">Blue</option>
					<option value="7">Purple</option>
					<option value="8">Pink</option>
				</select>
				<br>
				<input class="closeAlert" type="submit" value="Update">
			</form>
			<button class="closeAlert" id="changeStyleCloseButton">Cancel</button>
		</div>
		<!-- popup for creating a new private chat -->
		<div class="alertBox" id="createChat">
			<form action="private.php" method="post">
				<!-- title input -->
				<input class="alertTextBox" id="chatTitleInput" type="text" maxlength="10" placeholder="title" 
					name="chatTitle" required>
				<!-- font selection -->
				<select class="alertSelect" name="font">
					<option disabled selected>-- Font --</option>
					<option value="17">Algerian</option>
					<option value="9">Arial</option>
					<option value="16">Chiller</option>
					<option value="11">Courier</option>
					<option value="15">Jokerman</option>
					<option value="10">Times New Roman</option>
					<option value="13">Trebuchet MS</option>
					<option value="12">Verdana</option>
				</select>
				<!-- colour selection -->
				<select class="alertSelect" name="colour">
					<option disabled selected>-- Text colour --</option>
					<option value="1">Black</option>
					<option value="2">White</option>
					<option value="3">Red</option>
					<option value="4">Green</option>
					<option value="5">Cyan</option>
					<option value="6">Blue</option>
					<option value="7">Purple</option>
					<option value="8">Pink</option>
				</select>
				<br>
				<input class="closeAlert" type="submit" value="Create" name="submit">
			</form>
			<button class="closeAlert" id="createChatCloseButton">Cancel</button>
		</div>
		<?php
		// sign up pop up window
		if (isset($_SESSION['signUp'])){ echo //only allow sign up pop up window to appear if the user clicked on sign up
			'<div class="alertBox" id="finishSignUpBox" style="display:inline-block">
				<form id="iconForm" method="post" action="signUp.php"> 
					<input class="alertTextBox" type="text" maxlength="20" placeholder="username" name="username" 
						required value="' . $_SESSION['username'] . '">
					<input class="alertTextBox" type="password" placeholder="password" name="password" required 
						value="' . $_SESSION['password'] . '">
					<input class="alertTextBox" type="email" maxlength="50" placeholder="email" name="email" required>
					<br>
					<input class="iconInput" id="iconChoiceInput1" style="display:none" type="text" name="userIcon" 
						maxlength="1" value="0">
					Icon:
					<div class="iconsList">';
						// for the number of icons that are available to choose from
						for ($i=1; $i<=8; $i++){
							// echo the icon image that when clicked will set the iconChoiceInput1 field of the form to the current 
							// iconID run selectIcon() with the current icon as the parameter
							echo '<img class="iconTile" id="' . $i . '1" src="images/icons/' . $i . '.png" 
								onclick="$(`#iconChoiceInput1`).val(`' . $i . '`);selectIcon(' . $i . '1);">';
						}
					echo '
					</div>
					<input class="closeAlert" type="submit" value="Create">
				</form>
				<button class="closeAlert" id="signUpCloseButton">Cancel</button>
			</div>';
			$_POST = array();
			// clear session variables that shouldn't be stored
			unset($_SESSION['signUp']);
			unset($_SESSION['username']);
			unset($_SESSION['password']);
		}
		// pop up message from another page
		if (!empty($_SESSION['message'])){
			echo '<div class="alertBox" id="messageAlertBox" style="display:inline-block">' . $_SESSION['message'] 
				. '<br>
				<button class="closeAlert" id="messageCloseButton">Close</button></div>';
		}
		unset($_SESSION['message']);
		?>
		<!-- top menu bar -->
		<div class="menuBar">
			<div id="account">
				<?php
				// when the user is logged in the login/sign up form is replaced with their account details
				if ($loggedIn == true){
					echo '<div id="accountDetails">
							<a href="logout.php">
							<button class="menuButton" id="logoutButton">Logout</button>
							</a>
							<p class="username">' . $_SESSION['username'] . ' <sup><span id="userType">[' . $userType . ']
								</span></sup></p>
							<img id="userIcon" src="images/icons/' . $_SESSION['userIcon'] . '.png";">
							<p id="changeUserIconText">Change</p>
						</div>';
				} else { echo '
					<form method="post" action="login.php">
						<input class="loginInput" type="text" maxlength="20" name="username" placeholder="username" 
							required autofocus>
						<input class="loginInput" type="password" maxlength="20" name="password" placeholder="password" 
							required>
						<input class="menuButton" type="submit" value="Login" name="login">
						<input class="menuButton" type="submit" value="Sign up" name="signUp">
					</form>'; }
				?>
			</div>
			<h1 class="title">MonkeyChat</h1>
		</div>
		<!-- main body of page -->
		<div class="main">
			<div class="tabMenu">
				<a href="index.php"><button class="tab" id="newsTab">News</button></a>
				<!-- <a href="Questionnaire/questions.php"><button class="tab" id="questionsTab">Qs</button></a> -->
				<?php 
				// only logged in users can access any pages other than the news/home page
				if ($loggedIn == true) {
					echo '<a href="global.php"><button class="tab" id="globalTab">Global</button></a>';
					if ($userType == 'parent' || $userType == 'admin'){
						echo '<a href="parent.php"><button class="tab" id="parentTab">Parent</button></a>';
					}
					echo '
					<a href="private.php"><button class="tab" id="privateTab">Private</button></a>
					<a href="profile.php"><button class="tab" id="profileTab">Profile</button></a>';
					if ($userType == 'admin'){
						echo '<a href="reports.php"><button class="tab" id="reportsTab">Reports</button></a>';
					}
				}
				?>
			</div>
			<div class="mainInner">