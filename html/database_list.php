<?php

//- HTML: database list
echo "<fieldset style='margin:15px;'><legend><b>".$lang['db_ch']."</b></legend>";
if(sizeof($databases)<10) //if there aren't a lot of databases, just show them as a list of links instead of drop down menu
{
	$i=0;
	foreach($databases as $database)
	{
		$i++;
		echo '[' . ($database['readable'] ? 'r':' ' ) . ($database['writable'] && $database['writable_dir'] ? 'w':' ' ) . '] ';
		$url_path = str_replace(DIRECTORY_SEPARATOR,'/',$database['path']);
		if($database == $_SESSION[COOKIENAME.'currentDB'])
			echo "<a href='?switchdb=".urlencode($database['path'])."' class='active_db'>".htmlencode($database['name'])."</a>  (<a href='".htmlencode($url_path)."' title='".$lang['backup']."'>&darr;</a>)";
		else
			echo "<a href='?switchdb=".urlencode($database['path'])."'>".htmlencode($database['name'])."</a>  (<a href='".htmlencode($url_path)."' title='".$lang['backup']."'>&darr;</a>)";
		if($i<sizeof($databases))
			echo "<br/>";
	}
}
else //there are a lot of databases - show a drop down menu
{
	echo "<form action='".PAGE."' method='post'>";
	echo "<select name='database_switch'>";
	foreach($databases as $database)
	{
		$perms_string = htmlencode('[' . ($database['readable'] ? 'r':' ' ) . ($database['writable'] && $database['writable_dir'] ? 'w':' ' ) . '] ');
		if($database == $_SESSION[COOKIENAME.'currentDB'])
			echo "<option value='".htmlencode($database['path'])."' selected='selected'>".$perms_string.htmlencode($database['name'])."</option>";
		else
			echo "<option value='".htmlencode($database['path'])."'>".$perms_string.htmlencode($database['name'])."</option>";
	}
	echo "</select> ";
	echo "<input type='submit' value='".$lang['go']."' class='btn'>";
	echo "</form>";
}
echo "</fieldset>";
echo "<fieldset style='margin:15px;'><legend>";
echo "<a href='".PAGE."'";
if (!$target_table)
	echo " class='active_table'";
echo ">".htmlencode($currentDB['name'])."</a>";
echo "</legend>";