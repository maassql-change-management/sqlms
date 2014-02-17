<?php

// HTML: output help text, then exit
if(isset($_GET['help']))
{
	//help section array
	$help = array
	(
		$lang['help1'] => sprintf($lang['help1_x'], PROJECT, PROJECT, PROJECT), $lang['help2'] => $lang['help2_x'], $lang['help3'] => $lang['help3_x'], 
		$lang['help4'] => $lang['help4_x'], $lang['help5'] => $lang['help5_x'], $lang['help6'] => $lang['help6_x'],
		$lang['help7'] => $lang['help7_x'], $lang['help8'] => $lang['help8_x'], $lang['help9'] => $lang['help9_x']
	);
	?>
	</head>
	<body style="direction:<?php echo $lang['direction']; ?>;">
	<div id='help_container'>
	<?php
	echo "<div class='help_list'>";
	echo "<span style='font-size:18px;'>".PROJECT." v".VERSION." ".$lang['help_doc']."</span><br/><br/>";
	foreach((array)$help as $key => $val)
	{
		echo "<a href='#".$key."'>".$key."</a><br/>";
	}
	echo "</div>";
	echo "<br/><br/>";
	foreach((array)$help as $key => $val)
	{
		echo "<div class='help_outer'>";
		echo "<a class='headd' name='".$key."'>".$key."</a>";
		echo "<div class='help_inner'>";
		echo $val;
		echo "</div>";
		echo "<a class='help_top' href='#top'>".$lang['back_top']."</a>"; 
		echo "</div>";
	}
	?>
	</div>
	</body>
	</html>
	<?php
	exit();		
}