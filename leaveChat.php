<?php
require 'header.php';
// deletes the user from the chat
$conn->query("DELETE FROM chatmembers WHERE memberID = {$_SESSION['userID']} AND chatID = {$_SESSION['chatID']}");
echo '<script>window.location="private.php";</script>';
?>