<?php
	require_once(realpath(dirname(__FILE__)).'/engine/functions.php');
	
	error_reporting(E_ALL);
	
	define('PATH_DIVIDER', '/');
	// путь к папке, где установлена галерея относительно файловой системы
	define('ROOT_DIR', replacePathDivider(realpath(dirname(__FILE__))));
	// путь к папке галереи относительно домена
	define('ROOT_DIR_DEP', '/dirgal');
	// путь к папке, где находятся фотографии, относительно ф.с.
	define('START_DIR', ROOT_DIR.'/photos');
	// путь к папке, где находятся фотографии, относительно домена
	define('START_DIR_DEP', ROOT_DIR_DEP.'/photos');

	define('PREVIEW_small', 105);
	define('PREVIEW_big', 550);
	define('PREVIEW_lightbox', 750);
	
	// качество превью (влияет только на размер)
	define('JPEG_QUALITY', 70);
	
	define('THEME', 'default');
	define('THEME_DIR', ROOT_DIR.'/themes/'.THEME);
	
	function __autoload($className)
	{
		$path = ROOT_DIR.'/engine/'.$className.'.class.php';
		require_once $path;
	}
?>