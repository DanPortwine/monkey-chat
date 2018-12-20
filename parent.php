<?php
require 'header.php';
$_SESSION['page'] = 'parent.php';
$page = 'parent.php';
?>
<script> // sets the style for the tab of the current page
	loadParent();
</script>
<div class="messages" id="messages">
<?php require 'messages.php'; //loads the messages section of the page?>
</div>
<?php
require 'sendMessageDiv.php';
require 'footer.php';
?>