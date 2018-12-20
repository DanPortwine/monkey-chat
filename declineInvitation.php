<?php
require 'header.php';
// remove the invitation from the chatInvites table
$conn->query("DELETE FROM chatinvites WHERE userID = {$_SESSION['userID']} AND chatID = {$_SESSION['chatID']}");
// redirects the user to the chats list page
echo '<script>window.location="private.php";</script>';
?>