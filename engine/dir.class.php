<?php

class dir
{
	private $curDir;
	private $curFile = '';
	public $folderContent;

	public function __construct($path = '')
	{
		try
		{
			if($this->setDir(strval($path)))
				$this->getFolderContent();
		}
		catch (Exception $e)
		{
			include(THEME_DIR.'/error.php');
			exit;
		}
			
	}

	/*
	 * задает текущую директорию
	 */

	private function setDir($path)
	{
		if($path)
		{
			$path = replacePathDivider($path);
			if(!is_file(START_DIR.$path))
			{
				$this->curDir = START_DIR.$path;
			}
			else
			{
				$this->curDir = folderItem::levelUp(START_DIR.$path);
				
				$tmp = explode(PATH_DIVIDER, $path);
				$tmp = $tmp[count($tmp)-1];
					
				$this->curFile = $tmp;
			}

		}
		else $this->curDir = START_DIR;
		return true;
	}

	/*
	 * записывает в $this->folderContent данные о файлах/папках в текущей директории
	 * состав массива - объекты класса folderItem;
	 */
		
	public function getFolderContent()
	{
		//if(is_dir($this->curDir;
		if(is_readable($this->curDir)) $folderContent = scandir($this->curDir);
		else throw new Exception("403") ;
		//print_r($folderContent);
		$count = count($folderContent);
		$j = 0;
		for($i = 0; $i < $count; $i++)
		{
			$full_path = $this->curDir.PATH_DIVIDER.$folderContent[$i];
			//если текущий файл задан
			if($this->curFile)
			{
				if($folderContent[$i] == $this->curFile) $cf = true;
				else $cf = false;
			}
			// если не задан
			else
			{
				if(view::isImg(folderItem::file_type($folderContent[$i])))
				{
					$this->curFile = $folderContent[$i];
					$cf = true;
				}
				else $cf = false;
			}
			if($this->isAllowed($full_path))
				$this->folderContent[$j++] = new folderItem($folderContent[$i], $full_path, $cf);
		}
	}
	
	/*
	 * проверяет не исключается ли объект правилами
	 */
	
	private function isAllowed($path)
	{
		$name = explode('/',$path);
		$name = $name[count($name)-1];
		//echo $this->curDir. ' ' .START_DIR.' '.$name.'<br>';
		if(is_dir($path) && (($this->curDir == START_DIR.'/' && $name == '..') || $name == '.')) return false;
		return (is_dir($path) || view::isImg(folderItem::file_type($path)));
	}

	/*
	 * вроде и так ясно
	 */

	public function debug()
	{
		echo 'curDir = '.$this->curDir;
		echo '<br>';
	}
}
?>