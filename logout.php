<?php
require 'header.php';
$_SESSION = array(); // clears the current PHP session, therefore setting the user to logged out
echo '<script>window.location = "index.php";</script>';
require 'footer.php';
?>