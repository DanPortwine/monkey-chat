<?php
require 'session.php';
require 'connection.php';
// delete the user from the chat if they are not themself
if ($_POST['userID'] != $_SESSION['userID']) {
    $conn->query("DELETE FROM chatmembers WHERE chatID = {$_SESSION['chat']}");
}?>