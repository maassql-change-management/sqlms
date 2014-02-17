<?php

//- Switch to a different database with drop-down menu
if(isset($_POST['database_switch']))
{
	foreach($databases as $db_id => $database)
	{
		if($database['path'] == $_POST['database_switch'])
		{
			$_SESSION[COOKIENAME."currentDB"] = $database;
			break;
		}
	}
	$currentDB = $_SESSION[COOKIENAME.'currentDB'];
}
else if(isset($_GET['switchdb']))
{
	foreach($databases as $db_id => $database)
	{
		if($database['path'] == $_GET['switchdb'])
		{
			$_SESSION[COOKIENAME."currentDB"] = $database;
			break;
		}
	}
	$currentDB = $_SESSION[COOKIENAME.'currentDB'];
}