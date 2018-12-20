<?php
require 'session.php';
require 'connection.php';
// report message
if ($_POST['popupType'] == 'report'){
	$chat = substr($_SESSION['page'],0,-4) . 'chat';
	$messageIDQuery = 'SELECT messageID FROM ' . $chat;
	if ($chat != 'globalchat' and $chat != 'parentchat'){
		$chat = 'privatemessages';
	}
	// gather details about the message that has been reported
	$messageID = $_POST['messageID'];
	$messageBody = mysqli_fetch_row($conn->query("SELECT messageBody FROM {$chat} WHERE messageID = {$messageID}"))[0];
	$offenderID = mysqli_fetch_row($conn->query("SELECT userID FROM {$chat} WHERE messageId = {$messageID}"))[0];
	$reporterID = $_SESSION['userID'];
	$userType = mysqli_fetch_row($conn->query("SELECT userType FROM users WHERE userID = {$reporterID}"))[0];
	$message = "";
	$alreadyReported = false;
	// verify if the message has already been reported by the reporter
	foreach ($conn->query("SELECT reportID FROM reportedmessages") as $row){
		$id = $row['reportID'];
		$offenderIDCell = mysqli_fetch_row($conn->query("SELECT offenderID FROM reportedmessages 
			WHERE reportID = {$id};"))[0];
		$reporterIDCell = mysqli_fetch_row($conn->query("SELECT reporterID FROM reportedmessages 
			WHERE reportID = {$id};"))[0];
		$messageBodyCell = mysqli_fetch_row($conn->query("SELECT messageBody FROM reportedmessages 
			WHERE reportID = {$id};"))[0];
		$id = $row['reportID'];
		if ($messageBodyCell == $messageBody && $offenderIDCell == $offenderID && $reporterIDCell == $reporterID){
			$alreadyReported = true;
		}
	}
	// checks for if hte reporter is trying to report themself or a message they have already reported
	if ($offenderID == $reporterID){
		$message = 'You can not report yourself!';
	} else if ($alreadyReported == true){
		$message = 'You have already reported this user for this message';
	} else {
		if ($userType == 'child'){
			// if the reporter is a child add the details to the reportedMessage table
			$conn->query("INSERT INTO reportedmessages (messageBody,offenderID,reporterID) 
				VALUES ('{$messageBody}','{$offenderID}','{$reporterID}')");
			$message = 'You have successfully reported this message to your parent account';
		} else if ($userType == 'parent'){
			// if the reporter is a parent display the dropdown for selecting a report reason
			$_SESSION['parentReport'] = true;
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
					<input type="text" style="display:none" name="messageBody" value="' . $messageBody . '">
					<input class="closeAlert" type="submit" value="Report">
				</form>
			';
		}
	}
	echo $message . '<br><button class="closeAlert" onclick="$(`#messagePopupText`).hide().html(``);">Close</button>';
} 
// parent profile accepts a report from child account
else if ($_POST['popupType'] == 'acceptReport'){
	echo 'Select the appropriate reason for reporting:<br>
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
					<input type="number" style="display:none" name="offenderID" value="' . $_POST['offenderID'] . '">
					<input type="number" style="display:none" name="reportID" value="' . $_POST['reportID'] . '">
					<input type="text" style="display:none" name="messageBody" value="' . $_POST['messageBody'] . '">
					<input class="closeAlert" type="submit" value="Report">
				</form>
	';
} 
// parent profile declines a report from child account
else if ($_POST['popupType'] == 'declineReport'){
	$message = "Report declined successfully";
	$conn->query("DELETE FROM reportedMessages WHERE reportID = {$_POST['reportID']}");
	echo $message . '<br><button class="closeAlert" onclick="$(`#messagePopupText`).hide().html(``);
		window.location=(`profile.php`)">Close</button>'; 
}
//admin bans a user
else if ($_POST['popupType'] == 'setBan'){
	$confirmID = $_POST['confirmID'];
	// get detais of offender
	$offenderID = mysqli_fetch_row($conn->query("SELECT offenderID FROM confirmedreports 
		WHERE confirmID = {$confirmID}"))[0];
	$reasonID = mysqli_fetch_row($conn->query("SELECT reasonID FROM confirmedreports 
		WHERE confirmID = {$confirmID}"))[0];
	$reason = mysqli_fetch_row($conn->query("SELECT reason FROM reportreasons WHERE reasonID = {$reasonID}"))[0];
	$additionalTime = $_POST['banLength'] . ' days';
	// set the timezone
	date_default_timezone_set("Europe/London");
	// get the current time
	$currentTime = new DateTime('now');
	// formats currentrime for database insertion
	$banStart = date_format($currentTime,'Y-m-d H:i:s');
	// add the days to the current time
	date_add($currentTime,date_interval_create_from_date_string($additionalTime));
	$returnTime = date_format($currentTime,'Y-m-d H:i:s');
	// record the ban and apply ban
	$conn->query("INSERT INTO bans (reasonID,userID,banStart) VALUES ({$reasonID},{$offenderID},'{$banStart}')");
	$conn->query("UPDATE users SET timetoreturn = '{$returnTime}' WHERE userID = '{$offenderID}'");
	$conn->query("DELETE FROM confirmedreports WHERE confirmID = {$confirmID}");
	echo 'User banned successfully, they will return at: ' . $returnTime . '<br><button class="closeAlert" 
		onclick="$(`#messagePopupText`).hide().html(``);window.location=(`reports.php`)">Close</button>';
}
// admin account declines a report from a parent account
else if ($_POST['popupType'] == 'declineBan'){
	$message = "Ban declined successfully";
	$conn->query("DELETE FROM confirmedreports WHERE confirmID = {$_POST['confirmID']}");
	echo $message . '<br><button class="closeAlert" onclick="$(`#messagePopupText`).hide().html(``);
		window.location=(`reports.php`)">Close</button>';
}
// popup for changing user profile
else if ($_POST['popupType'] == 'editProfile'){
	echo ' <form>
			<select class="alertSelect" id="bannerColourSelect">
				<option value="none"disabled selected>-- Banner Colour --</option>
				<option value="1">Black</option>
				<option value="3">Red</option>
				<option value="4">Green</option>
				<option value="5">Cyan</option>
				<option value="6">Blue</option>
				<option value="7">Purple</option>
				<option value="8">Pink</option>
			</select>
			<input class="alertTextBox" id="bioSelect" type="text" maxlength="120" placeholder="bio">
		</form>
		<button class="closeAlert" onclick="var bannerColour = $(`#bannerColourSelect`).val();
			var bio = $(`#bioSelect`).val();$.post(`popups.php`,{popupType: `confirmEditProfile`, 
			bannerColour: bannerColour, bio: bio},function(data,status){$(`#messagePopupText`).html(``);
			$(`#messagePopupText`).prepend(data).show();});">Change</button>
		<button class="closeAlert" onclick="$(`#messagePopupText`).hide().html(``);">Close</button>
	';
}
// confirm edit profile
else if ($_POST['popupType'] == 'confirmEditProfile'){
	// if the user is changing their banner
	if ($_POST['bannerColour'] != ''){
		$bannerColourID = $_POST['bannerColour'];
		// takes the colour from the styles table
		$bannerColour = substr(mysqli_fetch_row($conn->query("SELECT val FROM chatstyles 
			WHERE styleID = {$bannerColourID}"))[0],1,6);
		// updates the user's banenr value in the users table
		$conn->query("UPDATE users SET banner = '{$bannerColour}' WHERE userID = {$_SESSION['userID']}");
	}
	// if the user is updating their bio, separate to the banner chage so either or both could be changed at one time
	if ($_POST['bio'] != ''){
		$bio = $_POST['bio'];
		$conn->query("UPDATE users SET userBio = '{$bio}' WHERE userID = {$_SESSION['userID']}")[0];
	}
	echo 'Profile successfully updated<br>
		<button class="closeAlert" onclick="$(`#messagePopupText`).hide().html(``);window.location=(`profile.php`)">
			Close
		</button>
	';
}
// user channes their icon image
else if ($_POST['popupType'] == 'changeIcon'){
	//Change user icon popup window
	echo '
	<form id="iconChoice" method="post" action="updateIcon.php">
		<div class="iconsList">';
			for ($i=1; $i<=8; $i++){
				echo '<img class="iconTile" id="' . $i . '" src="images/icons/' . $i . '.png" 
					onclick="$(`#iconBoxInput`).val(`' . $i . '`);selectIcon(' . $i . ');">';
			} echo '
		</div>
		<input class="iconInput" id="iconBoxInput" style="display:none" type="text" name="userIcon" maxlength="1" 
			value="0">
		<input class="closeAlert" type="submit" value="Update">
	</form>';
}
?>