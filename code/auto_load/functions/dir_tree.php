<?php

//function to scan entire directory tree and subdirectories
function dir_tree($dir)
{
	$path = '';
	$stack[] = $dir;
	while($stack)
	{
		$thisdir = array_pop($stack);
		if($dircont = scandir($thisdir))
		{
			$i=0;
			while(isset($dircont[$i]))
			{
				if($dircont[$i] !== '.' && $dircont[$i] !== '..')
				{
					$current_file = $thisdir.DIRECTORY_SEPARATOR.$dircont[$i];
					if(is_file($current_file))
					{
						$path[] = $thisdir.DIRECTORY_SEPARATOR.$dircont[$i];
					}
					elseif (is_dir($current_file))
					{
						$path[] = $thisdir.DIRECTORY_SEPARATOR.$dircont[$i];
						$stack[] = $current_file;
					}
				}
				$i++;
			}
		}
	}
	return $path;
}