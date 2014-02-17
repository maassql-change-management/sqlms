<?php
//- HMTL: tabs for databases	
if(!$target_table && !isset($_GET['confirm']) && (!isset($_GET['action']) || (isset($_GET['action']) && $_GET['action']!="table_create"))) //the absence of these fields means we are viewing the database homepage
{
	$view = isset($_GET['view']) ? $_GET['view'] : 'structure';

	echo "<a href='?view=structure' ";
	if($view=="structure")
		echo "class='tab_pressed'";
	else
		echo "class='tab'";
	echo ">".$lang['struct']."</a>";
	echo "<a href='?view=sql' ";
	if($view=="sql")
		echo "class='tab_pressed'";
	else
		echo "class='tab'";
	echo ">".$lang['sql']."</a>";
	echo "<a href='?view=export' ";
	if($view=="export")
		echo "class='tab_pressed'";
	else
		echo "class='tab'";
	echo ">".$lang['export']."</a>";
	echo "<a href='?view=import' ";
	if($view=="import")
		echo "class='tab_pressed'";
	else
		echo "class='tab'";
	echo ">".$lang['import']."</a>";
	echo "<a href='?view=vacuum' ";
	if($view=="vacuum")
		echo "class='tab_pressed'";
	else
		echo "class='tab'";
	echo ">".$lang['vac']."</a>";
	if($directory!==false && is_writable($directory))
	{
		echo "<a href='?view=rename' ";
		if($view=="rename")
			echo "class='tab_pressed'";
		else
			echo "class='tab'";
		echo ">".$lang['db_rename']."</a>";
		
		echo "<a href='?view=delete' title='".$lang['db_del']."' ";
		if($view=="delete")
			echo "class='tab_pressed delete_db'";
		else
			echo "class='tab delete_db'";
		echo "><span>".$lang['db_del']."</span></a>";
	}
	echo "<div style='clear:both;'></div>";
	echo "<div id='main'>";

  //- Switch on $view (actually a series of if-else)

	if($view=="structure")
	{
		//- Database structure, shows all the tables (=structure)
		$query = "SELECT sqlite_version() AS sqlite_version";
		$queryVersion = $db->select($query);
		$realVersion = $queryVersion['sqlite_version'];
		
		if(isset($dbexists))
		{
			echo "<div class='confirm' style='margin:10px 20px;'>";
			echo $lang['err'].': '.sprintf($lang['db_exists'], htmlencode($dbname));
			echo "</div><br/>";
		}
		
		if($db->isWritable() && !$db->isDirWritable())
		{
			echo "<div class='confirm' style='margin:10px 20px;'>";
			echo $lang['attention'].': '.$lang['directory_not_writable'];
			echo "</div><br/>";
		}
		
		if(isset($extension_not_allowed))
		{
			echo "<div class='confirm' style='margin:10px 20px;'>";
			echo $lang['extension_not_allowed'].': ';
			echo implode(', ', array_map('htmlencode', $allowed_extensions));
			echo '<br />'.$lang['add_allowed_extension'];
			echo "</div><br/>";
		}

		if ($auth->isPasswordDefault())
		{
			echo "<div class='confirm' style='margin:20px 0px;'>";
			echo sprintf($lang['warn_passwd'],(is_readable('phpliteadmin.config.php')?'phpliteadmin.config.php':PAGE))."<br />".$lang['warn0'];
			echo "</div>";
		}
		
		echo "<b>".$lang['db_name']."</b>: ".htmlencode($db->getName())."<br/>";
		echo "<b>".$lang['db_path']."</b>: ".htmlencode($db->getPath())."<br/>";
		echo "<b>".$lang['db_size']."</b>: ".$db->getSize()." KB<br/>";
		echo "<b>".$lang['db_mod']."</b>: ".$db->getDate()."<br/>";
		echo "<b>".$lang['sqlite_v']."</b>: ".$realVersion."<br/>";
		echo "<b>".$lang['sqlite_ext']."</b> ".helpLink($lang['help1']).": ".$db->getType()."<br/>"; 
		echo "<b>".$lang['php_v']."</b>: ".phpversion()."<br/><br/>";
		
		if(isset($_GET['sort']) && ($_GET['sort']=='type' || $_GET['sort']=='name'))
			$_SESSION[COOKIENAME.'sortTables'] = $_GET['sort'];
		if(isset($_GET['order']) && ($_GET['order']=='ASC' || $_GET['order']=='DESC'))
			$_SESSION[COOKIENAME.'orderTables'] = $_GET['order'];
				
		$query = "SELECT type, name FROM sqlite_master WHERE (type='table' OR type='view') AND name!='' AND name NOT LIKE 'sqlite_%'";
		$queryAdd = "";
		if(isset($_SESSION[COOKIENAME.'sortTables']))
			$queryAdd .= " ORDER BY ".$db->quote_id($_SESSION[COOKIENAME.'sortTables']);
		else
			$queryAdd .= " ORDER BY \"name\"";
		if(isset($_SESSION[COOKIENAME.'orderTables']))
			$queryAdd .= " ".$_SESSION[COOKIENAME.'orderTables'];
		$query .= $queryAdd;
		$result = $db->selectArray($query);

		if(sizeof($result)==0)
			echo $lang['no_tbl']."<br/><br/>";
		else
		{
			echo "<table border='0' cellpadding='2' cellspacing='1' class='viewTable'>";
			echo "<tr>";
			
			echo "<td class='tdheader'>";
			echo "<a href='?sort=type";
			if(isset($_SESSION[COOKIENAME.'sortTables']))
				$orderTag = ($_SESSION[COOKIENAME.'sortTables']=="type" && $_SESSION[COOKIENAME.'orderTables']=="ASC") ? "DESC" : "ASC";
			else
				$orderTag = "ASC";
			echo "&amp;order=".$orderTag;
			echo "'>".$lang['type']."</a> ".helpLink($lang['help3']); 
			if(isset($_SESSION[COOKIENAME.'sortTables']) && $_SESSION[COOKIENAME.'sortTables']=="type")
				echo (($_SESSION[COOKIENAME.'orderTables']=="ASC") ? " <b>&uarr;</b>" : " <b>&darr;</b>");
			echo "</td>";
			
			echo "<td class='tdheader'>";
			echo "<a href='?sort=name";
			if(isset($_SESSION[COOKIENAME.'sortTables']))
				$orderTag = ($_SESSION[COOKIENAME.'sortTables']=="name" && $_SESSION[COOKIENAME.'orderTables']=="ASC") ? "DESC" : "ASC";
			else
				$orderTag = "ASC";
			echo "&amp;order=".$orderTag;
			echo "'>".$lang['name']."</a>";
			if(isset($_SESSION[COOKIENAME.'sortTables']) && $_SESSION[COOKIENAME.'sortTables']=="name")
				echo (($_SESSION[COOKIENAME.'orderTables']=="ASC") ? " <b>&uarr;</b>" : " <b>&darr;</b>");
			echo "</td>";
			
			echo "<td class='tdheader' colspan='10'>".$lang['act']."</td>";
			echo "<td class='tdheader'>".$lang['rec']."</td>";
			echo "</tr>";
			
			$totalRecords = 0;
			$skippedTables = false;
			for($i=0; $i<sizeof($result); $i++)
			{
				$records = $db->numRows($result[$i]['name'], (!isset($_GET['forceCount'])));
				if($records == '?')
				{
					$skippedTables = true;
					$records = "<a href='?forceCount=1'>?</a>";
				}
				else
					$totalRecords += $records;
				$tdWithClass = "<td class='td".($i%2 ? "1" : "2")."'>";
				$tdWithClassLeft = "<td class='td".($i%2 ? "1" : "2")."' style='text-align:left;'>";
				
				if($result[$i]['type']=="table")
				{
					echo "<tr>";
					echo $tdWithClassLeft;
					echo $lang['tbl'];
					echo "</td>";
					echo $tdWithClassLeft;
					echo "<a href='?table=".urlencode($result[$i]['name'])."&amp;action=row_view'>".htmlencode($result[$i]['name'])."</a>";
					echo "</td>";
					echo $tdWithClass;
					echo "<a href='?table=".urlencode($result[$i]['name'])."&amp;action=row_view'>".$lang['browse']."</a>";
					echo "</td>";
					echo $tdWithClass;
					echo "<a href='?table=".urlencode($result[$i]['name'])."&amp;action=column_view'>".$lang['struct']."</a>";
					echo "</td>";
					echo $tdWithClass;
					echo "<a href='?table=".urlencode($result[$i]['name'])."&amp;action=table_sql'>".$lang['sql']."</a>";
					echo "</td>";
					echo $tdWithClass;
					echo "<a href='?table=".urlencode($result[$i]['name'])."&amp;action=table_search'>".$lang['srch']."</a>";
					echo "</td>";
					echo $tdWithClass;
					echo "<a href='?table=".urlencode($result[$i]['name'])."&amp;action=row_create'>".$lang['insert']."</a>";
					echo "</td>";
					echo $tdWithClass;
					echo "<a href='?table=".urlencode($result[$i]['name'])."&amp;action=table_export'>".$lang['export']."</a>";
					echo "</td>";
					echo $tdWithClass;
					echo "<a href='?table=".urlencode($result[$i]['name'])."&amp;action=table_import'>".$lang['import']."</a>";
					echo "</td>";
					echo $tdWithClass;
					echo "<a href='?table=".urlencode($result[$i]['name'])."&amp;action=table_rename'>".$lang['rename']."</a>";
					echo "</td>";
					echo $tdWithClass;
					echo "<a href='?table=".urlencode($result[$i]['name'])."&amp;action=table_empty' class='empty'>".$lang['empty']."</a>";
					echo "</td>";
					echo $tdWithClass;
					echo "<a href='?table=".urlencode($result[$i]['name'])."&amp;action=table_drop' class='drop'>".$lang['drop']."</a>";
					echo "</td>";
					echo $tdWithClass;
					echo $records;
					echo "</td>";
					echo "</tr>";
				}
				else
				{
					echo "<tr>";
					echo $tdWithClassLeft;
					echo "View";
					echo "</td>";
					echo $tdWithClassLeft;
					echo "<a href='?table=".urlencode($result[$i]['name'])."&amp;action=row_view'>".htmlencode($result[$i]['name'])."</a>";
					echo "</td>";
					echo $tdWithClass;
					echo "<a href='?table=".urlencode($result[$i]['name'])."&amp;action=row_view'>".$lang['browse']."</a>";
					echo "</td>";
					echo $tdWithClass;
					echo "<a href='?table=".urlencode($result[$i]['name'])."&amp;action=column_view'>".$lang['struct']."</a>";
					echo "</td>";
					echo $tdWithClass;
					echo "<a href='?table=".urlencode($result[$i]['name'])."&amp;action=table_sql'>".$lang['sql']."</a>";
					echo "</td>";
					echo $tdWithClass;
					echo "<a href='?table=".urlencode($result[$i]['name'])."&amp;action=table_search'>".$lang['srch']."</a>";
					echo "</td>";
					echo $tdWithClass;
					echo "";
					echo "</td>";
					echo $tdWithClass;
					echo "<a href='?table=".urlencode($result[$i]['name'])."&amp;action=table_export'>".$lang['export']."</a>";
					echo "</td>";
					echo $tdWithClass;
					echo "";
					echo "</td>";
					echo $tdWithClass;
					echo "";
					echo "</td>";
					echo $tdWithClass;
					echo "";
					echo "</td>";
					echo $tdWithClass;
					echo "<a href='?table=".urlencode($result[$i]['name'])."&amp;action=view_drop' class='drop'>".$lang['drop']."</a>";
					echo "</td>";
					echo $tdWithClass;
					echo $records;
					echo "</td>";
					echo "</tr>";
				}
			}
			echo "<tr>";
			echo "<td class='tdheader' colspan='12'>".sizeof($result)." total</td>";
			echo "<td class='tdheader' colspan='1' style='text-align:right;'>".$totalRecords.($skippedTables?" <a href='?forceCount=1'>+ ?</a>":"")."</td>";
			echo "</tr>";
			echo "</table>";
			echo "<br/>";
			if($skippedTables)
				echo "<div class='confirm' style='margin-bottom:20px;'>".sprintf($lang["counting_skipped"],"<a href='?forceCount=1'>","</a>")."</div>";
		}
		echo "<fieldset>";
		echo "<legend><b>".$lang['create_tbl_db']." '".htmlencode($db->getName())."'</b></legend>";
		echo "<form action='?action=table_create' method='post'>";
		echo $lang['name'].": <input type='text' name='tablename' style='width:200px;'/> ";
		echo $lang['fld_num'].": <input type='text' name='tablefields' style='width:90px;'/> ";
		echo "<input type='submit' name='createtable' value='".$lang['go']."' class='btn'/>";
		echo "</form>";
		echo "</fieldset>";
		echo "<br/>";
		echo "<fieldset>";
		echo "<legend><b>".$lang['create_view']." '".htmlencode($db->getName())."'</b></legend>";
		echo "<form action='?action=view_create&amp;confirm=1' method='post'>";
		echo $lang['name'].": <input type='text' name='viewname' style='width:200px;'/> ";
		echo $lang['sel_state']." ".helpLink($lang['help4']).": <input type='text' name='select' style='width:400px;'/> "; 
		echo "<input type='submit' name='createtable' value='".$lang['go']."' class='btn'/>";
		echo "</form>";
		echo "</fieldset>";
	}
	else if($view=="sql")
	{
		//- Database SQL editor (=sql)
		$isSelect = false;
		if(isset($_POST['query']) && $_POST['query']!="")
		{
			$delimiter = $_POST['delimiter'];
			$queryStr = $_POST['queryval'];
			$query = explode_sql($delimiter, $queryStr); //explode the query string into individual queries based on the delimiter

			for($i=0; $i<sizeof($query); $i++) //iterate through the queries exploded by the delimiter
			{
				if(str_replace(" ", "", str_replace("\n", "", str_replace("\r", "", $query[$i])))!="") //make sure this query is not an empty string
				{
					$queryTimer = new MicroTimer();
					if(preg_match('/^\s*(?:select|pragma|explain)\s/i', $query[$i])===1)   // pragma and explain often return rows just like select
					{
						$isSelect = true;
						$result = $db->selectArray($query[$i], "assoc");
					}
					else
					{
						$isSelect = false;
						$result = $db->query($query[$i]);
					}
					$queryTimer->stop();

					echo "<div class='confirm'>";
					echo "<b>";
					// 22 August 2011: gkf fixed bugs 46, 51 and 52.
					if($result!==false)
					{
						if($isSelect)
						{
							$affected = sizeof($result);
							printf($lang['show_rows'], $affected);
						}
						else
						{
							$affected = $db->getAffectedRows();
							echo $affected." ".$lang['rows_aff']." ";
						}
						printf($lang['query_time'], $queryTimer);
						echo "</b><br/>";
					}
					else
					{
						echo $lang['err'].": ".$db->getError()."</b><br/>";
					}
					echo "<span style='font-size:11px;'>".htmlencode($query[$i])."</span>";
					echo "</div><br/>";
					if($isSelect)
					{
						if(sizeof($result)>0)
						{
							$headers = array_keys($result[0]);

							echo "<table border='0' cellpadding='2' cellspacing='1' class='viewTable'>";
							echo "<tr>";
							for($j=0; $j<sizeof($headers); $j++)
							{
								echo "<td class='tdheader'>";
								echo htmlencode($headers[$j]);
								echo "</td>";
							}
							echo "</tr>";
							for($j=0; $j<sizeof($result); $j++)
							{
								$tdWithClass = "<td class='td".($j%2 ? "1" : "2")."'>";
								echo "<tr>";
								for($z=0; $z<sizeof($headers); $z++)
								{
									echo $tdWithClass;
									if($result[$j][$headers[$z]]==="")
										echo "&nbsp;";
									elseif($result[$j][$headers[$z]]===NULL)
										echo "<i class='null'>NULL</i>";
									else
										echo subString(htmlencode($result[$j][$headers[$z]]));
									echo "</td>";
								}
								echo "</tr>";
							}
							echo "</table><br/><br/>";
						}
					}
				}
			}
		}
		else
		{
			$delimiter = ";";
			$queryStr = "";
		}

		echo "<fieldset>";
		echo "<legend><b>".sprintf($lang['run_sql'],htmlencode($db->getName()))."</b></legend>";
		echo "<form action='?view=sql' method='post'>";
		echo "<textarea style='width:100%; height:300px;' name='queryval' cols='50' rows='8'>".htmlencode($queryStr)."</textarea>";
		echo $lang['delimit']." <input type='text' name='delimiter' value='".htmlencode($delimiter)."' style='width:50px;'/> ";
		echo "<input type='submit' name='query' value='".$lang['go']."' class='btn'/>";
		echo "</form>";
		echo "</fieldset>";
	}
	else if($view=="vacuum")
	{
		//- Vacuum database confirmation (=vacuum)
		if(isset($_POST['vacuum']))
		{
			$query = "VACUUM";
			$db->query($query);
			echo "<div class='confirm'>";
			printf($lang['db_vac'], htmlencode($db->getName()));
			echo "</div><br/>";
		}
		echo "<form method='post' action='?view=vacuum'>";
		printf($lang['vac_desc'],htmlencode($db->getName()));
		echo "<br/><br/>";
		echo "<input type='submit' value='".$lang['vac']."' name='vacuum' class='btn'/>";
		echo "</form>";
	}
	else if($view=="export")
	{
		//- Export view (=export)
		echo "<form method='post' action='?view=export'>";
		echo "<fieldset style='float:left; width:260px; margin-right:20px;'><legend><b>".$lang['export']."</b></legend>";
		echo "<select multiple='multiple' size='10' style='width:240px;' name='tables[]'>";
		$query = "SELECT name FROM sqlite_master WHERE type='table' OR type='view' ORDER BY name";
		$result = $db->selectArray($query);
		for($i=0; $i<sizeof($result); $i++)
		{
			if(substr($result[$i]['name'], 0, 7)!="sqlite_" && $result[$i]['name']!="")
				echo "<option value='".htmlencode($result[$i]['name'])."' selected='selected'>".htmlencode($result[$i]['name'])."</option>";
		}
		echo "</select>";
		echo "<br/><br/>";
		echo "<label><input type='radio' name='export_type' checked='checked' value='sql' onclick='toggleExports(\"sql\");'/> ".$lang['sql']."</label>";
		echo "<br/><label><input type='radio' name='export_type' value='csv' onclick='toggleExports(\"csv\");'/> ".$lang['csv']."</label>";
		echo "</fieldset>";
		
		echo "<fieldset style='float:left; max-width:350px;' id='exportoptions_sql'><legend><b>".$lang['options']."</b></legend>";
		echo "<label><input type='checkbox' checked='checked' name='structure'/> ".$lang['export_struct']."</label> ".helpLink($lang['help5'])."<br/>"; 
		echo "<label><input type='checkbox' checked='checked' name='data'/> ".$lang['export_data']."</label> ".helpLink($lang['help6'])."<br/>";
		echo "<label><input type='checkbox' name='drop'/> ".$lang['add_drop']."</label> ".helpLink($lang['help7'])."<br/>"; 
		echo "<label><input type='checkbox' checked='checked' name='transaction'/> ".$lang['add_transact']."</label> ".helpLink($lang['help8'])."<br/>";
		echo "<label><input type='checkbox' checked='checked' name='comments'/> ".$lang['comments']."</label> ".helpLink($lang['help9'])."<br/>"; 
		echo "</fieldset>";
		
		echo "<fieldset style='float:left; max-width:350px; display:none;' id='exportoptions_csv'><legend><b>".$lang['options']."</b></legend>";
		echo "<div style='float:left;'>".$lang['fld_terminated']."</div>";
		echo "<input type='text' value=';' name='export_csv_fieldsterminated' style='float:right;'/>";
		echo "<div style='clear:both;'>";
		echo "<div style='float:left;'>".$lang['fld_enclosed']."</div>";
		echo "<input type='text' value='\"' name='export_csv_fieldsenclosed' style='float:right;'/>";
		echo "<div style='clear:both;'>";
		echo "<div style='float:left;'>".$lang['fld_escaped']."</div>";
		echo "<input type='text' value='\' name='export_csv_fieldsescaped' style='float:right;'/>";
		echo "<div style='clear:both;'>";
		echo "<div style='float:left;'>".$lang['rep_null']."</div>";
		echo "<input type='text' value='NULL' name='export_csv_replacenull' style='float:right;'/>";
		echo "<div style='clear:both;'>";
		echo "<label><input type='checkbox' name='export_csv_crlf'/> ".$lang['rem_crlf']."</label><br/>";
		echo "<label><input type='checkbox' checked='checked' name='export_csv_fieldnames'/> ".$lang['put_fld']."</label>";
		echo "</fieldset>";
		
		echo "<div style='clear:both;'></div>";
		echo "<br/><br/>";
		echo "<fieldset><legend><b>".$lang['save_as']."</b></legend>";
		$file = pathinfo($db->getPath());
		$name = $file['filename'];
		echo "<input type='text' name='filename' value='".htmlencode($name)."_".date("Y-m-d").".dump' style='width:400px;'/> <input type='submit' name='export' value='".$lang['export']."' class='btn'/>";
		echo "</fieldset>";
		echo "</form>";
	}
	else if($view=="import")
	{
		//- Import view (=import)
		if(isset($_POST['import']))
		{
			echo "<div class='confirm'>";
			if($importSuccess===true)
				echo $lang['import_suc'];
			else
				echo $importSuccess;
			echo "</div><br/>";
		}
		
		echo "<form method='post' action='?view=import' enctype='multipart/form-data'>";
		echo "<fieldset style='float:left; width:260px; margin-right:20px;'><legend><b>".$lang['import']."</b></legend>";
		echo "<label><input type='radio' name='import_type' checked='checked' value='sql' onclick='toggleImports(\"sql\");'/> ".$lang['sql']."</label>";
		echo "<br/><label><input type='radio' name='import_type' value='csv' onclick='toggleImports(\"csv\");'/> ".$lang['csv']."</label>";
		echo "</fieldset>";
		
		echo "<fieldset style='float:left; max-width:350px;' id='importoptions_sql'><legend><b>".$lang['options']."</b></legend>";
		echo $lang['no_opt'];
		echo "</fieldset>";
		
		echo "<fieldset style='float:left; max-width:350px; display:none;' id='importoptions_csv'><legend><b>".$lang['options']."</b></legend>";
		echo "<div style='float:left;'>".$lang['csv_tbl']."</div>";
		echo "<select name='single_table' style='float:right;'>";
		$query = "SELECT name FROM sqlite_master WHERE type='table' OR type='view' ORDER BY name";
		$result = $db->selectArray($query);
		for($i=0; $i<sizeof($result); $i++)
		{
			if(substr($result[$i]['name'], 0, 7)!="sqlite_" && $result[$i]['name']!="")
				echo "<option value='".htmlencode($result[$i]['name'])."'>".htmlencode($result[$i]['name'])."</option>";
		}
		echo "</select>";
		echo "<div style='clear:both;'>";
		echo "<div style='float:left;'>".$lang['fld_terminated']."</div>";
		echo "<input type='text' value=';' name='import_csv_fieldsterminated' style='float:right;'/>";
		echo "<div style='clear:both;'>";
		echo "<div style='float:left;'>".$lang['fld_enclosed']."</div>";
		echo "<input type='text' value='\"' name='import_csv_fieldsenclosed' style='float:right;'/>";
		echo "<div style='clear:both;'>";
		echo "<div style='float:left;'>".$lang['fld_escaped']."</div>";
		echo "<input type='text' value='\' name='import_csv_fieldsescaped' style='float:right;'/>";
		echo "<div style='clear:both;'>";
		echo "<div style='float:left;'>".$lang['null_represent']."</div>";
		echo "<input type='text' value='NULL' name='import_csv_replacenull' style='float:right;'/>";
		echo "<div style='clear:both;'>";
		echo "<label><input type='checkbox' checked='checked' name='import_csv_fieldnames'/> ".$lang['fld_names']."</label>";
		echo "</fieldset>";
		
		echo "<div style='clear:both;'></div>";
		echo "<br/><br/>";
		
		echo "<fieldset><legend><b>".$lang['import_f']."</b></legend>";
		echo "<input type='file' value='".$lang['choose_f']."' name='file' style='background-color:transparent; border-style:none;'/> <input type='submit' value='".$lang['import']."' name='import' class='btn'/>";
		echo "</fieldset>";
	}
	else if($view=="rename")
	{
		//- Rename database confirmation (=rename)
		if(isset($extension_not_allowed))
		{
			echo "<div class='confirm'>";
			echo $lang['extension_not_allowed'].': ';
			echo implode(', ', array_map('htmlencode', $allowed_extensions));
			echo '<br />'.$lang['add_allowed_extension'];
			echo "</div><br/>";
		}
		if(isset($dbexists))
		{
			echo "<div class='confirm'>";
			if($oldpath==$newpath)
				echo $lang['err'].": ".$lang['warn_dumbass'];
			else{
				echo $lang['err'].": "; 
				printf($lang['db_exists'], htmlencode($newpath));
			}
			echo "</div><br/>";
		}
		if(isset($justrenamed))
		{
			echo "<div class='confirm'>";
			printf($lang['db_renamed'], htmlencode($oldpath));
			echo " '".htmlencode($newpath)."'.";
			echo "</div><br/>";
		}
		echo "<form action='?view=rename&amp;database_rename=1' method='post'>";
		echo "<input type='hidden' name='oldname' value='".htmlencode($db->getPath())."'/>";
		echo $lang['db_rename']." '".htmlencode($db->getPath())."' ".$lang['to']." <input type='text' name='newname' style='width:200px;' value='".htmlencode($db->getPath())."'/> <input type='submit' value='".$lang['rename']."' name='rename' class='btn'/>";
		echo "</form>";	
	}
	else if($view=="delete")
	{
		//- Delete database confirmation (=delete)
		echo "<form action='?database_delete=1' method='post'>";
		echo "<div class='confirm'>";
		echo sprintf($lang['ques_del_db'],htmlencode($db->getPath()))."<br/><br/>";
		echo "<input name='database_delete' value='".htmlencode($db->getPath())."' type='hidden'/>";
		echo "<input type='submit' value='".$lang['confirm']."' class='btn'/> ";
		echo "<a href='".PAGE."'>".$lang['cancel']."</a>";
		echo "</div>";
		echo "</form>";	
	}

	echo "</div>";
}