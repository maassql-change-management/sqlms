<?php

//- Select database (from session or first available)
if(!isset($_SESSION[COOKIENAME.'currentDB']) && count($databases)>0)
{
	//set the current database to the first existing one in the array (default)
	$_SESSION[COOKIENAME.'currentDB'] = reset($databases);
}
if(sizeof($databases)>0)
	$currentDB = $_SESSION[COOKIENAME.'currentDB'];
else // the database array is empty, offer to create a new database
{
	//- HTML: form to create a new database, exit
	if($directory!==false && is_writable($directory))
	{
		echo "<div class='confirm' style='margin:20px;'>";
		printf($lang['no_db'], PROJECT, PROJECT);
		echo "</div>";	
		if(isset($extension_not_allowed))
		{
			echo "<div class='confirm' style='margin:10px 20px;'>";
			echo $lang['err'].': '.$lang['extension_not_allowed'].': ';
			echo implode(', ', array_map('htmlencode', $allowed_extensions));
			echo '<br />'.$lang['add_allowed_extension'];
			echo "</div><br/>";
		}			
		echo "<fieldset style='margin:15px;'><legend><b>".$lang['db_create']."</b></legend>";
		echo "<form name='create_database' method='post' action='".PAGE."'>";
		echo "<input type='text' name='new_dbname' style='width:150px;'/> <input type='submit' value='".$lang['create']."' class='btn'/>";
		echo "</form>";
		echo "</fieldset>";
	}
	else
	{
		echo "<div class='confirm' style='margin:20px;'>";
		echo $lang['err'].": ".sprintf($lang['no_db2'], PROJECT);
		echo "</div><br/>";	
	}
	exit();
}