<p class="subtitle">Child accounts:</p>
<button id="createNewChildButton" onclick='$("#newChildAlertBox").toggle();'>Create new</button><br>
<?php
foreach ($conn->query("SELECT relationshipID FROM childparent") as $row){
	$id = $row['relationshipID'];
	$parentID = mysqli_fetch_row($conn->query("SELECT parentID FROM childparent WHERE relationshipID = {$id}"))[0];
	// dispalys the user details of the user's child accounts
	if ($parentID == $_SESSION['userID']){
		$childID = mysqli_fetch_row($conn->query("SELECT childID FROM childparent WHERE relationshipID = {$id};"))[0];
		$childUsername = mysqli_fetch_row($conn->query("SELECT username FROM users WHERE userID = {$childID};"))[0];
		$childUserIcon = mysqli_fetch_row($conn->query("SELECT userIcon FROM users WHERE userID = {$childID};"))[0];
		echo '<div class="childListItem">
				<img class="messageIcon" src="images/icons/' . $childUserIcon . '.png">
				<p class="childAccountsListItems">' . $childUsername . '</p>
				<form id="deleteUser" method="post" action="confirmDelete.php">
					<input id="deleteUserInput" type="text" value="' . $childID . '" name="userID">
					<input id="deleteChildUserButton" type="submit" value="Delete">
				</form>
			</div>';
	}
}
?>
<br>
<p class="subtitle">Reports:</p>
<p>Review reported messages from your child accounts and either report to admin or decline their report.</p>
<?php
// loops through all of the rows in the childparent table
foreach ($conn->query("SELECT relationshipID FROM childparent") as $row){
	$id = $row['relationshipID'];
	$parentID = mysqli_fetch_row($conn->query("SELECT parentID FROM childparent WHERE relationshipID = {$id}"))[0];
	// if the parentID of the current row is the currently logged in user
	if ($parentID == $_SESSION['userID']){
		$childID = mysqli_fetch_row($conn->query("SELECT childID FROM childparent WHERE relationshipID = {$id}"))[0];
		$childName = mysqli_fetch_row($conn->query("SELECT username FROM users WHERE userID = {$childID}"))[0];
		// if the message was reported by their child account
		if (mysqli_fetch_row($conn->query("SELECT reportID FROM reportedmessages WHERE reporterID = {$childID}"))[0]){
			echo '- ' . $childName . ':';
		}
		// loops through the reported messages that their child account has reported
		foreach ($conn->query("SELECT reportID FROM reportedmessages WHERE reporterID = {$childID};") as $row1){
			$rowID = $row1['reportID'];
			$message = mysqli_fetch_row($conn->query("SELECT messagebody FROM reportedmessages 
				WHERE reportID = {$rowID}"))[0];
			$reporterID = $conn->query("SELECT reporterID FROM reportedmessages WHERE reportID = {$rowID};");
			$offenderID = mysqli_fetch_row($conn->query("SELECT offenderID FROM reportedmessages 
				WHERE reportID = {$rowID};"))[0];
			$offenderName = mysqli_fetch_row($conn->query("SELECT username FROM users 
				WHERE userID = {$offenderID};"))[0];
			// displays the details of the report and buttons to report or decline
			echo '<div class="childListItem">
					<p class="confirmReport">
						<span class="reporterName">' . $offenderName . ' (' . $offenderID . '):</span> 
						<span>' . $message . '</span>
					</p>
					<div class="reportActionButtons">
						<button class="reportActionButton confirmButton" onclick="var id = ' . $rowID . ';
							$.post(`popups.php`,{popupType: `acceptReport`, reportID: id, offenderID: `' 
							. $offenderID . '`,messageBody: `' . $message . '`},
							function(data,status){$(`#messagePopupText`).prepend(data).show();})">Report</button>
						<button class="reportActionButton cancelButton" onclick="var id = ' . $rowID . ';
							$.post(`popups.php`,{popupType: `declineReport`, reportID: id},
							function(data,status){$(`#messagePopupText`).prepend(data).show();})">Decline</button>
					</div>
				</div>';
		}
	}
}
?>