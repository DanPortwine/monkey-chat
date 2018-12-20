<?php
require 'header.php';
$conn->query("DELETE FROM globalChat;"); //empties the chat table
$conn->query("ALTER TABLE globalChat AUTO_INCREMENT = 0;"); // resets the auto increment
echo '<script>window.location = "global.php";</script>';
require 'footer.php';
?>