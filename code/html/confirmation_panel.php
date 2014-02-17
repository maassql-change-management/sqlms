<?php
//- HTML: confirmation panel
//if the user has performed some action, show the resulting message
if(isset($_GET['confirm']))
{
	echo "<div id='main'>";
	echo "<div class='confirm'>";
	if(isset($error) && $error) //an error occured during the action, so show an error message
		echo $lang['err'].": ".$db->getError()."<br/>".$lang['bug_report'].' '.PROJECT_BUGTRACKER_LINK;
	else //action was performed successfully - show success message
		echo $completed;
	echo "</div>";
	if($_GET['action']=="row_delete" || $_GET['action']=="row_create" || $_GET['action']=="row_edit")
		echo "<br/><br/><a href='?table=".urlencode($target_table)."&amp;action=row_view'>".$lang['return']."</a>";
	else if($_GET['action']=="column_create" || $_GET['action']=="column_delete" || $_GET['action']=="column_edit" || $_GET['action']=="index_create" || $_GET['action']=="index_delete" || $_GET['action']=="trigger_delete" || $_GET['action']=="trigger_create")
		echo "<br/><br/><a href='?table=".urlencode($target_table)."&amp;action=column_view'>".$lang['return']."</a>";
	else
		echo "<br/><br/><a href='".PAGE.(isset($backlinkParameters)?"?".$backlinkParameters:'')."'>".$lang['return']."</a>";
	echo "</div>";
}