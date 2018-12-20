<?php
require 'header.php';
// insert user's details into chatmembers table, removes them form the chatInvites table and redirects them to the chat page
$conn->query("INSERT INTO chatmembers (chatID,memberID,isCreator) VALUES ({$_SESSION['chatID']},{$_SESSION['userID']},0)");
$conn->query("DELETE FROM chatInvites WHERE chatID = {$_SESSION['chatID']}");
echo '<script>window.location="chat.php?chatID=' . $_SESSION['chatID'] . '";</script>';
?>