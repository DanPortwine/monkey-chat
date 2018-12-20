<p class="subtitle">Parent account:</p>
<?php
// displays the user information about the parent account of the child account
$parentID = mysqli_fetch_row($conn->query("SELECT parentID FROM childparent WHERE childID = {$_SESSION['userID']};"))[0];
$parentUsername = mysqli_fetch_row($conn->query("SELECT username FROM users WHERE userID = {$parentID};"))[0];
$parentUserIcon = mysqli_fetch_row($conn->query("SELECT userIcon FROM users WHERE userID = {$parentID};"))[0];
echo '<div class="childListItem">
		<img class="messageIcon" src="images/icons/' . $parentUserIcon . '.png">
		<p class="childAccountsListItems">' . $parentUsername . '</p>
	</div>';
?>