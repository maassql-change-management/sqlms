<?php
//the function echo the help [?] links to the documentation
function helpLink($name)
{
	global $lang;
	return "<a href='?help=1' onclick='openHelp(\"".$name."\"); return false;' class='helpq' title='".$lang['help'].": ".$name."' target='_blank'><span>[?]</span></a>";	
}