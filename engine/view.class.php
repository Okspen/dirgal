<?php
class view
{
	private $theme = 'default';
	private $content;
	public $html;

	public function __construct($content)
	{
		$this->content = $content;
		$this->render();
	}

	public function render()
	{
		if($this->content == '404')
			include(ROOT_DIR.'/themes/'.$this->theme.'/404.php');
		else
		{
			$data = $this->getData();
			if($_SERVER['PHP_SELF'] == ROOT_DIR_DEP.'/ajax.php')
				include(ROOT_DIR.'/themes/'.$this->theme.'/integrated.php');
			else
				include(ROOT_DIR.'/themes/'.$this->theme.'/index.php');
		}
	}

	private function getData()
	{
		$data['themePath'] = ROOT_DIR_DEP.'/themes/'.$this->theme;
		$count = count($this->content);
		$fc = 0;
		$ic = 0;
		//echo '<pre>'.print_r($this->content,true).'</pre>';
		for($i=0; $i<$count; $i++)
		{
			// в массиве исключенных типов и имен ищеться,
			// не исключается ли этот элемент из списка по типу или имени
			//
			if(!is_file($this->content[$i]->fullPath))
			{
				$data['folders'][$fc++] = $this->content[$i];
			}
			if($this->isImg($this->content[$i]->type))
			{
				$data['images'][$ic++] = $this->content[$i];
				if($this->content[$i]->isCurrent()) $data['curImage'] = $this->content[$i];
			}
		}
		
		return $data;
	}

	private function countFiles()
	{
		$j = 0;
		for($i=0; $i<count($this->content); $i++)
		{
			if($this->isImg($this->content[$i]->type)) $j++;
		}
		return $j;
	}

	/*
	 * "определяет" с помощью предыдущей ф-ии, является ли файл изображением
	 */

	public static function isImg($type)
	{
		$imgTypesArray = array('jpg','jpeg','png','gif','bmp');
		return in_array($type, $imgTypesArray);
	}
}
?>