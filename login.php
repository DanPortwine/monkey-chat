<?php
require 'header.php';
if (isset($_POST['login'])){ //if the user clicked login
	$query = $conn->query("SELECT userid FROM users WHERE username = '{$_POST['username']}';");
	$userID = mysqli_fetch_row($query)[0];
	if ($query->num_rows > 0){
		$timeToReturn = mysqli_fetch_row($conn->query("SELECT timetoreturn FROM users WHERE userID = '{$userID}'"))[0];
		if ($timeToReturn < date_format(new DateTime('now'),"Y-m-d H:i:s") || empty($timeToReturn)){
			$_SESSION['username'] = $_POST['username'];
			$salt = mysqli_fetch_row($conn->query("SELECT userSalt FROM users WHERE userID = '{$userID}';"))[0];
			$password = mysqli_fetch_row($conn->query("SELECT userPassword FROM users WHERE userID = '{$userID}';"))[0];
			if (hash('sha512',$salt . $_POST['password']) === $password){ // checks the inputted password against the saved password in the database
				$_SESSION['userIcon'] = mysqli_fetch_row($conn->query("SELECT userIcon FROM users WHERE userID = '{$userID}';"))[0];
				$_SESSION['userID'] = mysqli_fetch_row($conn->query("SELECT userID FROM users WHERE userID = '{$userID}';"))[0];
				$_SESSION['userType'] = mysqli_fetch_row($conn->query("SELECT userType FROM users WHERE userID = '{$userID}';"))[0];
			} else {
				$_SESSION['message'] = 'Incorrect username or password'; // sets the text of the alert pop up window
			}
		} else {
			$_SESSION['message'] = 'You are currently banned. Try again later';
		}
	} else {
		$_SESSION['message'] = 'Incorrect username or password';
	}
	echo '<script>window.location = "index.php";</script>';
} else if (isset($_POST['signUp'])){ //if the user clicked sign up
	$username = $_POST['username'];
	//find number of rows in table
	$rows = $conn->query("SELECT username FROM users")->num_rows;
	//checks if username has been taken
	for ($i=0; $i<=$rows; $i++){
		$result = mysqli_fetch_row($conn->query("SELECT username FROM users WHERE userID = {$i}"))[0];
		if (isset($result) && $result == $username){
			$_SESSION['message'] = "Username already taken";
			echo '<script>window.location = "index.php";</script>';
			break;
		}
	}
	if (empty($result) || $result != $username){
		$_SESSION['username'] = $_POST['username'];
		$_SESSION['password'] = $_POST['password'];
		$_SESSION['signUp'] = true;
	}
} else { //if the user went directly to the login page
	$_SESSION['message'] = 'You were not logging in or signing up';
}
echo '<script>window.location = "index.php";</script>';
require 'footer.php';
?>