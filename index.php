<?php
require 'header.php'; // loads the header file
$_SESSION['page'] = 'index';
?>
<script> // sets the style for the tab of the current page
	loadNews();
</script>
<?php
if (isset($userID)){
	if ($userType == 'admin'){
		// displays the article entry area if the user is admin
		echo '
		<form id="enterNews" action="sendArticle.php" method="post">
			<textarea class="messageBodyEntry news" rows="5" cols="93" maxlength="1000" name="articleBody" autofocus></textarea>
			<input class="sendMessageButton" style="height:87px;margin-right:0px;" type="submit" value="Send"><br><br><br><br><br>
		</form>
		';
	}
}
// displays all of the articles
foreach ($conn->query("SELECT articleID FROM news ORDER BY articleID DESC") as $row){
	$timeStamp = mysqli_fetch_row($conn->query("SELECT articleTime FROM news WHERE articleID = {$row['articleID']}"))[0];
	$article = mysqli_fetch_row($conn->query("SELECT articleBody FROM news WHERE articleID = {$row['articleID']}"))[0];
	echo '<b>' . substr($timeStamp,8,2) . '/' . substr($timeStamp,5,2) . '/' . substr($timeStamp,0,4) . ' - ' . substr($timeStamp,11,5) . ':</b><br>' . $article . '<br><hr class="newsSplitter">';
}
require 'footer.php'; // loads the footer file
?>