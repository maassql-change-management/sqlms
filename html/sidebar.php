<?php
//- HTML: sidebar
echo '<table class="body_tbl" width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td valign="top" class="left_td" style="width:100px; padding:9px 2px 9px 9px;">';
echo "<div id='leftNav'>";
echo "<h1><a href='".PAGE."'>";
echo "<span id='logo'>".PROJECT."</span> <span id='version'>v".VERSION."</span>";
echo "</a></h1>";
echo "<div id='headerlinks'>";
echo "<a href='javascript:void' onclick='openHelp(\"top\");'>".$lang['docu']."</a> | ";
echo "<a href='http://www.gnu.org/licenses/gpl.html' target='_blank'>".$lang['license']."</a> | ";
echo "<a href='".PROJECT_URL."' target='_blank'>".$lang['proj_site']."</a>";
echo "</div>";