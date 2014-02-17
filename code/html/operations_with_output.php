<?php
//- Switch on $_GET['action'] for operations with output
if(isset($_GET['action']) && !isset($_GET['confirm']))
{
	echo "<div id='main'>";
	switch($_GET['action'])
	{
	//- Table actions

		//- Create table (=table_create)
		case "table_create":
			$query = "SELECT name FROM sqlite_master WHERE type='table' AND name=".$db->quote($_POST['tablename']);
			$results = $db->selectArray($query);
			if(sizeof($results)>0)
				$exists = true;
			else
				$exists = false;
			echo "<h2>".$lang['create_tbl'].": '".htmlencode($_POST['tablename'])."'</h2>";
			if($_POST['tablefields']=="" || intval($_POST['tablefields'])<=0)
				echo $lang['specify_fields'];
			else if($_POST['tablename']=="")
				echo $lang['specify_tbl'];
			else if($exists)
				echo $lang['tbl_exists'];
			else
			{
				$num = intval($_POST['tablefields']);
				$name = $_POST['tablename'];
				echo "<form action='?action=table_create&amp;confirm=1' method='post'>";
				echo "<input type='hidden' name='tablename' value='".htmlencode($name)."'/>";
				echo "<input type='hidden' name='rows' value='".$num."'/>";
				echo "<table border='0' cellpadding='2' cellspacing='1' class='viewTable'>";
				echo "<tr>";
				$headings = array($lang['fld'], $lang['type'], $lang['prim_key']);
				if($db->getType() != "SQLiteDatabase") $headings[] = $lang['autoincrement'];
				$headings[] = $lang['not_null'];
				$headings[] = $lang['def_val'];
				for($k=0; $k<count($headings); $k++)
					echo "<td class='tdheader'>" . $headings[$k] . "</td>";
				echo "</tr>";

				for($i=0; $i<$num; $i++)
				{
					$tdWithClass = "<td class='td" . ($i%2 ? "1" : "2") . "'>";
					echo "<tr>";
					echo $tdWithClass;
					echo "<input type='text' name='".$i."_field' style='width:200px;'/>";
					echo "</td>";
					echo $tdWithClass;
					echo "<select name='".$i."_type' id='i".$i."_type' onchange='toggleAutoincrement(".$i.");'>";
					foreach ($sqlite_datatypes as $t) {
						echo "<option value='".htmlencode($t)."'>".htmlencode($t)."</option>";
					}
					echo "</select>";
					echo "</td>";
					echo $tdWithClass;
					echo "<label><input type='checkbox' name='".$i."_primarykey' id='i".$i."_primarykey' onclick='toggleNull(".$i."); toggleAutoincrement(".$i.");'/> ".$lang['yes']."</label>";
					echo "</td>";
					if($db->getType() != "SQLiteDatabase")
					{
						echo $tdWithClass;
						echo "<label><input type='checkbox' name='".$i."_autoincrement' id='i".$i."_autoincrement'/> ".$lang['yes']."</label>";
						echo "</td>";
					}
					echo $tdWithClass;
					echo "<label><input type='checkbox' name='".$i."_notnull' id='i".$i."_notnull'/> ".$lang['yes']."</label>";
					echo "</td>";
					echo $tdWithClass;
					echo "<select name='".$i."_defaultoption' id='i".$i."_defaultoption' onchange=\"if(this.value!='defined' && this.value!='expr') document.getElementById('i".$i."_defaultvalue').value='';\">";
					echo "<option value='none'>".$lang['none']."</option><option value='defined'>".$lang['as_defined'].":</option><option>NULL</option><option>CURRENT_TIME</option><option>CURRENT_DATE</option><option>CURRENT_TIMESTAMP</option><option value='expr'>".$lang['expression'].":</option>";
					echo "</select>";
					echo "<input type='text' name='".$i."_defaultvalue' id='i".$i."_defaultvalue' style='width:100px;' onchange=\"if(document.getElementById('i".$i."_defaultoption').value!='expr') document.getElementById('i".$i."_defaultoption').value='defined';\"/>";
					echo "</td>";
					echo "</tr>";
				}
				echo "<tr>";
				echo "<td class='tdheader' style='text-align:right;' colspan='6'>";
				echo "<input type='submit' value='".$lang['create']."' class='btn'/> ";
				echo "<a href='".PAGE."'>".$lang['cancel']."</a>";
				echo "</td>";
				echo "</tr>";
				echo "</table>";
				echo "</form>";
				if($db->getType() != "SQLiteDatabase") echo "<script type='text/javascript'>window.onload=initAutoincrement;</script>";
			}
			break;

		//- Perform SQL query on table (=table_sql)
		case "table_sql":
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
						if($result!==false)
						{
							if($isSelect)
							{
								$affected = sizeof($result);
								echo $lang['showing']." ".$affected." ".$lang['rows'].". ";
							}
							else
							{
								$affected = $db->getAffectedRows();
								echo $affected." ".$lang['rows']." ".$lang['affected'].". ";
							}
							printf($lang['query_time'], $queryTimer);
							echo "</b><br/>";
						}
						else
						{
							echo $lang['err'].": ".$db->getError().".</b><br/>";
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
				$queryStr = "SELECT * FROM ".$db->quote_id($target_table)." WHERE 1";
			}

			echo "<fieldset>";
			echo "<legend><b>".sprintf($lang['run_sql'],htmlencode($db->getName()))."</b></legend>";
			echo "<form action='?table=".urlencode($target_table)."&amp;action=table_sql' method='post'>";
			echo "<div style='float:left; width:70%;'>";
			echo "<textarea style='width:97%; height:300px;' name='queryval' id='queryval' cols='50' rows='8'>".htmlencode($queryStr)."</textarea>";
			echo "</div>";
			echo "<div style='float:left; width:28%; padding-left:10px;'>";
			echo $lang['fields']."<br/>";
			echo "<select multiple='multiple' style='width:100%;' id='fieldcontainer'>";
			$query = "PRAGMA table_info(".$db->quote_id($target_table).")";
			$result = $db->selectArray($query);
			for($i=0; $i<sizeof($result); $i++)
			{
				echo "<option value='".htmlencode($result[$i][1])."'>".htmlencode($result[$i][1])."</option>";
			}
			echo "</select>";
			echo "<input type='button' value='&lt;&lt;' onclick='moveFields();' class='btn'/>";
			echo "</div>";
			echo "<div style='clear:both;'></div>";
			echo $lang['delimit']." <input type='text' name='delimiter' value='".htmlencode($delimiter)."' style='width:50px;'/> ";
			echo "<input type='submit' name='query' value='".$lang['go']."' class='btn'/>";
			echo "</form>";
			echo "</fieldset>";
			break;

		//- Empty table (=table_empty)
		case "table_empty":
			echo "<form action='?action=table_empty&amp;confirm=1' method='post'>";
			echo "<input type='hidden' name='tablename' value='".htmlencode($target_table)."'/>";
			echo "<div class='confirm'>";
			echo sprintf($lang['ques_empty'], htmlencode($target_table))."<br/><br/>";
			echo "<input type='submit' value='".$lang['confirm']."' class='btn'/> ";
			echo "<a href='".PAGE."'>".$lang['cancel']."</a>";
			echo "</div>";
			break;

		//- Drop table (=table_drop)
		case "table_drop":
			echo "<form action='?action=table_drop&amp;confirm=1' method='post'>";
			echo "<input type='hidden' name='tablename' value='".htmlencode($target_table)."'/>";
			echo "<div class='confirm'>";
			echo sprintf($lang['ques_drop'], htmlencode($target_table))."<br/><br/>";
			echo "<input type='submit' value='".$lang['confirm']."' class='btn'/> ";
			echo "<a href='".PAGE."'>".$lang['cancel']."</a>";
			echo "</div>";
			break;

		//- Drop view (=view_drop)
		case "view_drop":
			echo "<form action='?action=view_drop&amp;confirm=1' method='post'>";
			echo "<input type='hidden' name='viewname' value='".htmlencode($target_table)."'/>";
			echo "<div class='confirm'>";
			echo sprintf($lang['ques_drop_view'], htmlencode($target_table))."<br/><br/>";
			echo "<input type='submit' value='".$lang['confirm']."' class='btn'/> ";
			echo "<a href='".PAGE."'>".$lang['cancel']."</a>";
			echo "</div>";
			break;

		//- Export table (=table_export)
		case "table_export":
			echo "<form method='post' action='".PAGE."'>";
			echo "<fieldset style='float:left; width:260px; margin-right:20px;'><legend><b>".$lang['export']."</b></legend>";
			echo "<input type='hidden' value='".htmlencode($target_table)."' name='single_table'/>";
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
			echo "<div style='clear:both;'></div>";
			echo "<div style='float:left;'>".$lang['fld_enclosed']."</div>";
			echo "<input type='text' value='\"' name='export_csv_fieldsenclosed' style='float:right;'/>";
			echo "<div style='clear:both;'></div>";
			echo "<div style='float:left;'>".$lang['fld_escaped']."</div>";
			echo "<input type='text' value='\' name='export_csv_fieldsescaped' style='float:right;'/>";
			echo "<div style='clear:both;'></div>";
			echo "<div style='float:left;'>".$lang['rep_null']."</div>";
			echo "<input type='text' value='NULL' name='export_csv_replacenull' style='float:right;'/>";
			echo "<div style='clear:both;'></div>";
			echo "<label><input type='checkbox' name='export_csv_crlf'/> ".$lang['rem_crlf']."</label><br/>";
			echo "<label><input type='checkbox' checked='checked' name='export_csv_fieldnames'/> ".$lang['put_fld']."</label>";
			echo "</fieldset>";
			
			echo "<div style='clear:both;'></div>";
			echo "<br/><br/>";
			echo "<fieldset><legend><b>".$lang['save_as']."</b></legend>";
			$file = pathinfo($db->getPath());
			$name = $file['filename'];
			echo "<input type='text' name='filename' value='".htmlencode($name)."_".htmlencode($target_table)."_".date("Y-m-d").".dump' style='width:400px;'/> <input type='submit' name='export' value='".$lang['export']."' class='btn'/>";
			echo "</fieldset>";
			echo "</form>";
			echo "<div class='confirm' style='margin-top: 2em'>".sprintf($lang['backup_hint'], "<a href='".htmlencode(str_replace(DIRECTORY_SEPARATOR,'/',$currentDB['path']))."' title='".$lang['backup']."'>".$lang["backup_hint_linktext"]."</a>")."</div>";
			break;

		//- Import table (=table_import)
		case "table_import":
			if(isset($_POST['import']))
			{
				echo "<div class='confirm'>";
				if($importSuccess===true)
					echo $lang['import_suc'];
				else
					echo $lang['err'].': '.$importSuccess;
				echo "</div><br/>";
			}
			echo "<form method='post' action='?table=".urlencode($target_table)."&amp;action=table_import' enctype='multipart/form-data'>";
			echo "<fieldset style='float:left; width:260px; margin-right:20px;'><legend><b>".$lang['import_into']." ".htmlencode($target_table)."</b></legend>";
			echo "<label><input type='radio' name='import_type' checked='checked' value='sql' onclick='toggleImports(\"sql\");'/> ".$lang['sql']."</label>";
			echo "<br/><label><input type='radio' name='import_type' value='csv' onclick='toggleImports(\"csv\");'/> ".$lang['csv']."</label>";
			echo "</fieldset>";
			
			echo "<fieldset style='float:left; max-width:350px;' id='importoptions_sql'><legend><b>".$lang['options']."</b></legend>";
			echo $lang['no_opt'];
			echo "</fieldset>";
			
			echo "<fieldset style='float:left; max-width:350px; display:none;' id='importoptions_csv'><legend><b>".$lang['options']."</b></legend>";
			echo "<input type='hidden' value='".htmlencode($target_table)."' name='single_table'/>";
			echo "<div style='float:left;'>".$lang['fld_terminated']."</div>";
			echo "<input type='text' value=';' name='import_csv_fieldsterminated' style='float:right;'/>";
			echo "<div style='clear:both;'>";
			echo "<div style='float:left;'>".$lang['fld_enclosed']."</div>";
			echo "<input type='text' value='\"' name='import_csv_fieldsenclosed' style='float:right;'/>";
			echo "<div style='clear:both;'>";
			echo "<div style='float:left;'>".$lang['fld_escaped']."</div>";
			echo "<input type='text' value='\' name='import_csv_fieldsescaped' style='float:right;'/>";
			echo "<div style='clear:both;'>";
			echo "<div style='float:left;'>".$lang['rep_null']."</div>";
			echo "<input type='text' value='NULL' name='import_csv_replacenull' style='float:right;'/>";
			echo "<div style='clear:both;'>";
			echo "<label><input type='checkbox' checked='checked' name='import_csv_fieldnames'/> ".$lang['fld_names']."</label>";
			echo "</fieldset>";
			
			echo "<div style='clear:both;'></div>";
			echo "<br/><br/>";
			
			echo "<fieldset><legend><b>".$lang['import_f']."</b></legend>";
			echo "<input type='file' value='".$lang['choose_f']."' name='file' style='background-color:transparent; border-style:none;'/> <input type='submit' value='".$lang['import']."' name='import' class='btn'/>";
			echo "</fieldset>";
			break;

		//- Rename table (=table_rename)
		case "table_rename":
			echo "<form action='?action=table_rename&amp;confirm=1' method='post'>";
			echo "<input type='hidden' name='oldname' value='".htmlencode($target_table)."'/>";
			printf($lang['rename_tbl'], htmlencode($target_table));
			echo " <input type='text' name='newname' style='width:200px;'/> <input type='submit' value='".$lang['rename']."' name='rename' class='btn'/>";
			echo "</form>";
			break;

		//- Search table (=table_search)
		case "table_search":
			$foundVal = array();
			$fieldArr = array();
			if(isset($_GET['done']))
			{
				$query = "PRAGMA table_info(".$db->quote_id($target_table).")";
				$result = $db->selectArray($query);
				$j = 0;
				$arr = array();
				for($i=0; $i<sizeof($result); $i++)
				{
					$field = $result[$i][1];
					$field_index = str_replace(" ","_",$field);
					$operator = $_POST[$field_index.":operator"];
					$value = $_POST[$field_index];
					if($value!="" || $operator=="!= ''" || $operator=="= ''")
					{
						if($operator=="= ''" || $operator=="!= ''")
							$arr[$j] = $db->quote_id($field)." ".$operator;
						
						else{
							if($operator == "LIKE%"){ 
								$operator = "LIKE";
								if(!preg_match('/(^%)|(%$)/', $value)) $value = '%'.$value.'%';
							}
							$fieldArr[] = $field;
							$foundVal[] = $value;
							$arr[$j] = $db->quote_id($field)." ".$operator." ".$db->quote($value);
						}
						$j++;
					}
				}
				$query = "SELECT * FROM ".$db->quote_id($target_table);
				$whereTo = '';
				if(sizeof($arr)>0)
				{
					$whereTo .= " WHERE ".$arr[0];
					for($i=1; $i<sizeof($arr); $i++)
					{
						$whereTo .= " AND ".$arr[$i];
					}
				}
				$query .= $whereTo;
				$queryTimer = new MicroTimer();
				$result = $db->selectArray($query,"assoc");
				$queryTimer->stop();

				echo "<div class='confirm'>";
				echo "<b>";
				if($result!==false)
				{
					$affected = sizeof($result);
					echo $lang['showing']." ".$affected." ".$lang['rows'].". ";
					printf($lang['query_time'], $queryTimer);
					echo "</b><br/>";
				}
				else
				{
					echo $lang['err'].": ".$db->getError().".</b><br/>".$lang['bug_report'].' '.PROJECT_BUGTRACKER_LINK.'<br/>';
				}
				echo "<span style='font-size:11px;'>".htmlencode($query)."</span>";
				echo "</div><br/>";

				if(sizeof($result)>0)
				{
					$headers = array_keys($result[0]);

					echo "<table border='0' cellpadding='2' cellspacing='1' class='viewTable'>";
					echo "<tr>";
					echo "<td>&nbsp;</td><td>&nbsp;</td>"; 
					for($j=0; $j<sizeof($headers); $j++)
					{
						echo "<td class='tdheader'>";
						echo htmlencode($headers[$j]);
						echo "</td>";
					}
					echo "</tr>";

					$pkid = getRowId($target_table, $whereTo);

					for($j=0; $j<sizeof($result); $j++)
					{
						$pk = $pkid[$j][0];
						$tdWithClass = "<td class='td".($j%2 ? "1" : "2")."'>";
						$cVal = 0;
						echo "<tr>";
						echo $tdWithClass."<a href='?table=".urlencode($target_table)."&amp;action=row_editordelete&amp;pk=".urlencode($pk)."&amp;type=edit' title='".$lang['edit']."' class='edit'><span>".$lang['edit']."</span></a></td>"; 
						echo $tdWithClass."<a href='?table=".urlencode($target_table)."&amp;action=row_editordelete&amp;pk=".urlencode($pk)."&amp;type=delete' title='".$lang['del']."' class='delete'><span>".$lang['del']."</span></a></td>";
						for($z=0; $z<sizeof($headers); $z++)
						{
							echo $tdWithClass;
							$fldResult = $result[$j][$headers[$z]];
							if(!empty($foundVal) and in_array($headers[$z], $fieldArr)){
								$foundVal = str_replace('%', '', $foundVal);
								$fldResult = str_ireplace($foundVal[$cVal], '[fnd]'.$foundVal[$cVal].'[/fnd]', $fldResult);
								$cVal++;
							}
							echo str_replace(array('[fnd]', '[/fnd]'), array('<u class="found">', '</u>'), htmlencode($fldResult));
							echo "</td>";
						}
						echo "</tr>";
					}
					echo "</table><br/><br/>";
				}
				
				echo "<a href='?table=".urlencode($target_table)."&amp;action=table_search'>".$lang['srch_again']."</a>";
			}
			else
			{
				$query = "PRAGMA table_info(".$db->quote_id($target_table).")";
				$result = $db->selectArray($query);
				
				echo "<form action='?table=".urlencode($target_table)."&amp;action=table_search&amp;done=1' method='post'>";
					
				echo "<table border='0' cellpadding='2' cellspacing='1' class='viewTable'>";
				echo "<tr>";
				echo "<td class='tdheader'>".$lang['fld']."</td>";
				echo "<td class='tdheader'>".$lang['type']."</td>";
				echo "<td class='tdheader'>".$lang['operator']."</td>";
				echo "<td class='tdheader'>".$lang['val']."</td>";
				echo "</tr>";

				for($i=0; $i<sizeof($result); $i++)
				{
					$field = $result[$i][1];
					$type = $result[$i]['type'];
					$typeAffinity = get_type_affinity($type);
					$tdWithClass = "<td class='td".($i%2 ? "1" : "2")."'>";
					$tdWithClassLeft = "<td class='td".($i%2 ? "1" : "2")."' style='text-align:left;'>";
					echo "<tr>";
					echo $tdWithClassLeft;
					echo htmlencode($field);
					echo "</td>";
					echo $tdWithClassLeft;
					echo htmlencode($type);
					echo "</td>";
					echo $tdWithClassLeft;
					echo "<select name='".htmlencode($field).":operator' onchange='checkLike(\"".htmlencode($field)."_search\", this.options[this.selectedIndex].value); '>";
					echo "<option value='='>=</option>";
					if($typeAffinity=="INTEGER" || $typeAffinity=="REAL" || $typeAffinity=="NUMERIC")
					{
						echo "<option value='&gt;'>&gt;</option>";
						echo "<option value='&gt;='>&gt;=</option>";
						echo "<option value='&lt;'>&lt;</option>";
						echo "<option value='&lt;='>&lt;=</option>";
					}
					else if($typeAffinity=="TEXT" || $typeAffinity=="NONE")
					{
						echo "<option value='= '''>= ''</option>";
						echo "<option value='!= '''>!= ''</option>";
					}
					echo "<option value='!='>!=</option>";
					if($typeAffinity=="TEXT" || $typeAffinity=="NONE")
						echo "<option value='LIKE' selected='selected'>LIKE</option>";
					else
						echo "<option value='LIKE'>LIKE</option>";
					echo "<option value='LIKE%'>LIKE %...%</option>";
					echo "<option value='NOT LIKE'>NOT LIKE</option>";
					echo "</select>";
					echo "</td>";
					echo $tdWithClassLeft;
					if($typeAffinity=="INTEGER" || $typeAffinity=="REAL" || $typeAffinity=="NUMERIC")
						echo "<input type='text' id='".htmlencode($field)."_search' name='".htmlencode($field)."'/>";
					else
						echo "<textarea id='".htmlencode($field)."_search' name='".htmlencode($field)."' rows='1' cols='60'></textarea>";
					echo "</td>";
					echo "</tr>";
				}
				echo "<tr>";
				echo "<td class='tdheader' style='text-align:right;' colspan='4'>";
				echo "<input type='submit' value='".$lang['srch']."' class='btn'/>";
				echo "</td>";
				echo "</tr>";
				echo "</table>";
				echo "</form>";
			}
			break;

	//- Row actions

		//- View row (=row_view)
		case "row_view":
			if(!isset($_POST['startRow']))
				$_POST['startRow'] = 0;

			if(isset($_POST['numRows']))
				$_SESSION[COOKIENAME.'numRows'] = $_POST['numRows'];

			if(!isset($_SESSION[COOKIENAME.'numRows']))
				$_SESSION[COOKIENAME.'numRows'] = $rowsNum;
			
			if(isset($_SESSION[COOKIENAME.'currentTable']) && $_SESSION[COOKIENAME.'currentTable']!=$target_table)
			{
				unset($_SESSION[COOKIENAME.'sortRows']);
				unset($_SESSION[COOKIENAME.'orderRows']);	
			}
			if(isset($_POST['viewtype']))
			{
				$_SESSION[COOKIENAME.'viewtype'] = $_POST['viewtype'];	
			}
			
			$rowCount = $db->numRows($target_table);
			$lastPage = intval($rowCount / $_SESSION[COOKIENAME.'numRows']);
			$remainder = intval($rowCount % $_SESSION[COOKIENAME.'numRows']);
			if($remainder==0)
				$remainder = $_SESSION[COOKIENAME.'numRows'];
			
			//- HTML: pagination buttons
			echo "<div style=''>";
			//previous button
			if($_POST['startRow']>0)
			{
				echo "<div style='float:left;'>";
				echo "<form action='?action=row_view&amp;table=".urlencode($target_table)."' method='post'>";
				echo "<input type='hidden' name='startRow' value='0'/>";
				echo "<input type='hidden' name='numRows' value='".$_SESSION[COOKIENAME.'numRows']."'/> ";
				echo "<input type='submit' value='&larr;&larr;' name='previous' class='btn'/> ";
				echo "</form>";
				echo "</div>";
				echo "<div style='float:left; overflow:hidden; margin-right:20px;'>";
				echo "<form action='?action=row_view&amp;table=".urlencode($target_table)."' method='post'>";
				echo "<input type='hidden' name='startRow' value='".intval($_POST['startRow']-$_SESSION[COOKIENAME.'numRows'])."'/>";
				echo "<input type='hidden' name='numRows' value='".$_SESSION[COOKIENAME.'numRows']."'/> ";
				echo "<input type='submit' value='&larr;' name='previous_full' class='btn'/> ";
				echo "</form>";
				echo "</div>";
			}
			
			//show certain number buttons
			echo "<div style='float:left;'>";
			echo "<form action='?action=row_view&amp;table=".urlencode($target_table)."' method='post'>";
			echo "<input type='submit' value='".$lang['show']." : ' name='show' class='btn'/> ";
			echo "<input type='text' name='numRows' style='width:50px;' value='".$_SESSION[COOKIENAME.'numRows']."'/> ";
			echo $lang['rows_records'];

			if(intval($_POST['startRow']+$_SESSION[COOKIENAME.'numRows']) < $rowCount)
				echo "<input type='text' name='startRow' style='width:90px;' value='".intval($_POST['startRow']+$_SESSION[COOKIENAME.'numRows'])."'/>";
			else
				echo "<input type='text' name='startRow' style='width:90px;' value='0'/> ";
			echo $lang['as_a'];
			echo " <select name='viewtype'>";
			if(!isset($_SESSION[COOKIENAME.'viewtype']) || $_SESSION[COOKIENAME.'viewtype']=="table")
			{
				echo "<option value='table' selected='selected'>".$lang['tbl']."</option>";
				echo "<option value='chart'>".$lang['chart']."</option>";
			}
			else
			{
				echo "<option value='table'>".$lang['tbl']."</option>";
				echo "<option value='chart' selected='selected'>".$lang['chart']."</option>";
			}
			echo "</select>";
			echo "</form>";
			echo "</div>";
			
			//next button
			if(intval($_POST['startRow']+$_SESSION[COOKIENAME.'numRows'])<$rowCount)
			{
				echo "<div style='float:left; margin-left:20px; '>";
				echo "<form action='?action=row_view&amp;table=".urlencode($target_table)."' method='post'>";
				echo "<input type='hidden' name='startRow' value='".intval($_POST['startRow']+$_SESSION[COOKIENAME.'numRows'])."'/>";
				echo "<input type='hidden' name='numRows' value='".$_SESSION[COOKIENAME.'numRows']."'/> ";
				echo "<input type='submit' value='&rarr;' name='next' class='btn'/> ";
				echo "</form>";
				echo "</div>";
				echo "<div style='float:left; '>";
				echo "<form action='?action=row_view&amp;table=".urlencode($target_table)."' method='post'>";
				echo "<input type='hidden' name='startRow' value='".intval($rowCount-$remainder)."'/>";
				echo "<input type='hidden' name='numRows' value='".$_SESSION[COOKIENAME.'numRows']."'/> ";
				echo "<input type='submit' value='&rarr;&rarr;' name='next_full' class='btn'/> ";
				echo "</form>";
				echo "</div>";
			}
			echo "<div style='clear:both;'></div>";
			echo "</div>";

			//- Query execution
			if(!isset($_GET['sort']))
				$_GET['sort'] = NULL;
			if(!isset($_GET['order']))
				$_GET['order'] = NULL;

			$numRows = $_SESSION[COOKIENAME.'numRows'];
			$startRow = $_POST['startRow'];
			if(isset($_GET['sort']))
			{
				$_SESSION[COOKIENAME.'sortRows'] = $_GET['sort'];
				$_SESSION[COOKIENAME.'currentTable'] = $target_table;
			}
			if(isset($_GET['order']))
			{
				$_SESSION[COOKIENAME.'orderRows'] = $_GET['order'];
				$_SESSION[COOKIENAME.'currentTable'] = $target_table;
			}
			$_SESSION[COOKIENAME.'numRows'] = $numRows;
			$query = "SELECT *, ROWID FROM ".$db->quote_id($target_table);
			$queryDisp = "SELECT * FROM ".$db->quote_id($target_table);
			$queryAdd = "";
			if(isset($_SESSION[COOKIENAME.'sortRows']))
				$queryAdd .= " ORDER BY ".$db->quote_id($_SESSION[COOKIENAME.'sortRows']);
			if(isset($_SESSION[COOKIENAME.'orderRows']))
				$queryAdd .= " ".$_SESSION[COOKIENAME.'orderRows'];
			$queryAdd .= " LIMIT ".$startRow.", ".$numRows;
			$query .= $queryAdd;
			$queryDisp .= $queryAdd;
			$queryTimer = new MicroTimer();
			$arr = $db->selectArray($query);
			$queryTimer->stop();

			//- Show results
			if(sizeof($arr)>0)
			{
				echo "<br/><div class='confirm'>";
				echo "<b>".$lang['showing_rows']." ".$startRow." - ".($startRow + sizeof($arr)-1).", ".$lang['total'].": ".$rowCount." ";
				printf($lang['query_time'], $queryTimer);
				echo "</b><br/>";
				echo "<span style='font-size:11px;'>".htmlencode($queryDisp)."</span>";
				echo "</div><br/>";
				
				if($target_table_type == 'view')
				{
					echo sprintf($lang['readonly_tbl'], htmlencode($target_table))." <a href='http://en.wikipedia.org/wiki/View_(database)' target='_blank'>http://en.wikipedia.org/wiki/View_(database)</a>"; 
					echo "<br/><br/>";	
				}
				
				$query = "PRAGMA table_info(".$db->quote_id($target_table).")";
				$result = $db->selectArray($query);
				$rowidColumn = sizeof($result);

				//- Table view				
				if(!isset($_SESSION[COOKIENAME.'viewtype']) || $_SESSION[COOKIENAME.'viewtype']=="table")
				{
					echo "<form action='?action=row_editordelete&amp;table=".urlencode($target_table)."' method='post' name='checkForm'>";
					echo "<table border='0' cellpadding='2' cellspacing='1' class='viewTable'>";
					echo "<tr>";
					if($target_table_type == 'table')
						echo "<td colspan='3'></td>";

					for($i=0; $i<sizeof($result); $i++)
					{
						echo "<td class='tdheader'>";
						echo "<a href='?action=row_view&amp;table=".urlencode($target_table)."&amp;sort=".urlencode($result[$i]['name']);
						if(isset($_SESSION[COOKIENAME.'sortRows']))
							$orderTag = ($_SESSION[COOKIENAME.'sortRows']==$result[$i]['name'] && $_SESSION[COOKIENAME.'orderRows']=="ASC") ? "DESC" : "ASC";
						else
							$orderTag = "ASC";
						echo "&amp;order=".$orderTag;
						echo "'>".htmlencode($result[$i]['name'])."</a>";
						if(isset($_SESSION[COOKIENAME.'sortRows']) && $_SESSION[COOKIENAME.'sortRows']==$result[$i]['name'])
							echo (($_SESSION[COOKIENAME.'orderRows']=="ASC") ? " <b>&uarr;</b>" : " <b>&darr;</b>");
						echo "</td>";
					}
					echo "</tr>";

					for($i=0; $i<sizeof($arr); $i++)
					{
						// -g-> $pk will always be the last column in each row of the array because we are doing a "SELECT *, ROWID FROM ..."
						$pk = $arr[$i][$rowidColumn];
						$tdWithClass = "<td class='td".($i%2 ? "1" : "2")."'>";
						$tdWithClassLeft = "<td class='td".($i%2 ? "1" : "2")."' style='text-align:left;'>";
						echo "<tr>";
						if($target_table_type == 'table')
						{
							echo $tdWithClass;
							echo "<input type='checkbox' name='check[]' value='".htmlencode($pk)."' id='check_".htmlencode($i)."'/>";
							echo "</td>";
							echo $tdWithClass;
							// -g-> Here, we need to put the ROWID in as the link for both the edit and delete.
							echo "<a href='?table=".urlencode($target_table)."&amp;action=row_editordelete&amp;pk=".urlencode($pk)."&amp;type=edit' title='".$lang['edit']."' class='edit'><span>".$lang['edit']."</span></a>";
							echo "</td>";
							echo $tdWithClass;
							echo "<a href='?table=".urlencode($target_table)."&amp;action=row_editordelete&amp;pk=".urlencode($pk)."&amp;type=delete' title='".$lang['del']."' class='delete'><span>".$lang['del']."</span></a>";
							echo "</td>";
						}
						for($j=0; $j<sizeof($result); $j++)
						{
							$typeAffinity = get_type_affinity($result[$j]['type']);
							if($typeAffinity=="INTEGER" || $typeAffinity=="REAL" || $typeAffinity=="NUMERIC")
								echo $tdWithClass;
							else
								echo $tdWithClassLeft;
							if($arr[$i][$j]==="")
								echo "&nbsp;";
							elseif($arr[$i][$j]===NULL)
								echo "<i class='null'>NULL</i>";
							else
								echo subString(htmlencode($arr[$i][$j]));
							echo "</td>";
						}
						echo "</tr>";
					}
					echo "</table>";
					if($target_table_type == 'table')
					{
						echo "<a onclick='checkAll()'>".$lang['chk_all']."</a> / <a onclick='uncheckAll()'>".$lang['unchk_all']."</a> <i>".$lang['with_sel'].":</i> ";
						echo "<select name='type'>";
						echo "<option value='edit'>".$lang['edit']."</option>";
						echo "<option value='delete'>".$lang['del']."</option>";
						echo "</select> ";
						echo "<input type='submit' value='".$lang['go']."' name='massGo' class='btn'/>";
					}
					echo "</form>";
				}
				else
				//- Chart view
				{
					if(!isset($_SESSION[COOKIENAME.$target_table.'chartlabels']))
					{
						// No label-column set. Try to pick a text-column as label-column.
						for($i=0; $i<sizeof($result); $i++)
						{
							if(get_type_affinity($result[$i]['type'])=='TEXT')
							{
								$_SESSION[COOKIENAME.$target_table.'chartlabels'] = $i;
								break;
							}
						}
					}
					if(!isset($_SESSION[COOKIENAME.'chartlabels']))
						// no text column found, use the first column
						$_SESSION[COOKIENAME.'chartlabels'] = 0;
						
					if(!isset($_SESSION[COOKIENAME.$target_table.'chartvalues']))
					{
						// No value-column set. Pick the first numeric column if possible.
						// If not possible, pick the first column that is not the label-column.
						
						$potential_value_column = null;
						for($i=0; $i<sizeof($result); $i++)
						{
							if($potential_value_column===null && $i != $_SESSION[COOKIENAME.$target_table.'chartlabels'])
								// the first column (of any type) that is not the label-column
								$potential_value_column = $i;
							// check if the col is numeric
							$typeAffinity = get_type_affinity($result[$i]['type']);  
							if($typeAffinity=='INTEGER' || $typeAffinity=='REAL' || $typeAffinity=='NUMERIC')
							{
								// this is defined as a numeric column, so prefer this as a value column over $potential_value_column
								$_SESSION[COOKIENAME.$target_table.'chartvalues'] = $i;
								break;
							}
						}
						if(!isset($_SESSION[COOKIENAME.$target_table.'chartvalues']))
						{
							// we did not find a numeric column
							if($potential_value_column!==null)
								// use the $potential_value_column, i.e. the second column which is not the label-column
								$_SESSION[COOKIENAME.$target_table.'chartvalues'] = $potential_value_column;
							else
								// it's hopeless, there is only 1 column
								$_SESSION[COOKIENAME.$target_table.'chartvalues'] = 0;  
						}
					}
					
					if(!isset($_SESSION[COOKIENAME.'charttype']))
						$_SESSION[COOKIENAME.'charttype'] = 'bar';
						
					if(isset($_POST['chartsettings']))
					{
						$_SESSION[COOKIENAME.'charttype'] = $_POST['charttype'];	
						$_SESSION[COOKIENAME.$target_table.'chartlabels'] = $_POST['chartlabels'];
						$_SESSION[COOKIENAME.$target_table.'chartvalues'] = $_POST['chartvalues'];
					}
					//- Chart javascript code
					?>
					<script type='text/javascript' src='https://www.google.com/jsapi'></script>
					<script type='text/javascript'>
					google.load('visualization', '1.0', {'packages':['corechart']});
					google.setOnLoadCallback(drawChart);
					function drawChart()
					{
						var data = new google.visualization.DataTable();
						data.addColumn('string', '<?php echo $result[$_SESSION[COOKIENAME.$target_table.'chartlabels']]['name']; ?>');
						data.addColumn('number', '<?php echo $result[$_SESSION[COOKIENAME.$target_table.'chartvalues']]['name']; ?>');
						data.addRows([
						<?php
						for($i=0; $i<sizeof($arr); $i++)
						{
							$label = str_replace("'", "", htmlencode($arr[$i][$_SESSION[COOKIENAME.$target_table.'chartlabels']]));
							$value = htmlencode($arr[$i][$_SESSION[COOKIENAME.$target_table.'chartvalues']]);
							
							if($value==NULL || $value=="")
								$value = 0;
								
							echo "['".$label."', ".$value."]";
							if($i<sizeof($arr)-1)
								echo ",";
						}
						$height = (sizeof($arr)+1) * 30;
						if($height>1000)
							$height = 1000;
						else if($height<300)
							$height = 300;
						if($_SESSION[COOKIENAME.'charttype']=="pie")
							$height = 800;
						?>
						]);
						var chartWidth = document.getElementById("main_column").offsetWidth - document.getElementById("chartsettingsbox").offsetWidth - 100;
						if(chartWidth>1000)
							chartWidth = 1000;
							
						var options = 
						{
							'width':chartWidth,
							'height':<?php echo $height; ?>,
							'title':'<?php echo $result[$_SESSION[COOKIENAME.$target_table.'chartlabels']]['name']." vs ".$result[$_SESSION[COOKIENAME.$target_table.'chartvalues']]['name']; ?>'
						};
						<?php
						if($_SESSION[COOKIENAME.'charttype']=="bar")
							echo "var chart = new google.visualization.BarChart(document.getElementById('chart_div'));";
						else if($_SESSION[COOKIENAME.'charttype']=="pie")
							echo "var chart = new google.visualization.PieChart(document.getElementById('chart_div'));";
						else
							echo "var chart = new google.visualization.LineChart(document.getElementById('chart_div'));";
						?>
						chart.draw(data, options);
					}
					</script>
					<div id="chart_div" style="float:left;"><?php echo $lang['no_chart']; ?></div>
					<?php
					echo "<fieldset style='float:right; text-align:center;' id='chartsettingsbox'><legend><b>Chart Settings</b></legend>";
					echo "<form action='?action=row_view&amp;table=".urlencode($target_table)."' method='post'>";
					echo $lang['chart_type'].": <select name='charttype'>";
					echo "<option value='bar'";
					if($_SESSION[COOKIENAME.'charttype']=="bar")
						echo " selected='selected'";
					echo ">".$lang['chart_bar']."</option>";
					echo "<option value='pie'";
					if($_SESSION[COOKIENAME.'charttype']=="pie")
						echo " selected='selected'";
					echo ">".$lang['chart_pie']."</option>";
					echo "<option value='line'";
					if($_SESSION[COOKIENAME.'charttype']=="line")
						echo " selected='selected'";
					echo ">".$lang['chart_line']."</option>";
					echo "</select>";
					echo "<br/><br/>";
					echo $lang['lbl'].": <select name='chartlabels'>";
					for($i=0; $i<sizeof($result); $i++)
					{
						if(isset($_SESSION[COOKIENAME.$target_table.'chartlabels']) && $_SESSION[COOKIENAME.$target_table.'chartlabels']==$i)
							echo "<option value='".$i."' selected='selected'>".htmlencode($result[$i]['name'])."</option>";
						else
							echo "<option value='".$i."'>".htmlencode($result[$i]['name'])."</option>";
					}
					echo "</select>";
					echo "<br/><br/>";
					echo $lang['val'].": <select name='chartvalues'>";
					for($i=0; $i<sizeof($result); $i++)
					{
						if(isset($_SESSION[COOKIENAME.$target_table.'chartvalues']) && $_SESSION[COOKIENAME.$target_table.'chartvalues']==$i)
							echo "<option value='".$i."' selected='selected'>".htmlencode($result[$i]['name'])."</option>";
						else
							echo "<option value='".$i."'>".htmlencode($result[$i]['name'])."</option>";
					}
					echo "</select>";
					echo "<br/><br/>";
					echo "<input type='submit' name='chartsettings' value='".$lang['update']."' class='btn'/>";
					echo "</form>";
					echo "</fieldset>";
					echo "<div style='clear:both;'></div>";
					//end chart view
				}
			}
			else if($rowCount>0)//no rows - do nothing
			{
				echo "<br/><br/>".$lang['no_rows'];
			}
			elseif($target_table_type == 'table')
			{
				echo "<br/><br/>".$lang['empty_tbl']." <a href='?table=".urlencode($target_table)."&amp;action=row_create'>".$lang['click']."</a> ".$lang['insert_rows'];
			}

			break;

		//- Create new row (=row_create)
		case "row_create":
			$fieldStr = "";
			echo "<form action='?table=".urlencode($target_table)."&amp;action=row_create' method='post'>";
			echo $lang['restart_insert'];
			echo " <select name='num'>";
			for($i=1; $i<=40; $i++)
			{
				if(isset($_POST['num']) && $_POST['num']==$i)
					echo "<option value='".$i."' selected='selected'>".$i."</option>";
				else
					echo "<option value='".$i."'>".$i."</option>";
			}
			echo "</select> ";
			echo $lang['rows'];
			echo " <input type='submit' value='".$lang['go']."' class='btn'/>";
			echo "</form>";
			echo "<br/>";
			$query = "PRAGMA table_info(".$db->quote_id($target_table).")";
			$result = $db->selectArray($query);
			echo "<form action='?table=".urlencode($target_table)."&amp;action=row_create&amp;confirm=1' method='post'>";
			if(isset($_POST['num']))
				$num = $_POST['num'];
			else
				$num = 1;
			echo "<input type='hidden' name='numRows' value='".$num."'/>";
			for($j=0; $j<$num; $j++)
			{
				if($j>0)
					echo "<label><input type='checkbox' value='ignore' name='".$j.":ignore' id='row_".$j."_ignore' checked='checked'/> ".$lang['ignore']."</label><br/>";
				echo "<table border='0' cellpadding='2' cellspacing='1' class='viewTable'>";
				echo "<tr>";
				echo "<td class='tdheader'>".$lang['fld']."</td>";
				echo "<td class='tdheader'>".$lang['type']."</td>";
				echo "<td class='tdheader'>".$lang['func']."</td>";
				echo "<td class='tdheader'>Null</td>";
				echo "<td class='tdheader'>".$lang['val']."</td>";
				echo "</tr>";

				for($i=0; $i<sizeof($result); $i++)
				{
					$field = $result[$i]['name'];
					$field_html = htmlencode($field);
					if($j==0)
						$fieldStr .= ":".$field;
					$type = strtolower($result[$i]['type']);
					$typeAffinity = get_type_affinity($type);
					$tdWithClass = "<td class='td".($i%2 ? "1" : "2")."'>";
					$tdWithClassLeft = "<td class='td".($i%2 ? "1" : "2")."' style='text-align:left;'>";
					echo "<tr>";
					echo $tdWithClassLeft;
					echo $field_html;
					echo "</td>";
					echo $tdWithClassLeft;
					echo htmlencode($type);
					echo "</td>";
					echo $tdWithClassLeft;
					echo "<select name='function_".$j."_".$field_html."' onchange='notNull(\"row_".$j."_field_".$i."_null\");'>";
					echo "<option value=''>&nbsp;</option>";
					foreach (array_merge($sqlite_functions, $custom_functions) as $f) {
						echo "<option value='".htmlencode($f)."'>".htmlencode($f)."</option>";
					}
					echo "</select>";
					echo "</td>";
					//we need to have a column dedicated to nulls -di
					echo $tdWithClassLeft;
					if($result[$i]['notnull']==0)
					{
						if($result[$i]['dflt_value']==="NULL")
							echo "<input type='checkbox' name='".$j.":".$field_html."_null' id='row_".$j."_field_".$i."_null' checked='checked' onclick='disableText(this, \"row_".$j."_field_".$i."_value\");'/>";
						else
							echo "<input type='checkbox' name='".$j.":".$field_html."_null' id='row_".$j."_field_".$i."_null' onclick='disableText(this, \"row_".$j."_field_".$i."_value\");'/>";
					}
					echo "</td>";
					echo $tdWithClassLeft;
					if($result[$i]['dflt_value'] === "NULL")
						$dflt_value = "";
					else
						$dflt_value = htmlencode(deQuoteSQL($result[$i]['dflt_value']));
					
					if($typeAffinity=="INTEGER" || $typeAffinity=="REAL" || $typeAffinity=="NUMERIC")
						echo "<input type='text' id='row_".$j."_field_".$i."_value' name='".$j.":".$field_html."' value='".$dflt_value."' onblur='changeIgnore(this, \"row_".$j."_ignore\");' onclick='notNull(\"row_".$j."_field_".$i."_null\");'/>";
					else
						echo "<textarea id='row_".$j."_field_".$i."_value' name='".$j.":".$field_html."' rows='5' cols='60' onclick='notNull(\"row_".$j."_field_".$i."_null\");' onblur='changeIgnore(this, \"row_".$j."_ignore\");'>".$dflt_value."</textarea>";
				echo "</td>";
				echo "</tr>";
				}
				echo "<tr>";
				echo "<td class='tdheader' style='text-align:right;' colspan='5'>";
				echo "<input type='submit' value='".$lang['insert']."' class='btn'/>";
				echo "</td>";
				echo "</tr>";
				echo "</table><br/>";
			}
			$fieldStr = substr($fieldStr, 1);
			echo "<input type='hidden' name='fields' value='".htmlencode($fieldStr)."'/>";
			echo "</form>";
			break;

		//- Edit or delete row (=row_editordelete)
		case "row_editordelete":
			if(isset($_POST['check']))
				$pks = $_POST['check'];
			else if(isset($_GET['pk']))
				$pks = array($_GET['pk']);
			else $pks[0] = "";
			$str = $pks[0];
			$pkVal = $pks[0];
			for($i=1; $i<sizeof($pks); $i++)
			{
				$str .= ", ".$pks[$i];
				$pkVal .= ":".$pks[$i];
			}
			if($str=="") //nothing was selected so show an error
			{
				echo "<div class='confirm'>";
				echo $lang['err'].": ".$lang['no_sel'];
				echo "</div>";
				echo "<br/><br/><a href='?table=".urlencode($target_table)."&amp;action=row_view'>".$lang['return']."</a>";
			}
			else
			{
				if((isset($_POST['type']) && $_POST['type']=="edit") || (isset($_GET['type']) && $_GET['type']=="edit")) //edit
				{
					echo "<form action='?table=".urlencode($target_table)."&amp;action=row_edit&amp;confirm=1&amp;pk=".urlencode($pkVal)."' method='post'>";
					$query = "PRAGMA table_info(".$db->quote_id($target_table).")";
					$result = $db->selectArray($query);

					//build the POST array of fields
					$fieldStr = $result[0][1];
					for($j=1; $j<sizeof($result); $j++)
						$fieldStr .= ":".$result[$j][1];

					echo "<input type='hidden' name='fieldArray' value='".htmlencode($fieldStr)."'/>";

					for($j=0; $j<sizeof($pks); $j++)
					{
						if(!is_numeric($pks[$j])) continue;
						$query = "SELECT * FROM ".$db->quote_id($target_table)." WHERE ROWID = ".$pks[$j];
						$result1 = $db->select($query);

						echo "<table border='0' cellpadding='2' cellspacing='1' class='viewTable'>";
						echo "<tr>";
						echo "<td class='tdheader'>".$lang['fld']."</td>";
						echo "<td class='tdheader'>".$lang['type']."</td>";
						echo "<td class='tdheader'>".$lang['func']."</td>";
						echo "<td class='tdheader'>Null</td>";
						echo "<td class='tdheader'>".$lang['val']."</td>";
						echo "</tr>";

						for($i=0; $i<sizeof($result); $i++)
						{
							$field = $result[$i][1];
							$type = $result[$i]['type'];
							$typeAffinity = get_type_affinity($type);
							$value = $result1[$i];
							$tdWithClass = "<td class='td".($i%2 ? "1" : "2")."'>";
							$tdWithClassLeft = "<td class='td".($i%2 ? "1" : "2")."' style='text-align:left;'>";
							echo "<tr>";
							echo $tdWithClass;
							echo htmlencode($field);
							echo "</td>";
							echo $tdWithClass;
							echo htmlencode($type);
							echo "</td>";
							echo $tdWithClassLeft;
							echo "<select name='function_".htmlencode($pks[$j])."_".htmlencode($field)."' onchange='notNull(\"".htmlencode($pks[$j]).":".htmlencode($field)."_null\");'>";
							echo "<option value=''></option>";
							foreach (array_merge($sqlite_functions, $custom_functions) as $f) {
								echo "<option value='".htmlencode($f)."'>".htmlencode($f)."</option>";
							}
							echo "</select>";
							echo "</td>";
							echo $tdWithClassLeft;
							if($result[$i][3]==0)
							{
								if($value===NULL)
									echo "<input type='checkbox' name='".htmlencode($pks[$j]).":".htmlencode($field)."_null' id='".htmlencode($pks[$j]).":".htmlencode($field)."_null' checked='checked'/>";
								else
									echo "<input type='checkbox' name='".htmlencode($pks[$j]).":".htmlencode($field)."_null' id='".htmlencode($pks[$j]).":".htmlencode($field)."_null'/>";
							}
							echo "</td>";
							echo $tdWithClassLeft;
							if($typeAffinity=="INTEGER" || $typeAffinity=="REAL" || $typeAffinity=="NUMERIC")
								echo "<input type='text' name='".htmlencode($pks[$j]).":".htmlencode($field)."' value='".htmlencode($value)."' onblur='changeIgnore(this, \"".$j."\", \"".htmlencode($pks[$j]).":".htmlencode($field)."_null\")' />";
							else
								echo "<textarea name='".htmlencode($pks[$j]).":".htmlencode($field)."' rows='1' cols='60' class='".htmlencode($field)."_textarea' onblur='changeIgnore(this, \"".$j."\", \"".htmlencode($pks[$j]).":".htmlencode($field)."_null\")'>".htmlencode($value)."</textarea>";
							echo "</td>";
							echo "</tr>";
						}
						echo "<tr>";
						echo "<td class='tdheader' style='text-align:right;' colspan='5'>";
						// Note: the 'Save changes' button must be first in the code so it is the one used when submitting the form with the Enter key (issue #215)
						echo "<input type='submit' value='".$lang['save_ch']."' class='btn'/> ";
						echo "<input type='submit' name='new_row' value='".$lang['new_insert']."' class='btn'/> ";
						echo "<a href='?table=".urlencode($target_table)."&amp;action=row_view'>".$lang['cancel']."</a>";
						echo "</td>";
						echo "</tr>";
						echo "</table>";
						echo "<br/>";
					}
					echo "</form>";
				}
				else //delete
				{
					echo "<form action='?table=".urlencode($target_table)."&amp;action=row_delete&amp;confirm=1&amp;pk=".urlencode($pkVal)."' method='post'>";
					echo "<div class='confirm'>";
					printf($lang['ques_del_rows'], htmlencode($str), htmlencode($target_table));
					echo "<br/><br/>";
					echo "<input type='submit' value='".$lang['confirm']."' class='btn'/> ";
					echo "<a href='?table=".urlencode($target_table)."&amp;action=row_view'>".$lang['cancel']."</a>";
					echo "</div>";
				}
			}
			break;

	//- Column actions

		//- View table structure (=column_view)
		case "column_view":
			$query = "PRAGMA table_info(".$db->quote_id($target_table).")";
			$result = $db->selectArray($query);

			echo "<form action='?table=".urlencode($target_table)."&amp;action=column_confirm' method='post' name='checkForm'>";
			echo "<table border='0' cellpadding='2' cellspacing='1' class='viewTable'>";
			echo "<tr>";
			if($target_table_type == 'table')
				echo "<td colspan='3'></td>";
			echo "<td class='tdheader'>".$lang['col']." #</td>";
			echo "<td class='tdheader'>".$lang['fld']."</td>";
			echo "<td class='tdheader'>".$lang['type']."</td>";
			echo "<td class='tdheader'>Not Null</td>";
			echo "<td class='tdheader'>".$lang['def_val']."</td>";
			echo "<td class='tdheader'>".$lang['prim_key']."</td>";
			echo "</tr>";

			$noPrimaryKey = true;
			
			for($i=0; $i<sizeof($result); $i++)
			{
				$colVal = $result[$i][0];
				$fieldVal = $result[$i][1];
				$typeVal = $result[$i]['type'];
				$notnullVal = $result[$i][3];
				$defaultVal = $result[$i][4];
				$primarykeyVal = $result[$i][5];

				if(intval($notnullVal)!=0)
					$notnullVal = $lang['yes'];
				else
					$notnullVal = $lang['no'];
				if(intval($primarykeyVal)!=0)
				{
					$primarykeyVal = $lang['yes'];
					$noPrimaryKey = false;
				}
				else
					$primarykeyVal = $lang['no'];

				$tdWithClass = "<td class='td".($i%2 ? "1" : "2")."'>";
				$tdWithClassLeft = "<td class='td".($i%2 ? "1" : "2")."' style='text-align:left;'>";
				echo "<tr>";
				if($target_table_type == 'table')
				{
					echo $tdWithClass;
					echo "<input type='checkbox' name='check[]' value='".htmlencode($fieldVal)."' id='check_".$i."'/>";
					echo "</td>";
					echo $tdWithClass;
					echo "<a href='?table=".urlencode($target_table)."&amp;action=column_edit&amp;pk=".urlencode($fieldVal)."' title='".$lang['edit']."' class='edit'><span>".$lang['edit']."</span></a>";
					echo "</td>";
					echo $tdWithClass;
					echo "<a href='?table=".urlencode($target_table)."&amp;action=column_confirm&amp;action2=column_delete&amp;pk=".urlencode($fieldVal)."' title='".$lang['del']."' class='delete'><span>".$lang['del']."</span></a>";
					echo "</td>";
				}
				echo $tdWithClass;
				echo htmlencode($colVal);
				echo "</td>";
				echo $tdWithClassLeft;
				echo htmlencode($fieldVal);
				echo "</td>";
				echo $tdWithClassLeft;
				echo htmlencode($typeVal);
				echo "</td>";
				echo $tdWithClassLeft;
				echo htmlencode($notnullVal);
				echo "</td>";
				echo $tdWithClassLeft;
				if($defaultVal===NULL)
					echo "<i class='null'>".$lang['none']."</i>";
				elseif($defaultVal==="NULL")
					echo "<i class='null'>NULL</i>";
				else
					echo htmlencode($defaultVal);
				echo "</td>";
				echo $tdWithClassLeft;
				echo htmlencode($primarykeyVal);
				echo "</td>";
				echo "</tr>";
			}

			echo "</table>";
			if($target_table_type == 'table')
			{
				echo "<a onclick='checkAll()'>".$lang['chk_all']."</a> / <a onclick='uncheckAll()'>".$lang['unchk_all']."</a> <i>".$lang['with_sel'].":</i> ";
				echo "<select name='action2'>";
				//echo "<option value='edit'>".$lang['edit']."</option>";
				echo "<option value='column_delete'>".$lang['del']."</option>";
				if($noPrimaryKey)
					echo "<option value='primarykey_add'>".$lang['prim_key']."</option>";
				echo "</select> ";
				echo "<input type='submit' value='".$lang['go']."' name='massGo' class='btn'/>";
			}
			echo "</form>";
			if($target_table_type == 'table')
			{
				echo "<br/>";
				echo "<form action='?table=".urlencode($target_table)."&amp;action=column_create' method='post'>";
				echo "<input type='hidden' name='tablename' value='".htmlencode($target_table)."'/>";
				echo $lang['add']." <input type='text' name='tablefields' style='width:30px;' value='1'/> ".$lang['tbl_end']." <input type='submit' value='".$lang['go']."' name='addfields' class='btn'/>";
				echo "</form>";
			}
			
			$query = "SELECT sql FROM sqlite_master WHERE name=".$db->quote($target_table);
			$master = $db->selectArray($query);
			
			echo "<br/>";
			echo "<br/>";
			echo "<div class='confirm'>";
			echo "<b>".$lang['query_used_'.$target_table_type]."</b><br/>";
			echo "<span style='font-size:11px;'>".htmlencode($master[0]['sql'])."</span>";
			echo "</div>";
			echo "<br/>";
			if($target_table_type == 'view')
			{
				echo "<br/><hr/><br/>";
				//$query = "SELECT * FROM sqlite_master WHERE type='index' AND tbl_name='".$target_table."'";
				$query = "PRAGMA index_list(".$db->quote_id($target_table).")";
				$result = $db->selectArray($query);
				if(sizeof($result)>0)
				{
					echo "<h2>".$lang['indexes'].":</h2>";
					echo "<table border='0' cellpadding='2' cellspacing='1' class='viewTable'>";
					echo "<tr>";
					echo "<td colspan='1'>";
					echo "</td>";
					echo "<td class='tdheader'>".$lang['name']."</td>";
					echo "<td class='tdheader'>".$lang['unique']."</td>";
					echo "<td class='tdheader'>".$lang['seq_no']."</td>";
					echo "<td class='tdheader'>".$lang['col']." #</td>";
					echo "<td class='tdheader'>".$lang['fld']."</td>";
					echo "</tr>";
					for($i=0; $i<sizeof($result); $i++)
					{
						if($result[$i]['unique']==0)
							$unique = $lang['no'];
						else
							$unique = $lang['yes'];

						$query = "PRAGMA index_info(".$db->quote_id($result[$i]['name']).")";
						$info = $db->selectArray($query);
						$span = sizeof($info);

						$tdWithClass = "<td class='td".($i%2 ? "1" : "2")."'>";
						$tdWithClassLeft = "<td class='td".($i%2 ? "1" : "2")."' style='text-align:left;'>";
						$tdWithClassSpan = "<td class='td".($i%2 ? "1" : "2")."' rowspan='".$span."'>";
						$tdWithClassLeftSpan = "<td class='td".($i%2 ? "1" : "2")."' style='text-align:left;' rowspan='".$span."'>";
						echo "<tr>";
						echo $tdWithClassSpan;
						echo "<a href='?table=".urlencode($target_table)."&amp;action=index_delete&amp;pk=".urlencode($result[$i]['name'])."' title='".$lang['del']."' class='delete'><span>".$lang['del']."</span></a>";
						echo "</td>";
						echo $tdWithClassLeftSpan;
						echo $result[$i]['name'];
						echo "</td>";
						echo $tdWithClassLeftSpan;
						echo $unique;
						echo "</td>";
						for($j=0; $j<$span; $j++)
						{
							if($j!=0)
								echo "<tr>";
							echo $tdWithClassLeft;
							echo htmlencode($info[$j]['seqno']);
							echo "</td>";
							echo $tdWithClassLeft;
							echo htmlencode($info[$j]['cid']);
							echo "</td>";
							echo $tdWithClassLeft;
							echo htmlencode($info[$j]['name']);
							echo "</td>";
							echo "</tr>";
						}
					}
					echo "</table><br/><br/>";
				}
				
				$query = "SELECT * FROM sqlite_master WHERE type='trigger' AND tbl_name=".$db->quote($target_table)." ORDER BY name";
				$result = $db->selectArray($query);
				//print_r($result);
				if(sizeof($result)>0)
				{
					echo "<h2>".$lang['triggers'].":</h2>";
					echo "<table border='0' cellpadding='2' cellspacing='1' class='viewTable'>";
					echo "<tr>";
					echo "<td colspan='1'>";
					echo "</td>";
					echo "<td class='tdheader'>".$lang['name']."</td>";
					echo "<td class='tdheader'>".$lang['sql']."</td>";
					echo "</tr>";
					for($i=0; $i<sizeof($result); $i++)
					{
						$tdWithClass = "<td class='td".($i%2 ? "1" : "2")."'>";
						echo "<tr>";
						echo $tdWithClass;
						echo "<a href='?table=".urlencode($target_table)."&amp;action=trigger_delete&amp;pk=".urlencode($result[$i]['name'])."' title='".$lang['del']."' class='delete'><span>".$lang['del']."</span></a>";
						echo "</td>";
						echo $tdWithClass;
						echo htmlencode($result[$i]['name']);
						echo "</td>";
						echo $tdWithClass;
						echo htmlencode($result[$i]['sql']);
						echo "</td>";
					}
					echo "</table><br/><br/>";
				}
				
				echo "<form action='?table=".urlencode($target_table)."&amp;action=index_create' method='post'>";
				echo "<input type='hidden' name='tablename' value='".htmlencode($target_table)."'/>";
				echo "<br/><div class='tdheader'>";
				echo $lang['create_index2']." <input type='text' name='numcolumns' style='width:30px;' value='1'/> ".$lang['cols']." <input type='submit' value='".$lang['go']."' name='addindex' class='btn'/>";
				echo "</div>";
				echo "</form>";
				
				echo "<form action='?table=".urlencode($target_table)."&amp;action=trigger_create' method='post'>";
				echo "<input type='hidden' name='tablename' value='".htmlencode($target_table)."'/>";
				echo "<br/><div class='tdheader'>";
				echo $lang['create_trigger2']." <input type='submit' value='".$lang['go']."' name='addindex' class='btn'/>";
				echo "</div>";
				echo "</form>";
			}
			break;

		//- Create column (=column_create)
		case "column_create":
			echo "<h2>".sprintf($lang['new_fld'],htmlencode($_POST['tablename']))."</h2>";
			if($_POST['tablefields']=="" || intval($_POST['tablefields'])<=0)
				echo $lang['specify_fields'];
			else if($_POST['tablename']=="")
				echo $lang['specify_tbl'];
			else
			{
				$num = intval($_POST['tablefields']);
				$name = $_POST['tablename'];
				echo "<form action='?table=".urlencode($_POST['tablename'])."&amp;action=column_create&amp;confirm=1' method='post'>";
				echo "<input type='hidden' name='tablename' value='".htmlencode($name)."'/>";
				echo "<input type='hidden' name='rows' value='".$num."'/>";
				echo "<table border='0' cellpadding='2' cellspacing='1' class='viewTable'>";
				echo "<tr>";
				$headings = array($lang["fld"], $lang["type"], $lang["prim_key"]);    
				if($db->getType() != "SQLiteDatabase") $headings[] = $lang["autoincrement"];
				$headings[] = $lang["not_null"];
				$headings[] = $lang["def_val"];
				
				for($k=0; $k<count($headings); $k++)
					echo "<td class='tdheader'>" . $headings[$k] . "</td>";
				echo "</tr>";

				for($i=0; $i<$num; $i++)
				{
					$tdWithClass = "<td class='td" . ($i%2 ? "1" : "2") . "'>";
					echo "<tr>";
					echo $tdWithClass;
					echo "<input type='text' name='".$i."_field' style='width:200px;'/>";
					echo "</td>";
					echo $tdWithClass;
					echo "<select name='".$i."_type' id='i".$i."_type' onchange='toggleAutoincrement(".$i.");'>";
					foreach ($sqlite_datatypes as $t) {
						echo "<option value='".htmlencode($t)."'>".htmlencode($t)."</option>";
					}
					echo "</select>";
					echo "</td>";
					echo $tdWithClass;
					echo "<label><input type='checkbox' name='".$i."_primarykey'/> ".$lang['yes']."</label>";
					echo "</td>";
					if($db->getType() != "SQLiteDatabase")
					{
						echo $tdWithClass;
						echo "<label><input type='checkbox' name='".$i."_autoincrement' id='i".$i."_autoincrement'/> ".$lang['yes']."</label>";
						echo "</td>";
					}
					echo $tdWithClass;
					echo "<label><input type='checkbox' name='".$i."_notnull'/> ".$lang['yes']."</label>";
					echo "</td>";
					echo $tdWithClass;
					echo "<select name='".$i."_defaultoption' id='i".$i."_defaultoption' onchange=\"if(this.value!='defined' && this.value!='expr') document.getElementById('i".$i."_defaultvalue').value='';\">";
					echo "<option value='none'>".$lang['none']."</option><option value='defined'>".$lang['as_defined'].":</option><option>NULL</option><option>CURRENT_TIME</option><option>CURRENT_DATE</option><option>CURRENT_TIMESTAMP</option><option value='expr'>".$lang['expression'].":</option>";
					echo "</select>";
					echo "<input type='text' name='".$i."_defaultvalue' id='i".$i."_defaultvalue' style='width:100px;' onchange=\"if(document.getElementById('i".$i."_defaultoption').value!='expr') document.getElementById('i".$i."_defaultoption').value='defined';\"/>";
					echo "</td>";
					echo "</tr>";
				}
				echo "<tr>";
				echo "<td class='tdheader' style='text-align:right;' colspan='6'>";
				echo "<input type='submit' value='".$lang['add_flds']."' class='btn'/> ";
				echo "<a href='?table=".urlencode($_POST['tablename'])."&amp;action=column_view'>".$lang['cancel']."</a>";
				echo "</td>";
				echo "</tr>";
				echo "</table>";
				echo "</form>";
			}
			break;

		//- Delete column (=column_confirm)
		case "column_confirm":
			if(isset($_POST['check']))
				$pks = $_POST['check'];
			elseif(isset($_GET['pk']))
				$pks = array($_GET['pk']);
			else $pks = array();
			
			if(sizeof($pks)==0) //nothing was selected so show an error
			{
				echo "<div class='confirm'>";
				echo $lang['err'].": ".$lang['no_sel'];
				echo "</div>";
				echo "<br/><br/><a href='?table=".urlencode($target_table)."&amp;action=column_view'>".$lang['return']."</a>";
			}
			else
			{
				$str = $pks[0];
				$pkVal = $pks[0];
				for($i=1; $i<sizeof($pks); $i++)
				{
					$str .= ", ".$pks[$i];
					$pkVal .= ":".$pks[$i];
				}
				echo "<form action='?table=".urlencode($target_table)."&amp;action=".$_REQUEST['action2']."&amp;confirm=1&amp;pk=".urlencode($pkVal)."' method='post'>";
				echo "<div class='confirm'>";
				printf($lang['ques_'.$_REQUEST['action2']], htmlencode($str), htmlencode($target_table));
				echo "<br/><br/>";
				echo "<input type='submit' value='".$lang['confirm']."' class='btn'/> ";
				echo "<a href='?table=".urlencode($target_table)."&amp;action=column_view'>".$lang['cancel']."</a>";
				echo "</div>";
			}
			break;

		//- Edit column (=column_edit)
		case "column_edit":
			echo "<h2>".sprintf($lang['edit_col'], htmlencode($_GET['pk']))." ".$lang['on_tbl']." '".htmlencode($target_table)."'</h2>";
			echo $lang['sqlite_limit']."<br/><br/>";
			if(!isset($_GET['pk']))
				echo $lang['specify_col'];
			else if (!$target_table)
				echo $lang['specify_tbl'];
			else
			{
				$query = "PRAGMA table_info(".$db->quote_id($target_table).")";
				$result = $db->selectArray($query);

				for($i=0; $i<sizeof($result); $i++)
				{
					if($result[$i][1]==$_GET['pk'])
					{
						$colVal = $result[$i][0];
						$fieldVal = $result[$i][1];
						$typeVal = $result[$i]['type'];
						$notnullVal = $result[$i][3];
						$defaultVal = $result[$i][4];
						$primarykeyVal = $result[$i][5];
						break;
					}
				}
				
				$name = $target_table;
				echo "<form action='?table=".urlencode($name)."&amp;action=column_edit&amp;confirm=1' method='post'>";
				echo "<input type='hidden' name='tablename' value='".htmlencode($name)."'/>";
				echo "<input type='hidden' name='oldvalue' value='".htmlencode($_GET['pk'])."'/>";
				echo "<table border='0' cellpadding='2' cellspacing='1' class='viewTable'>";
				echo "<tr>";
				//$headings = array("Field", "Type", "Primary Key", "Autoincrement", "Not NULL", "Default Value");
				$headings = array($lang["fld"], $lang["type"]);
				for($k=0; $k<count($headings); $k++)
					echo "<td class='tdheader'>".$headings[$k]."</td>";
				echo "</tr>";
			
				$i = 0;
				$tdWithClass = "<td class='td" . ($i%2 ? "1" : "2") . "'>";
				echo "<tr>";
				echo $tdWithClass;
				echo "<input type='text' name='".$i."_field' style='width:200px;' value='".htmlencode($fieldVal)."'/>";
				echo "</td>";
				echo $tdWithClass;
				echo "<select name='".$i."_type' id='i".$i."_type' onchange='toggleAutoincrement(".$i.");'>";
				if(!in_array($typeVal, $sqlite_datatypes))
					echo "<option value='".htmlencode($typeVal)."' selected='selected'>".htmlencode($typeVal)."</option>";
				foreach ($sqlite_datatypes as $t) {
					if($t==$typeVal)
						echo "<option value='".htmlencode($t)."' selected='selected'>".htmlencode($t)."</option>";
					else
						echo "<option value='".htmlencode($t)."'>".htmlencode($t)."</option>";
				}
				echo "</select>";
				echo "</td>";
				/*
				echo $tdWithClass;
				if($primarykeyVal)
					echo "<input type='checkbox' name='".$i."_primarykey' checked='checked'/> Yes";
				else
					echo "<input type='checkbox' name='".$i."_primarykey'/> Yes";
				echo "</td>";
				echo $tdWithClass;
				if(1==2)
					echo "<input type='checkbox' name='".$i."_autoincrement' id='".$i."_autoincrement' checked='checked'/> Yes";
				else
					echo "<input type='checkbox' name='".$i."_autoincrement' id='".$i."_autoincrement'/> Yes";
				echo "</td>";
				echo $tdWithClass;
				if($notnullVal)
					echo "<input type='checkbox' name='".$i."_notnull' checked='checked'/> Yes";
				else
					echo "<input type='checkbox' name='".$i."_notnull'/> Yes";
				echo "</td>";
				echo $tdWithClass;
				echo "<input type='text' name='".$i."_defaultvalue' value='".$defaultVal."' style='width:100px;'/>";
				echo "</td>";
				*/
				echo "</tr>";

				echo "<tr>";
				echo "<td class='tdheader' style='text-align:right;' colspan='6'>";
				echo "<input type='submit' value='".$lang['save_ch']."' class='btn'/> ";
				echo "<a href='?table=".urlencode($target_table)."&amp;action=column_view'>".$lang['cancel']."</a>";
				echo "</td>";
				echo "</tr>";
				echo "</table>";
				echo "</form>";
			}
			break;

		//- Delete index (=index_delete)
		case "index_delete":
			echo "<form action='?table=".urlencode($target_table)."&amp;action=index_delete&amp;pk=".urlencode($_GET['pk'])."&amp;confirm=1' method='post'>";
			echo "<div class='confirm'>";
			echo sprintf($lang['ques_del_index'], htmlencode($_GET['pk']))."<br/><br/>";
			echo "<input type='submit' value='".$lang['confirm']."' class='btn'/> ";
			echo "<a href='?table=".urlencode($target_table)."&amp;action=column_view'>".$lang['cancel']."</a>";
			echo "</div>";
			echo "</form>";
			break;

		//- Delete trigger (=trigger_delete)
		case "trigger_delete":
			echo "<form action='?table=".urlencode($target_table)."&amp;action=trigger_delete&amp;pk=".urlencode($_GET['pk'])."&amp;confirm=1' method='post'>";
			echo "<div class='confirm'>";
			echo sprintf($lang['ques_del_trigger'], htmlencode($_GET['pk']))."<br/><br/>";
			echo "<input type='submit' value='".$lang['confirm']."' class='btn'/> ";
			echo "<a href='?table=".urlencode($target_table)."&amp;action=column_view'>".$lang['cancel']."</a>";
			echo "</div>";
			echo "</form>";
			break;

		//- Create trigger (=trigger_create)
		case "trigger_create":
			echo "<h2>".$lang['create_trigger']." '".htmlencode($_POST['tablename'])."'</h2>";
			if($_POST['tablename']=="")
				echo $lang['specify_tbl'];
			else
			{
				echo "<form action='?table=".urlencode($_POST['tablename'])."&amp;action=trigger_create&amp;confirm=1' method='post'>";
				echo $lang['trigger_name'].": <input type='text' name='trigger_name'/><br/><br/>";
				echo "<fieldset><legend>".$lang['db_event']."</legend>";
				echo $lang['before']."/".$lang['after'].": ";
				echo "<select name='beforeafter'>";
				echo "<option value=''></option>";
				echo "<option value='BEFORE'>".$lang['before']."</option>"; 
				echo "<option value='AFTER'>".$lang['after']."</option>"; 
				echo "<option value='INSTEAD OF'>".$lang['instead']."</option>"; 
				echo "</select>";
				echo "<br/><br/>";
				echo $lang['event'].": ";
				echo "<select name='event'>";
				echo "<option value='DELETE'>".$lang['del']."</option>";
				echo "<option value='INSERT'>".$lang['insert']."</option>";
				echo "<option value='UPDATE'>".$lang['update']."</option>";
				echo "</select>";
				echo "</fieldset><br/><br/>";
				echo "<fieldset><legend>".$lang['trigger_act']."</legend>";
				echo "<label><input type='checkbox' name='foreachrow'/> ".$lang['each_row']."</label><br/><br/>";
				echo $lang['when_exp'].":<br/>";
				echo "<textarea name='whenexpression' style='width:500px; height:100px;' rows='8' cols='50'></textarea>";
				echo "<br/><br/>";
				echo $lang['trigger_step'].":<br/>";
				echo "<textarea name='triggersteps' style='width:500px; height:100px;' rows='8' cols='50'></textarea>";
				echo "</fieldset><br/><br/>";
				echo "<input type='submit' value='".$lang['create_trigger2']."' class='btn'/> ";
				echo "<a href='?table=".urlencode($_POST['tablename'])."&amp;action=column_view'>".$lang['cancel']."</a>";
				echo "</form>";
			}
			break;

		//- Create index (=index_create)
		case "index_create":
			echo "<h2>".$lang['create_index']." '".htmlencode($_POST['tablename'])."'</h2>";
			if($_POST['numcolumns']=="" || intval($_POST['numcolumns'])<=0)
				echo $lang['specify_fields'];
			else if($_POST['tablename']=="")
				echo $lang['specify_tbl'];
			else
			{
				echo "<form action='?table=".urlencode($_POST['tablename'])."&amp;action=index_create&amp;confirm=1' method='post'>";
				$num = intval($_POST['numcolumns']);
				$query = "PRAGMA table_info(".$db->quote_id($_POST['tablename']).")";

				$result = $db->selectArray($query);
				echo "<fieldset><legend>".$lang['define_index']."</legend>";
				echo $lang['index_name'].": <input type='text' name='name'/><br/>";
				echo $lang['dup_val'].": ";
				echo "<select name='duplicate'>";
				echo "<option value='yes'>".$lang['allow']."</option>";
				echo "<option value='no'>".$lang['not_allow']."</option>";
				echo "</select><br/>";
				echo "</fieldset>";
				echo "<br/>";
				echo "<fieldset><legend>".$lang['define_in_col']."</legend>";
				for($i=0; $i<$num; $i++)
				{
					echo "<select name='".$i."_field'>";
					echo "<option value=''>--".$lang['ignore']."--</option>";
					for($j=0; $j<sizeof($result); $j++)
						echo "<option value='".htmlencode($result[$j][1])."'>".htmlencode($result[$j][1])."</option>";
					echo "</select> ";
					echo "<select name='".$i."_order'>";
					echo "<option value=''></option>";
					echo "<option value=' ASC'>".$lang['asc']."</option>";
					echo "<option value=' DESC'>".$lang['desc']."</option>";
					echo "</select><br/>";
				}
				echo "</fieldset>";
				echo "<br/><br/>";
				echo "<input type='hidden' name='num' value='".$num."'/>";
				echo "<input type='submit' value='".$lang['create_index1']."' class='btn'/> ";
				echo "<a href='?table=".urlencode($_POST['tablename'])."&amp;action=column_view'>".$lang['cancel']."</a>";
				echo "</form>";
			}
			break;
	}
	echo "</div>";
}