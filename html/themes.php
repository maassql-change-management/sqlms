<?php
//- HTML: css/theme include
if(isset($_GET['theme'])) $theme = basename($_GET['theme']);

// allow themes to be dropped in subfolder "themes"
if(is_file('themes/'.$theme)) $theme = 'themes/'.$theme;

if (file_exists($theme))
	// an external stylesheet exists - import it
	echo "<link href='{$theme}' rel='stylesheet' type='text/css' />", PHP_EOL;
else
	// only use the default stylesheet if an external one does not exist
	echo "<link href='?resource=css' rel='stylesheet' type='text/css' />", PHP_EOL;
