<?php
	class folderItem
	{
		public $name;
		public $viewName;
		public $type;
		
		public $current;
		
		public $path;
		public $viewPath;
		public $fullPath;
		public $thumbnailPaths = array();
		
		public function __construct($name, $fullPath, $current = false)
		{
			$this->setName($name);
			$this->setPaths($fullPath);
			if(is_file($this->fullPath))
			{
				$this->current = $current;
				$this->type = $this->file_type($this->name);
				
				$data['name'] = $this->name;
				$data['fullPath'] = $this->fullPath;
				//$this->createThumbnail($data);
				$this->thumbnailPaths['small'] = ROOT_DIR_DEP.'/thumbnails/small'.folderItem::getViewPath($this->fullPath);
				//$this->createThumbnail($data, 'lightbox');
				$this->thumbnailPaths['lightbox'] = ROOT_DIR_DEP.'/thumbnails/lightbox'.folderItem::getViewPath($this->fullPath);
				if($this->isCurrent())
				{
					$this->createThumbnail($data, 'big');
					$this->createThumbnail($data, 'lightbox');
				}
			}
			else
			{
				if($this->name == '..') $this->type = 'up';
				else $this->type = 'dir';
			}
		}
		
		/*
		 * задает имена - полученное во время сканирования и имя без расширения
		 */
		
		private function setName($name)
		{
			$this->name = $name;
			if($name == '..') $this->viewName = 'Up';
			elseif(is_file($this->fullPath)) $this->viewName = $this->file_name($name);
			else $this->viewName = $name;
		}
		
		/*
		 * задает пути - полный от корня ф.с., относительно корня сайта и для просмотра (относительно папки галлереи)
		 */
		 
		private function setPaths($fullPath)
		{
			$this->fullPath = str_replace('\\', '/', realpath($fullPath));
			$this->path = $this->getDepPath($this->fullPath);
			$this->viewPath = $this->getViewPath($this->fullPath);
		}
		
		/*
		 * в $path передается реальный путь, возвращается вариант обрезанный по кол-ву символов START_DIR
		 */
		
		private function getDepPath($path)
		{
			if ($this->name == '..') return START_DIR_DEP.$this->levelUp($path);
			return START_DIR_DEP.substr($path, strlen(START_DIR));
		}
		
		/*
		 * URL делается красивым - по ссылке дается путь от START_DIR
		 */
		
		public function getViewPath($path)
		{
			return substr($path, strlen(START_DIR));
		}
		
		/*
		 * из пути /home/okspen делает /home
		 */
		
		public static function levelUp($path)
		{
			// из пути /prev/cur выйдет /prev
			$path = explode(PATH_DIVIDER, $path);
			$path = arrayUnsetFromEnd($path, 1);
			return $path = implode(PATH_DIVIDER, $path);
		}
		
		/*
		 * "определяет" по символам после последней точки в имени файла его расширение
		 */
		
		public static function file_type($fname)
		{
			$fname = explode('.', $fname);
			return strtolower($fname[count($fname) - 1]);
		}
		
		/*
		 * возвращает имя файла без расширения (без содержимого после последней точки)
		 */
		
		public static function file_name($fname)
		{
			$fnameArr = explode('.', $fname);
			// если имя не содержит точек (папка folder, а не файл folder.jpg)
			// то тогда есть смысл удалять содержимое после последней точки
			if(count($fnameArr)>1)	return implode('.', arrayUnsetFromEnd($fnameArr, 1));
			return $fname;
		}
		
		/*
		 * возвращает состояние флага текущего файла
		 */
		 
		public function isCurrent()
		{
			return $this->current;
		}
		
		public static function nameToSize($name)
		{
			switch($name)			
			{
				case 'small': return PREVIEW_small; break;
				case 'big': return PREVIEW_big; break;
				case 'lightbox': return PREVIEW_lightbox; break;
				case 'original': return 0;
			}
		}
		
		/*
		 * создает превью графического файла и складывает все в папку /thumbnails
		 */
		
		public static function createThumbnail($data, $sizeName = 'small', $angle = 0)
		{
			$file_info = getimagesize($data['fullPath']);
			$coeff = $file_info[1]/$file_info[0];
			
			// определяем размер в цифрах по заранее заданому имени
			$width = folderItem::nameTosize($sizeName);
			
			if($width == 0) 
			{
				$width = $file_info[0];
				$thumbnailPath = $data['fullPath'];
				$thumbnailPath = explode('/',$thumbnailPath);
				$thumbnailPath = arrayUnsetFromEnd($thumbnailPath,1);
				$thumbnailPath = implode('/',$thumbnailPath);
				$thumbnailPath .= "/tmp.jpg"; 
				
				copy($data['fullPath'], $thumbnailPath);
			}
			else
			{
				$createDir = folderItem::getViewPath($data['fullPath']);
				$createDir = arrayUnsetFromEnd(explode('/',$createDir), 1);
				$createDir = implode('/', $createDir);
				$createDir = ROOT_DIR.'/thumbnails/'.$sizeName.$createDir; 
				if(!is_readable($createDir)) mkdir($createDir, 0777, true);
				//echo ROOT_DIR.'/thumbnails/'.$sizeName.$createDir;
				$thumbnailPath = $createDir.'/'.$data['name'];
			}
			
			if(view::isImg((folderItem::file_type($data['name'])) && !file_exists($thumbnailPath)) || $sizeName == 'original')
			{
				require_once ROOT_DIR.'/lib/phpthumb/ThumbLib.inc.php';
				
				$options = array('jpegQuality' => JPEG_QUALITY);
				$thumb = PhpThumbFactory::create($data['fullPath'], $options);

				if($angle) $thumb->rotateImageNDegrees($angle);
				
				// все, кроме оригинала и маленьких превью ресайзим обычным способом, без обрезания
				if(($sizeName != 'original') && ($sizeName != 'small')) $thumb->resize($width, $width/$coeff);

				// маленькие превью мы ресайзим обрезая до нужного размера 105*79
				// ширина настраивается, а коэфициент допиливает высоту до нужного размера 
				if($sizeName == 'small') $thumb->adaptiveResize($width, $width/1.32911);
				
				// после обработки оригинала сохраняем во временный файл, потом копируем его в оригинальный
				// и удаляем временный файл
				if($sizeName == "original")
				{
					$thumb->save($thumbnailPath);
					copy($thumbnailPath, $data['fullPath']);
					unlink($thumbnailPath);
				}
				// сохраняем обычное превью
				else $thumb->save($thumbnailPath);
			}
			return $thumbnailPath;
		}

		public static function rotateThumbnail($data, $angle = 0)
		{
			//поворачиваем превью
			unlink(ROOT_DIR."/thumbnails/small/".$data['name']);
			unlink(ROOT_DIR."/thumbnails/big/".$data['name']);
			unlink(ROOT_DIR."/thumbnails/lightbox/".$data['name']);
			folderItem::createThumbnail($data,'small',$angle);
			folderItem::createThumbnail($data,'big',$angle);
			folderItem::createThumbnail($data,'lightbox',$angle);
			//поворачиваем оригинал
			folderItem::createThumbnail($data, 'original', $angle);
		}		
	}
?>
