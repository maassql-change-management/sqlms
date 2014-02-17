<?php
// check whether a path is a db managed by this tool
// requires that $databases is already filled!
// returns the key of the db if managed, false otherwise.
function isManagedDB($path)
{
	global $databases;
	foreach($databases as $db_key => $database)
	{
		if($path == $database['path'])
		{
			// a db we manage. Thats okay.
			// return the key.
			return $db_key;
		}
	}
	// not a db we manage!
	return false;
}