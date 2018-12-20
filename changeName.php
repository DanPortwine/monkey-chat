<?php
require 'header.php';
// updates the value of the chat's title then redirects the user to the page they were last on
$conn->query("UPDATE privatechats SET chatTitle = '{$_POST['chatTitle']}' WHERE chatID = {$_SESSION['chatID']}");
echo '<script>window.location=("' . $_SESSION['page'] . '");</script>';
require 'footer.php';
?>