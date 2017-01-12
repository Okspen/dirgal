<?php
	require_once('config.php');
	$curDir = new dir($_GET['path']);
	$view = new view($curDir->folderContent);
?>