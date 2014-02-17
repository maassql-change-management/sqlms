<?php
//- HTML: breadcrumb navigation
echo "<a href='".PAGE."'>".htmlencode($currentDB['name'])."</a>";
if ($target_table)
	echo " &rarr; <a href='?table=".urlencode($target_table)."&amp;action=row_view'>".htmlencode($target_table)."</a>";
echo "<br/><br/>";