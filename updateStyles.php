<?php
require 'header.php';
// updates the caht styles only if that style has been entered in the style popup
if (empty($_POST['font']) && isset($_POST['colour'])){
	$conn->query("UPDATE privateChats SET colourID = {$_POST['colour']} WHERE chatID = {$_SESSION['chatID']}");
} else if (isset($_POST['font']) && empty($_POST['colour'])){
	$conn->query("UPDATE privateChats SET fontID = {$_POST['font']} WHERE chatID = {$_SESSION['chatID']}");
} else if (isset($_POST['font']) && isset($_POST['colour'])){
	$conn->query("UPDATE privateChats SET fontID = {$_POST['font']}, colourID = {$_POST['colour']} WHERE chatID = {$_SESSION['chatID']}");
} else {
	$_SESSION['message'] = 'You did not select a change';
}
echo '<script>window.location=("' . $_SESSION['page'] . '");</script>';
require 'footer.php';
?>