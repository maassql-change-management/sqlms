<?php


//- HTML: table list
$query = "SELECT type, name FROM sqlite_master WHERE type='table' OR type='view' ORDER BY name";
$result = $db->selectArray($query);
$j=0;
for($i=0; $i<sizeof($result); $i++)
{
	if(substr($result[$i]['name'], 0, 7)!="sqlite_" && $result[$i]['name']!="")
	{
		echo "<span class='sidebar_table'>[".$lang[$result[$i]['type']=='table'?'tbl':'view']."]</span> ";
		echo "<a href='?action=row_view&amp;table=".urlencode($result[$i]['name'])."'";
		if ($target_table == $result[$i]['name'])
			echo " class='active_table'";
		echo ">".htmlencode($result[$i]['name'])."</a><br/>";
		$j++;
	}
}
if($j==0)
	echo $lang['no_tbl'];
echo "</fieldset>";
