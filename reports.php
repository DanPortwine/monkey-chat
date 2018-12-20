<?php
require 'header.php';
?>
<script>
	loadReports();
</script>
<p class="subtitle">Reported messages:</p>
<p>Enter the number of days of the ban and click <i>Ban</i> to ban the user or decline the ban:</p>
<?php
// loop through every row in the confirmereports table of reported messages that parents have approved
foreach ($conn->query("SELECT confirmID FROM confirmedreports;") as $row){
	$id = $row['confirmID'];
	// get al the required details
	$message = mysqli_fetch_row($conn->query("SELECT messageBody FROM confirmedreports WHERE confirmID = {$id}"))[0];
	$reasonID = mysqli_fetch_row($conn->query("SELECT reasonID FROM confirmedreports WHERE confirmID = {$id}"))[0];
	$reason = mysqli_fetch_row($conn->query("SELECT reason FROM reportreasons WHERE reasonID = {$reasonID}"))[0];
	$offenderID = mysqli_fetch_row($conn->query("SELECT offenderID FROM confirmedreports WHERE confirmID = {$id}"))[0];
	$offender = mysqli_fetch_row($conn->query("SELECT username FROM users WHERE userID = {$offenderID}"))[0];
	$reporterID = mysqli_fetch_row($conn->query("SELECT reporterID FROM confirmedreports WHERE confirmID = {$id}"))[0];
	$reporter = mysqli_fetch_row($conn->query("SELECT username FROM users WHERE userID = {$reporterID}"))[0];
	// display the details with input to enter days for ban and buttons to ban or decline
	echo '
		<div class="childListItem">
			<p class="confirmReport confirmBan"><span class="reporterName">' . $offender . ' (' . $offenderID . ')[' . $reasonID . ']:</span> <span>' . $message . '</span></p>
			<div class="reportActionButtons">
				<input class="alertTextBox" type="number" id="banLength">
				<button class="reportActionButton confirmButton" onclick="var id = ' . $id . ';var length = $(`#banLength`).val();$.post(`popups.php`, {popupType: `setBan`, confirmID: `' 
					. $id . '`, banLength: length},function(data,status){$(`#messagePopupText`).prepend(data).show();})">Ban</button>
				<button class="reportActionButton cancelButton" onclick="var id = ' . $id . ';$.post(`popups.php`, {popupType: `declineBan`, confirmID: id},
					function(data,status){$(`#messagePopupText`).prepend(data).show();})">Decline</button>
			</div>
		</div>
	';
}
?>
<p class="subtitle">Banned users:</p>
<?php
// shows a list of all the currently banned users
foreach ($conn->query("SELECT userID FROM users WHERE timetoreturn > CURRENT_TIMESTAMP") as $row){
	echo mysqli_fetch_row($conn->query("SELECT username FROM users WHERE userID = {$row['userID']}"))[0] . ' (' . $row['userID'] . ') - ' . 
	mysqli_fetch_row($conn->query("SELECT timeToReturn FROM users WHERE userID = {$row['userID']}"))[0] . '<br>';
}
require 'footer.php';
?>