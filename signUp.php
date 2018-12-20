<?php
require 'header.php';
$username = $_POST['username'];
$password = $_POST['password'];
$email = $_POST['email'];
$userIcon = $_POST['userIcon'];
//generate salt
$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
$salt = '';
for ($i=0; $i<128; $i++) {
	$salt .= $characters[rand(0,35)];
}
//generate hashed password
$password = hash('sha512',$salt . $password);
// if the user's details were added correctly save their details to the session
if ($conn->query("INSERT INTO users (username,userType,userIcon,userPassword,userSalt,email,banner) 
		VALUES ('{$username}','parent','{$userIcon}','{$password}','{$salt}','{$email}','ff5992')") === true) {
	$_SESSION['userID'] = $conn->insert_id;
	$_SESSION['username'] = $username;
	$_SESSION['userIcon'] = $userIcon;
	$_SESSION['userType'] = 'parent';
} else {
	$_SESSION['message'] = "Error: " . $query . "<br>" . $conn->error;
}
echo '<script>window.location = "index.php";</script>';
require 'footer.php';
?>