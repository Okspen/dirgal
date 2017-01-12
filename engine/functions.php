<?php
	function arrayUnsetFromEnd($array, $n)
	{
		$count = count($array);
		for($i=1; $i<=$n; $i++)
		{
			if (isset($array[$count-$i])) unset($array[$count-$i]);
		}
		return $array;
	}
	
	function replacePathDivider($path)
	{
		return str_replace('\\', PATH_DIVIDER, $path);
	}
?>