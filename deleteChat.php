<?php
require 'header.php';
// delete each element of the private chat in the right order
$conn->query("UPDATE privatechats SET latestMessage = NULL WHERE chatID = {$_SESSION['chatID']}");
$conn->query("DELETE FROM privatemessages WHERE chatID = {$_SESSION['chatID']}");
$conn->query("DELETE FROM chatmembers WHERE chatID = {$_SESSION['chatID']}");
$conn->query("DELETE FROM chatinvites WHERE chatID = {$_SESSION['chatID']}");
$conn->query("DELETE FROM privatechats WHERE chatID = {$_SESSION['chatID']}");
unset($_SESSION['chatID']); // removes the chat's ID from the session
$_SESSION['message'] .= 'Successfully deleted chat';
// redirects to the private page
echo '<script>window.location=("private.php");</script>';
require 'footer.php';
?>