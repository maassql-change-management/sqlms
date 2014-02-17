<?php
//- Show the various tab views for a table
if(!isset($_GET['confirm']) && $target_table && isset($_GET['action']) && ($_GET['action']=="table_export" || $_GET['action']=="table_import" || $_GET['action']=="table_sql" || $_GET['action']=="row_view" || $_GET['action']=="row_create" || $_GET['action']=="column_view" || $_GET['action']=="table_rename" || $_GET['action']=="table_search" || $_GET['action']=="table_triggers"))
{
	//- HTML: tabs for tables
	if($target_table_type == 'table')
	{
		echo "<a href='?table=".urlencode($target_table)."&amp;action=row_view' ";
		if($_GET['action']=="row_view")
			echo "class='tab_pressed'";
		else
			echo "class='tab'";
		echo ">".$lang['browse']."</a>";
		echo "<a href='?table=".urlencode($target_table)."&amp;action=column_view' ";
		if($_GET['action']=="column_view")
			echo "class='tab_pressed'";
		else
			echo "class='tab'";
		echo ">".$lang['struct']."</a>";
		echo "<a href='?table=".urlencode($target_table)."&amp;action=table_sql' ";
		if($_GET['action']=="table_sql")
			echo "class='tab_pressed'";
		else
			echo "class='tab'";
		echo ">".$lang['sql']."</a>";
		echo "<a href='?table=".urlencode($target_table)."&amp;action=table_search' ";
		if($_GET['action']=="table_search")
			echo "class='tab_pressed'";
		else
			echo "class='tab'";
		echo ">".$lang['srch']."</a>";
		echo "<a href='?table=".urlencode($target_table)."&amp;action=row_create' ";
		if($_GET['action']=="row_create")
			echo "class='tab_pressed'";
		else
			echo "class='tab'";
		echo ">".$lang['insert']."</a>";
		echo "<a href='?table=".urlencode($target_table)."&amp;action=table_export' ";
		if($_GET['action']=="table_export")
			echo "class='tab_pressed'";
		else
			echo "class='tab'";
		echo ">".$lang['export']."</a>";
		echo "<a href='?table=".urlencode($target_table)."&amp;action=table_import' ";
		if($_GET['action']=="table_import")
			echo "class='tab_pressed'";
		else
			echo "class='tab'";
		echo ">".$lang['import']."</a>";
		echo "<a href='?table=".urlencode($target_table)."&amp;action=table_rename' ";
		if($_GET['action']=="table_rename")
			echo "class='tab_pressed'";
		else
			echo "class='tab'";
		echo ">".$lang['rename']."</a>";
		echo "<a href='?action=table_empty&amp;table=".urlencode($target_table)."' ";
		echo "class='tab empty'";
		echo ">".$lang['empty']."</a>";
		echo "<a href='?action=table_drop&amp;table=".urlencode($target_table)."' ";
		echo "class='tab drop'";
		echo ">".$lang['drop']."</a>";
		echo "<div style='clear:both;'></div>";
	}
	else
	//- HTML: tabs for views
	{
		echo "<a href='?table=".urlencode($target_table)."&amp;action=row_view' ";
		if($_GET['action']=="row_view")
			echo "class='tab_pressed'";
		else
			echo "class='tab'";
		echo ">".$lang['browse']."</a>";
		echo "<a href='?table=".urlencode($target_table)."&amp;action=column_view' ";
		if($_GET['action']=="column_view")
			echo "class='tab_pressed'";
		else
			echo "class='tab'";
		echo ">".$lang['struct']."</a>";
		echo "<a href='?table=".urlencode($target_table)."&amp;action=table_sql' ";
		if($_GET['action']=="table_sql")
			echo "class='tab_pressed'";
		else
			echo "class='tab'";
		echo ">".$lang['sql']."</a>";
		echo "<a href='?table=".urlencode($target_table)."&amp;action=table_search' ";
		if($_GET['action']=="table_search")
			echo "class='tab_pressed'";
		else
			echo "class='tab'";
		echo ">".$lang['srch']."</a>";
		echo "<a href='?table=".urlencode($target_table)."&amp;action=table_export' ";
		if($_GET['action']=="table_export")
			echo "class='tab_pressed'";
		else
			echo "class='tab'";
		echo ">".$lang['export']."</a>";
		echo "<a href='?action=view_drop&amp;table=".urlencode($target_table)."' ";
		echo "class='tab drop'";
		echo ">".$lang['drop']."</a>";
		echo "<div style='clear:both;'></div>";
	}
}