<?php
require 'header.php';
$conn->query("UPDATE users SET userIcon = '{$_POST['userIcon']}' WHERE username = '{$_SESSION['username']}';"); // updates the user's row in the database
$_SESSION['userIcon'] = $_POST['userIcon']; // updates the icon on the user's  browser
echo '<script>window.location = "' . $_SESSION['page'] . '.php";</script>';
require 'footer.php';
?>