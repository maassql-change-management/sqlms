<?php
// checks the (new) name of a database file  
function checkDbName($name)
{
	global $allowed_extensions;
	$info = pathinfo($name);
	if(isset($info['extension']) && !in_array($info['extension'], $allowed_extensions))
	{
		return false;
	} else
	{
		return (!is_file($name) && !is_dir($name));
	}

}