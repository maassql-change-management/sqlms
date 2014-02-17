<?php
//- Switch on $_GET['action'] for operations without output
if(isset($_GET['action']) && isset($_GET['confirm']))
{
	switch($_GET['action'])
	{
	//- Table actions

		//- Create table (=table_create)
		case "table_create":
			$num = intval($_POST['rows']);
			$name = $_POST['tablename'];
			$primary_keys = array();
			for($i=0; $i<$num; $i++)
			{
				if($_POST[$i.'_field']!="" && isset($_POST[$i.'_primarykey']))
				{
					$primary_keys[] = $_POST[$i.'_field'];
				}
			}
			$query = "CREATE TABLE ".$db->quote($name)." (";
			for($i=0; $i<$num; $i++)
			{
				if($_POST[$i.'_field']!="")
				{
					$query .= $db->quote($_POST[$i.'_field'])." ";
					$query .= $_POST[$i.'_type']." ";
					if(isset($_POST[$i.'_primarykey']))
					{
						if(count($primary_keys)==1)
						{
							$query .= "PRIMARY KEY "; 
							if(isset($_POST[$i.'_autoincrement']) && $db->getType() != "SQLiteDatabase")
								$query .=  "AUTOINCREMENT ";
						}
						$query .= "NOT NULL ";
					}
					if(!isset($_POST[$i.'_primarykey']) && isset($_POST[$i.'_notnull']))
						$query .= "NOT NULL ";
					if($_POST[$i.'_defaultoption']!='defined' && $_POST[$i.'_defaultoption']!='none' && $_POST[$i.'_defaultoption']!='expr')
						$query .= "DEFAULT ".$_POST[$i.'_defaultoption']." ";
					elseif($_POST[$i.'_defaultoption']=='expr')
						$query .= "DEFAULT (".$_POST[$i.'_defaultvalue'].") ";
					elseif(isset($_POST[$i.'_defaultvalue']) && $_POST[$i.'_defaultoption']=='defined')
					{
						$typeAffinity = get_type_affinity($_POST[$i.'_type']);
						if(($typeAffinity=="INTEGER" || $typeAffinity=="REAL" || $typeAffinity=="NUMERIC") && is_numeric($_POST[$i.'_defaultvalue']))
							$query .= "DEFAULT ".$_POST[$i.'_defaultvalue']."  ";
						else
							$query .= "DEFAULT ".$db->quote($_POST[$i.'_defaultvalue'])." ";
					}
					$query = substr($query, 0, sizeof($query)-2);
					$query .= ", ";
				}
			}
			if (count($primary_keys)>1)
			{
				$compound_key = "";
				foreach ($primary_keys as $primary_key)
				{
					$compound_key .= ($compound_key=="" ? "" : ", ") . $db->quote($primary_key);
				}
				$query .= "PRIMARY KEY (".$compound_key."), ";
			}
			$query = substr($query, 0, sizeof($query)-3);
			$query .= ")";
			$result = $db->query($query);
			if($result===false)
				$error = true;
			$completed = $lang['tbl']." '".htmlencode($_POST['tablename'])."' ".$lang['created'].".<br/><span style='font-size:11px;'>".htmlencode($query)."</span>";
			$backlinkParameters = "&amp;action=column_view&amp;table=".urlencode($name);
			break;

		//- Empty table (=table_empty)
		case "table_empty":
			$query = "DELETE FROM ".$db->quote_id($_POST['tablename']);
			$result = $db->query($query);
			if($result===false)
				$error = true;
			$query = "VACUUM";
			$result = $db->query($query);
			if($result===false)
				$error = true;
			$completed = $lang['tbl']." '".htmlencode($_POST['tablename'])."' ".$lang['emptied'].".<br/><span style='font-size:11px;'>".htmlencode($query)."</span>";
			$backlinkParameters = "&amp;action=row_view&amp;table=".urlencode($name);
			break;

		//- Create view (=view_create)
		case "view_create":
			$query = "CREATE VIEW ".$db->quote($_POST['viewname'])." AS ".$_POST['select'];
			$result = $db->query($query);
			if($result===false)
				$error = true;
			$completed = $lang['view']." '".htmlencode($_POST['viewname'])."' ".$lang['created'].".<br/><span style='font-size:11px;'>".htmlencode($query)."</span>";
			$backlinkParameters = "&amp;action=column_view&amp;table=".urlencode($_POST['viewname']);
			break;

		//- Drop table (=table_drop)
		case "table_drop":
			$query = "DROP TABLE ".$db->quote_id($_POST['tablename']);
			$result=$db->query($query);
			if($result===false)
				$error = true;
			$completed = $lang['tbl']." '".htmlencode($_POST['tablename'])."' ".$lang['dropped'].".";
			$backlinkParameters = "";
			break;

		//- Drop view (=view_drop)
		case "view_drop":
			$query = "DROP VIEW ".$db->quote_id($_POST['viewname']);
			$result=$db->query($query);
			if($result===false)
				$error = true;
			$completed = $lang['view']." '".htmlencode($_POST['viewname'])."' ".$lang['dropped'].".";
			$backlinkParameters = "";
			break;

		//- Rename table (=table_rename)
		case "table_rename":
			$query = "ALTER TABLE ".$db->quote_id($_POST['oldname'])." RENAME TO ".$db->quote($_POST['newname']);
			if($db->getVersion()==3)
				$result = $db->query($query, true);
			else
				$result = $db->query($query, false);
			if($result===false)
				$error = true;
			$completed = $lang['tbl']." '".htmlencode($_POST['oldname'])."' ".$lang['renamed']." '".htmlencode($_POST['newname'])."'.<br/><span style='font-size:11px;'>".htmlencode($query)."</span>";
			$backlinkParameters = "&amp;action=row_view&amp;table=".urlencode($_POST['newname']);
			break;

	//- Row actions

		//- Create row (=row_create)
		case "row_create":
			$completed = "";
			$num = $_POST['numRows'];
			$fields = explode(":", $_POST['fields']);
			$z = 0;
			
			$query = "PRAGMA table_info(".$db->quote_id($target_table).")";
			$result = $db->selectArray($query);
			
			for($i=0; $i<$num; $i++)
			{
				if(!isset($_POST[$i.":ignore"]))
				{
					$query_cols = "";
					$query_vals = "";
					$all_default = true;
					for($j=0; $j<sizeof($fields); $j++)
					{
						// PHP replaces space with underscore
						$fields[$j] = str_replace(" ","_",$fields[$j]);
						
						$null = isset($_POST[$i.":".$fields[$j]."_null"]);
						if(!$null)
						{
							if(!isset($_POST[$i.":".$fields[$j]]) && $debug)
							{
								echo "MISSING POST INDEX (".$i.":".$fields[$j].")<br><pre />";
								var_dump($_POST);
								echo "</pre><hr />";
							} 
							$value = $_POST[$i.":".$fields[$j]];
						}
						else
							$value = "";
						if($value===$result[$j]['dflt_value'])
						{
							// if the value is the default value, skip it
							continue;
						} else
							$all_default = false;
						$query_cols .= $db->quote_id($fields[$j]).",";
						
						$type = $result[$j]['type'];
						$typeAffinity = get_type_affinity($type);
						$function = $_POST["function_".$i."_".$fields[$j]];
						if($function!="")
							$query_vals .= $function."(";
						if(($typeAffinity=="TEXT" || $typeAffinity=="NONE") && !$null)
							$query_vals .= $db->quote($value);
						elseif(($typeAffinity=="INTEGER" || $typeAffinity=="REAL"|| $typeAffinity=="NUMERIC") && $value=="")
							$query_vals .= "NULL";
						elseif($null)
							$query_vals .= "NULL";
						else
							$query_vals .= $db->quote($value);
						if($function!="")
							$query_vals .= ")";
						$query_vals .= ",";
					}
					$query = "INSERT INTO ".$db->quote_id($target_table);
					if(!$all_default)
					{
						$query_cols = substr($query_cols, 0, strlen($query_cols)-1);
						$query_vals = substr($query_vals, 0, strlen($query_vals)-1);
					
						$query.=" (". $query_cols . ") VALUES (". $query_vals. ")";
					} else {
						$query .= " DEFAULT VALUES";
					}
					$result1 = $db->query($query);
					if($result1===false)
						$error = true;
					$completed .= "<span style='font-size:11px;'>".htmlencode($query)."</span><br/>";
					$z++;
				}
			}
			$completed = $z." ".$lang['rows']." ".$lang['inserted'].".<br/><br/>".$completed;
			$backlinkParameters = "&amp;action=column_view&amp;table=".urlencode($target_table);
			break;

		//- Delete row (=row_delete)
		case "row_delete":
			$pks = explode(":", $_GET['pk']);
			$query = "DELETE FROM ".$db->quote_id($target_table)." WHERE ROWID = ".$pks[0];
			for($i=1; $i<sizeof($pks); $i++)
			{
				$query .= " OR ROWID = ".$pks[$i];
			}
			$result = $db->query($query);
			if($result===false)
				$error = true;
			$completed = sizeof($pks)." ".$lang['rows']." ".$lang['deleted'].".<br/><span style='font-size:11px;'>".htmlencode($query)."</span>";
			$backlinkParameters = "&amp;action=row_view&amp;table=".urlencode($target_table);
			break;

		//- Edit row (=row_edit)
		case "row_edit":
			$pks = explode(":", $_GET['pk']);
			$fields = explode(":", $_POST['fieldArray']);
			
			$z = 0;
			
			$query = "PRAGMA table_info(".$db->quote_id($target_table).")";
			$result = $db->selectArray($query);
			
			if(isset($_POST['new_row']))
				$completed = "";
			else
				$completed = sizeof($pks)." ".$lang['rows']." ".$lang['affected'].".<br/><br/>";

			for($i=0; $i<sizeof($pks); $i++)
			{
				if(isset($_POST['new_row']))
				{
					$query = "INSERT INTO ".$db->quote_id($target_table)." (";
					for($j=0; $j<sizeof($fields); $j++)
					{
						$query .= $db->quote_id($fields[$j]).",";
					}
					$query = substr($query, 0, sizeof($query)-2);
					$query .= ") VALUES (";
					for($j=0; $j<sizeof($fields); $j++)
					{
						$field_index = str_replace(" ","_",$fields[$j]);
						$value = $_POST[$pks[$i].":".$field_index];
						$null = isset($_POST[$pks[$i].":".$field_index."_null"]);
						$type = $result[$j][2];
						$typeAffinity = get_type_affinity($type);
						$function = $_POST["function_".$pks[$i]."_".$field_index];
						if($function!="")
							$query .= $function."(";
							//di - messed around with this logic for null values
						if(($typeAffinity=="TEXT" || $typeAffinity=="NONE") && $null==false)
							$query .= $db->quote($value);
						else if(($typeAffinity=="INTEGER" || $typeAffinity=="REAL" || $typeAffinity=="NUMERIC") && $null==false && $value=="")
							$query .= "NULL";
						else if($null==true)
							$query .= "NULL";
						else
							$query .= $db->quote($value);
						if($function!="")
							$query .= ")";
						$query .= ",";
					}
					$query = substr($query, 0, sizeof($query)-2);
					$query .= ")";
					$result1 = $db->query($query);
					if($result1===false)
						$error = true;
					$z++;
				}
				else
				{
					$query = "UPDATE ".$db->quote_id($target_table)." SET ";
					for($j=0; $j<sizeof($fields); $j++)
					{
						if(!is_numeric($pks[$i])) continue;
						$field_index = str_replace(" ","_",$fields[$j]);
						$function = $_POST["function_".$pks[$i]."_".$field_index];
						$null = isset($_POST[$pks[$i].":".$field_index."_null"]);
						$query .= $db->quote_id($fields[$j])."=";
						if($function!="")
							$query .= $function."(";
						if($null)
							$query .= "NULL";
						else
							$query .= $db->quote($_POST[$pks[$i].":".$field_index]);
						if($function!="")
							$query .= ")";
						$query .= ", ";
					}
					$query = substr($query, 0, sizeof($query)-3);
					$query .= " WHERE ROWID = ".$pks[$i];
					$result1 = $db->query($query);
					if($result1===false)
					{
						$error = true;
					}
				}
				$completed .= "<span style='font-size:11px;'>".htmlencode($query)."</span><br/>";
			}
			if(isset($_POST['new_row']))
				$completed = $z." ".$lang['rows']." ".$lang['inserted'].".<br/><br/>".$completed;
			$backlinkParameters = "&amp;action=row_view&amp;table=".urlencode($target_table);
			break;

	//- Column actions

		//- Create column (=column_create)
		case "column_create":
			$num = intval($_POST['rows']);
			for($i=0; $i<$num; $i++)
			{
				if($_POST[$i.'_field']!="")
				{
					$query = "ALTER TABLE ".$db->quote_id($target_table)." ADD ".$db->quote($_POST[$i.'_field'])." ";
					$query .= $_POST[$i.'_type']." ";
					if(isset($_POST[$i.'_primarykey']))
						$query .= "PRIMARY KEY ";
					if(isset($_POST[$i.'_notnull']))
						$query .= "NOT NULL ";
					if($_POST[$i.'_defaultoption']!='defined' && $_POST[$i.'_defaultoption']!='none' && $_POST[$i.'_defaultoption']!='expr')
						$query .= "DEFAULT ".$_POST[$i.'_defaultoption']." ";
					elseif($_POST[$i.'_defaultoption']=='expr')
						$query .= "DEFAULT (".$_POST[$i.'_defaultvalue'].") ";
					elseif(isset($_POST[$i.'_defaultvalue']) && $_POST[$i.'_defaultoption']=='defined')
					{
						$typeAffinity = get_type_affinity($_POST[$i.'_type']);
						if(($typeAffinity=="INTEGER" || $typeAffinity=="REAL" || $typeAffinity=="NUMERIC") && is_numeric($_POST[$i.'_defaultvalue']))
							$query .= "DEFAULT ".$_POST[$i.'_defaultvalue']."  ";
						else
							$query .= "DEFAULT ".$db->quote($_POST[$i.'_defaultvalue'])." ";
					}
					if($db->getVersion()==3 &&
						($_POST[$i.'_defaultoption']=='defined' || $_POST[$i.'_defaultoption']=='none' || $_POST[$i.'_defaultoption']=='NULL')
						// Sqlite3 cannot add columns with default values that are not constant, so use AlterTable-workaround
						&& !isset($_POST[$i.'_primarykey'])) // sqlite3 cannot add primary key columns
						$result = $db->query($query, true);
					else
						$result = $db->query($query, false);
					if($result===false)
						$error = true;
				}
			}
			$completed = $lang['tbl']." '".htmlencode($target_table)."' ".$lang['altered'].".";
			$backlinkParameters = "&amp;action=column_view&amp;table=".urlencode($target_table);
			break;

		//- Delete column (=column_delete)
		case "column_delete":
			$pks = explode(":", $_GET['pk']);
			$query = "ALTER TABLE ".$db->quote_id($target_table).' DROP '.$db->quote_id($pks[0]);
			for($i=1; $i<sizeof($pks); $i++)
			{
				$query .= ", DROP ".$db->quote_id($pks[$i]);
			}
			$result = $db->query($query);
			if($result===false)
				$error = true;
			$completed = $lang['tbl']." '".htmlencode($target_table)."' ".$lang['altered'].".";
			$backlinkParameters = "&amp;action=column_view&amp;table=".urlencode($target_table);
			break;

		//- Add a primary key (=primarykey_add)
		case "primarykey_add":
			$pks = explode(":", $_GET['pk']);
			$query = "ALTER TABLE ".$db->quote_id($target_table).' ADD PRIMARY KEY ('.$db->quote_id($pks[0]);
			for($i=1; $i<sizeof($pks); $i++)
			{
				$query .= ", ".$db->quote_id($pks[$i]);
			}
			$query .= ")";
			$result = $db->query($query);
			if($result===false)
				$error = true;
			$completed = $lang['tbl']." '".htmlencode($target_table)."' ".$lang['altered'].".";
			$backlinkParameters = "&amp;action=column_view&amp;table=".urlencode($target_table);
			break;

		//- Edit column (=column_edit)
		case "column_edit":
			$query = "ALTER TABLE ".$db->quote_id($target_table).' CHANGE '.$db->quote_id($_POST['oldvalue'])." ".$db->quote($_POST['0_field'])." ".$_POST['0_type'];
			$result = $db->query($query);
			if($result===false)
				$error = true;
			$completed = $lang['tbl']." '".htmlencode($target_table)."' ".$lang['altered'].".";
			$backlinkParameters = "&amp;action=column_view&amp;table=".urlencode($target_table);
			break;

		//- Delete trigger (=trigger_delete)
		case "trigger_delete":
			$query = "DROP TRIGGER ".$db->quote_id($_GET['pk']);
			$result = $db->query($query);
			if($result===false)
				$error = true;
			$completed = $lang['trigger']." '".htmlencode($_GET['pk'])."' ".$lang['deleted'].".<br/><span style='font-size:11px;'>".htmlencode($query)."</span>";
			$backlinkParameters = "&amp;action=column_view&amp;table=".urlencode($target_table);
			break;

		//- Delete index (=index_delete)
		case "index_delete":
			$query = "DROP INDEX ".$db->quote_id($_GET['pk']);
			$result = $db->query($query);
			if($result===false)
				$error = true;
			$completed = $lang['index']." '".htmlencode($_GET['pk'])."' ".$lang['deleted'].".<br/><span style='font-size:11px;'>".htmlencode($query)."</span>";
			$backlinkParameters = "&amp;action=column_view&amp;table=".urlencode($target_table);
			break;

		//- Create trigger (=trigger_create)
		case "trigger_create":
			$str = "CREATE TRIGGER ".$db->quote($_POST['trigger_name']);
			if($_POST['beforeafter']!="")
				$str .= " ".$_POST['beforeafter'];
			$str .= " ".$_POST['event']." ON ".$db->quote_id($target_table);
			if(isset($_POST['foreachrow']))
				$str .= " FOR EACH ROW";
			if($_POST['whenexpression']!="")
				$str .= " WHEN ".$_POST['whenexpression'];
			$str .= " BEGIN";
			$str .= " ".$_POST['triggersteps'];
			$str .= " END";
			$query = $str;
			$result = $db->query($query);
			if($result===false)
				$error = true;
			$completed = $lang['trigger']." ".$lang['created'].".<br/><span style='font-size:11px;'>".htmlencode($query)."</span>";
			$backlinkParameters = "&amp;action=column_view&amp;table=".urlencode($target_table);
			break;

		//- Create index (=index_create)
		case "index_create":
			$num = $_POST['num'];
			if($_POST['name']=="")
			{
				$completed = $lang['blank_index'];
			}
			else if($_POST['0_field']=="")
			{
				$completed = $lang['one_index'];
			}
			else
			{
				$str = "CREATE ";
				if($_POST['duplicate']=="no")
					$str .= "UNIQUE ";
				$str .= "INDEX ".$db->quote($_POST['name'])." ON ".$db->quote_id($target_table)." (";
				$str .= $db->quote_id($_POST['0_field']).$_POST['0_order'];
				for($i=1; $i<$num; $i++)
				{
					if($_POST[$i.'_field']!="")
						$str .= ", ".$db->quote_id($_POST[$i.'_field']).$_POST[$i.'_order'];
				}
				$str .= ")";
				$query = $str;
				$result = $db->query($query);
				if($result===false)
					$error = true;
				$completed = $lang['index']." ".$lang['created'].".<br/><span style='font-size:11px;'>".htmlencode($query)."</span>";
			}
			$backlinkParameters = "&amp;action=column_view&amp;table=".urlencode($target_table);
			break;
	}
}