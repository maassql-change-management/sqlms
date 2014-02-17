<?php
//- HTML: page footer
echo "<br/>";
echo "<span style='font-size:11px;'>".$lang['powered']." <a href='".PROJECT_URL."' target='_blank' style='font-size:11px;'>".PROJECT."</a> | ";
printf($lang['page_gen'], $pageTimer);
echo "</span>";
echo "</td></tr></table>";
$db->close(); //close the database
echo "</body>";
echo "</html>";