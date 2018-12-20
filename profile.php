<?php
require 'header.php';
$_SESSION['page'] = 'profile';
$banner = '#' . mysqli_fetch_row($conn->query("SELECT banner FROM users WHERE userID = {$_SESSION['userID']}"))[0];
?>
<script> // sets the style for the tab of the current page
	loadProfile();
	$(document).ready(function(){$("#userProfileDetails").css("background","linear-gradient(to right,
		<?php echo $banner . ',' . $banner; ?>,#ffaa00)")});
</script>
<div id="userProfileDetails">
	<img id="profilePageUserIcon" src="images/icons/<?php echo $_SESSION['userIcon']; ?>.png">
	<p id="profilePageUsername"><?php echo $_SESSION['username']; ?></p>
	<?php
	if ($userType == 'parent'){
		// if the user is a parent they can delete their account
		echo '
			<form id="deleteUser" method="post" action="confirmDelete.php">
				<input id="deleteUserInput" type="text" value="' . $_SESSION['userID'] . '" name="userID">
				<input id="deleteUserButton" type="submit" value="Delete">
			</form>';
	}
	?><br><br>
</div>
<button class="pageButton" id="editProfileButton" onclick="var userID = <?php echo $_SESSION['userID']; ?>;
	$.post(`popups.php`,{popupType: `editProfile`, userID: userID},
	function(data,status){$(`#messagePopupText`).prepend(data).show();})">Edit profile</button>
<!-- the record of the user's bans is displayed -->
<div id="bans">
	<?php
	foreach ($conn->query("SELECT banID FROM bans WHERE userID = {$_SESSION['userID']}") as $row){
		$id = $row['banID'];
		$reasonID = mysqli_fetch_row($conn->query("SELECT reasonID FROM bans WHERE banID = {$id}"))[0];
		$reason = mysqli_fetch_row($conn->query("SELECT reason FROM reportreasons WHERE reasonID = {$reasonID}"))[0];
		$dateBanned = substr(mysqli_fetch_row($conn->query("SELECT banStart FROM bans WHERE banID = {$id}"))[0],0,10);
		echo '<p class="ban">' . $username . ' was banned for ' . $reason . ' on ' . $dateBanned . '</p>';
	}
	?>
</div>
<!-- the user's bio is displayed -->
<div id="bio">
	<?php
	echo '<p id="bioMessage">' . mysqli_fetch_row($conn->query("SELECT userBio FROM users 
		WHERE userID = '{$_SESSION['userID']}';"))[0] . '</p>';
	?>
</div>
<p class="subtitle">Friend requests:</p>
<?php
// loop through requests table for every time the requested user is the current user
foreach ($conn->query("SELECT friendID FROM requests WHERE acceptID = {$_SESSION['userID']}") as $row){
	$id = $row['friendID'];
	// get the requesting and requested user's ids
	$requestID = mysqli_fetch_row($conn->query("SELECT requestID FROM requests WHERE friendID = {$id}"))[0];
	$acceptID = mysqli_fetch_row($conn->query("SELECT acceptID FROM requests WHERE friendID = {$id}"))[0];
	// if the requested user is the current user for the current iterated requesting user
	if ($acceptID == $_SESSION['userID']){
		$friendUserIcon = mysqli_fetch_row($conn->query("SELECT userIcon FROM users WHERE userID = {$requestID}"))[0];
		$friendUsername = mysqli_fetch_row($conn->query("SELECT username FROM users WHERE userID = {$requestID}"))[0];
		// list element with requesting user's details and accept/decline buttons
		echo '
		<div class="childListItem">
			<img class="messageIcon" src="images/icons/' . $friendUserIcon . '.png">
			<p class="childAccountsListItems">' . $friendUsername . '</p>
			<form id="deleteUser" method="post" action="requestAction.php">
				<input id="deleteUserInput" type="text" value="' . $requestID . '" name="userID">
				<input id="deleteChildUserButton" type="submit" name="deleteRequest" value="Reject">
				<input id="acceptFriendRequestButton" type="submit" name="acceptRequest" value="Accept">
			</form>
		</div>
		';
	}
}
?>
<button class="pageButton" id="createNewChildButton" onclick="$(`#createRequestAlertBox`).toggle();">
	Send request</button><br>
<p class="subtitle">Friends list:</p>
<?php
$count = 0;
foreach ($conn->query("SELECT friendshipID FROM friends") as $row){
	$count++;
	$_SESSION['id'] = $row['friendshipID'];
	$requestID = mysqli_fetch_row($conn->query("SELECT requestID FROM friends 
		WHERE friendshipID = {$_SESSION['id']}"))[0];
	$acceptID = mysqli_fetch_row($conn->query("SELECT acceptID FROM friends 
		WHERE friendshipID = {$_SESSION['id']}"))[0];
	if ($requestID == $_SESSION['userID'] || $acceptID == $_SESSION['userID']){
		if ($requestID == $_SESSION['userID']){
			$friendID = $acceptID;
		} else if($acceptID == $_SESSION['userID']){
			$friendID = $requestID;
		}
		$friendUsername = mysqli_fetch_row($conn->query("SELECT username FROM users WHERE userID = {$friendID};"))[0];
		$friendUserIcon = mysqli_fetch_row($conn->query("SELECT userIcon FROM users WHERE userID = {$friendID};"))[0];
		// displays the list of the user's friends
		echo '
		<div class="childListItem">
			<img class="messageIcon" src="images/icons/' . $friendUserIcon . '.png">
			<p class="childAccountsListItems">' . $friendUsername . '</p>
			<div id="deleteUser"><button id="deleteChildUserButton" onclick="$(`#unfriend`).toggle();">
				Unfriend</button></div>
		</div>
		';
	}
}
echo '<br>';
if ($count == 0){
	echo 'None <br><br>';
}
// displays the correct profile widgets for the user type
if ($_SESSION['userType'] == 'parent'){
	require 'parentProfile.php';
} else if ($_SESSION['userType'] == 'child'){
	require 'childProfile.php';
}
require 'footer.php';
?>