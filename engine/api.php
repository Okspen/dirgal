<?php
	require_once(realpath(dirname(__FILE__).'/..').'/config.php');
	$action = $_POST['action'];
	$path = $_POST['path'];
	
	if(isset($_POST['size']))
		$sizeName = $_POST['size'];
	
	if(isset($_POST['angle']))
		$angle = $_POST['angle'];
	
	$name = explode('/',$path);
	$data['name'] = $name[count($name)-1];
	$data['fullPath'] = START_DIR.$path;
	
	//print_r($data);
	switch($action)
	{
		case 'thumbnail':
			//echo $sizeName;
			$thumbnailPath = folderItem::createThumbnail($data, $sizeName);
			echo ROOT_DIR_DEP.substr($thumbnailPath, strlen(ROOT_DIR));
		break;
		case 'rotateThumbnail':
			folderItem::rotateThumbnail($data, $angle);
			//echo ROOT_DIR_DEP.substr($thumbnailPath, strlen(ROOT_DIR));
		break;
	}
?>