<?php
require 'header.php';
$article = $_POST['articleBody'];
// insert the article data into the news table
$conn->query("INSERT INTO news (articleBody) VALUES ('{$article}')");
echo '<script>window.location="index.php";</script>';
require 'footer.php';
?>