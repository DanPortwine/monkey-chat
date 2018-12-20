<?php
require 'header.php';
$username = $_POST['username'];
// find number of rows in table
$rows = $conn->query("SELECT username FROM users")->num_rows;
// checks if username has been taken
$free = true;
foreach ($conn->query("SELECT userID FROM users") as $row){
	if ($username == mysqli_fetch_row($conn->query("SELECT username FROM users WHERE userID = {$row['userID']}"))[0]){
		$_SESSION['message'] = "Error: username already taken";
		$free = false;
		break;
	}
}
// username is not taken
if ($free == true){
	// find how many child accounts parent already has
	$rows = $conn->query("SELECT relationshipID FROM childparent WHERE parentID = {$_SESSION['userID']}")->num_rows;
	// only allow max 10 child account to be made
	if ($rows < 10){
		$username = $_POST['username'];
		$password = $_POST['password'];
		$userIcon = $_POST['userIcon'];
		$email = mysqli_fetch_row($conn->query("SELECT email FROM users WHERE username = '{$_SESSION['username']}';"))[0];
		// generate salt
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$salt = '';
		for ($x=0; $x<128; $x++) {
			$salt .= $characters[rand(0,35)];
		}
		// generate hashed password
		$password = hash('sha512',$salt . $password);
		// create child account
		$conn->query("INSERT INTO users (username,userType,userIcon,userPassword,userSalt,email) VALUES ('{$username}','child','{$userIcon}','{$password}','{$salt}','{$email}')");
		$conn->query("INSERT INTO childParent (childID,parentID) VALUES ('{$conn->insert_id}','{$_SESSION['userID']}')");
	} else {
		$_SESSION['message'] = 'Error: you are only allowed up to 10 Child accounts!';
	}
}
// redirects the user to their profile page
echo '<script>window.location = "profile.php";</script>';
require 'footer.php';
?>