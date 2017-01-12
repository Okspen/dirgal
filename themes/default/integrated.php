	<link rel="stylesheet" href="<?php echo $data['themePath'].'/style.css'; ?>" media="all">
	<div class="folders">
		<ul>
		<?php
			foreach($data['folders'] as $key => $value)
			{
				$type = $data['folders'][$key]->type;
				$viewName = $data['folders'][$key]->viewName;
				$viewPath = $data['folders'][$key]->viewPath;
		?>	
			<li><a class="<?php echo $type; ?>" href="<?php echo '#'.$viewPath; ?>"><?php echo $viewName; ?></a></li>
		<?php	
			}
		?>	
		</ul>
	</div>
	
	<?php if(isset($data['images'])){ ?>
	<div class="image">
		<a href="<?php echo $data['curImage']->thumbnailPaths['lightbox']; ?>">
			<img id="big-preview" src="<?php  echo ROOT_DIR_DEP.'/thumbnails/big/'.folderItem::getViewPath($data['curImage']->fullPath); ?>" alt="" />
		</a>
		<div class="image-info">
			<a href="<?php echo START_DIR_DEP.$data['curImage']->viewPath; ?>">
				<img src="<?php echo $data['themePath']; ?>/img/link-image.png" />
			</a>
			<a class="rotate-left" href="<?php echo START_DIR_DEP.$data['curImage']->viewPath; ?>">
				<img src="<?php echo $data['themePath']; ?>/img/rotate-left.png" />
			</a>
			<a class="rotate-right" href="<?php echo START_DIR_DEP.$data['curImage']->viewPath; ?>">
				<img src="<?php echo $data['themePath']; ?>/img/rotate-right.png" />
			</a>
		</div>
	</div>
	
	<a class="prevPage"></a><a class="nextPage"></a>
	
	<div class="files">
		<div class="items">
		<?php
			//echo '<pre>'.print_r($data, true).'</pre>';
			foreach($data['images'] as $key=>$value)
			{
				($data['images'][$key]->current) ? $class = 'active' : $class = '';
				
				$name = $data['images'][$key]->name;
				$viewName = $data['images'][$key]->viewName;
				$viewPath = $data['images'][$key]->viewPath;
				$thumbnailPaths = $data['images'][$key]->thumbnailPaths;
				//тут мы получаем "полный" адрес превью относительно ф.с.
				//так получилось, что имея исходные данные только так можно
				//универсально получить то, что нужно
				$exists = file_exists(ROOT_DIR.substr($thumbnailPaths['small'], strlen(ROOT_DIR_DEP)));
		?>
			<a href="<?php echo '#'.$viewPath; ?>" class="<?php echo $class; ?>">	
				<img src="<?php if($exists) echo $thumbnailPaths['small']; ?>" alt="<?php echo $name; ?>" />
				<p><?php echo $name; ?></p>
			</a><?php
			}?>
		</div>
	</div>
	<?php } ?>
