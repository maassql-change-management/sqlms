<?php

//- HTML: login screen if not authorized, exit
if(!$auth->isAuthorized())
{
	echo "<div id='loginBox'>";
	echo "<h1><span id='logo'>".PROJECT."</span> <span id='version'>v".VERSION."</span></h1>";
	echo "<div style='padding:15px; text-align:center;'>";
	if ($auth->isFailedLogin())
		echo "<span class='warning'>".$lang['passwd_incorrect']."</span><br/><br/>";
	echo "<form action='".PAGE."' method='post'>";
	echo $lang['passwd'].": <input type='password' name='password'/><br/>";
	echo "<label><input type='checkbox' name='remember' value='yes' checked='checked'/> ".$lang['remember']."</label><br/><br/>";
	echo "<input type='submit' value='".$lang['login']."' class='btn'/>";
	echo "<input type='hidden' name='login' value='true' />";
	echo "</form>";
	echo "</div>";
	echo "</div>";
	echo "<br/>";
	echo "<div style='text-align:center;'>";
	echo "<span style='font-size:11px;'>".$lang['powered']." <a href='".PROJECT_URL."' target='_blank' style='font-size:11px;'>".PROJECT."</a> | "; 
	printf($lang['page_gen'], $pageTimer);
	echo "</span></div>";
	echo "</body></html>";
	exit();
}