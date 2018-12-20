<?php
require 'header.php';
// removes the relationship from friends table
$conn->query("DELETE FROM friends WHERE friendshipID = {$_POST['userID']}");
echo '<script>window.location="profile.php";</script>'; // redirects to user's profile page
require 'footer.php';
?>