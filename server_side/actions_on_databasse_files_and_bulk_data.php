<?php

//- Actions on database files and bulk data
if ($auth->isAuthorized())
{

	//- Create a new database
	if(isset($_POST['new_dbname']))
	{
		if($_POST['new_dbname']=='')
		{
			// TODO: Display an error message (do NOT echo here. echo below in the html-body!)
		}
		else
		{
			$str = preg_replace('@[^\w-.]@','', $_POST['new_dbname']);
			$dbname = $str;
			$dbpath = $str;
			if(checkDbName($dbname))
			{
				$tdata = array();	
				$tdata['name'] = $dbname;
				$tdata['path'] = $directory.DIRECTORY_SEPARATOR.$dbpath;
				$td = new Database($tdata);
				$td->query("VACUUM");
			} else
			{
				if(is_file($dbname) || is_dir($dbname)) $dbexists = true;
				else $extension_not_allowed=true;
			}
		}
	}
	
	//- Scan a directory for databases
	if($directory!==false)
	{
		if($directory[strlen($directory)-1]==DIRECTORY_SEPARATOR) //if user has a trailing slash in the directory, remove it
			$directory = substr($directory, 0, strlen($directory)-1);
			
		if(is_dir($directory)) //make sure the directory is valid
		{
			if($subdirectories===true)
				$arr = dir_tree($directory);
			else
				$arr = scandir($directory);
			$databases = array();
			$j = 0;
			for($i=0; $i<sizeof($arr); $i++) //iterate through all the files in the databases
			{
				if($subdirectories===false)
					$arr[$i] = $directory.DIRECTORY_SEPARATOR.$arr[$i];
				
				if(@!is_file($arr[$i])) continue;
				$con = file_get_contents($arr[$i], NULL, NULL, 0, 60);
				if(strpos($con, "** This file contains an SQLite 2.1 database **", 0)!==false || strpos($con, "SQLite format 3", 0)!==false)
				{
					$databases[$j]['path'] = $arr[$i];
					if($subdirectories===false)
						$databases[$j]['name'] = basename($arr[$i]);
					else
						$databases[$j]['name'] = $arr[$i];
					$databases[$j]['writable'] = is_writable($databases[$j]['path']);
					$databases[$j]['writable_dir'] = is_writable(dirname($databases[$j]['path']));
					$databases[$j]['readable'] = is_readable($databases[$j]['path']);
					$j++;
				}
			}
			// 22 August 2011: gkf fixed bug #50.
			sort($databases);
			if(isset($tdata))
			{
				foreach($databases as $db_id => $database)
				{
					if($database['path'] == $tdata['path'])
					{
						$_SESSION[COOKIENAME.'currentDB'] = $database;
						break;
					}
				}
			}
		}
		else //the directory is not valid - display error and exit
		{
			echo "<div class='confirm' style='margin:20px;'>".$lang['not_dir']."</div>";
			exit();
		}
	}
	else
	{
		for($i=0; $i<sizeof($databases); $i++)
		{
			if(!file_exists($databases[$i]['path']))
				continue; //skip if file not found ! - probably a warning can be displayed - later
			$databases[$i]['writable'] = is_writable($databases[$i]['path']);
			$databases[$i]['writable_dir'] = is_writable(dirname($databases[$i]['path']));
			$databases[$i]['readable'] = is_readable($databases[$i]['path']);
		}
		sort($databases);
	}
	// we now have the $databases array set. Check whethet currentDB is a managed Db (is in this array)
	if(isset($_SESSION[COOKIENAME.'currentDB']) && isManagedDB($_SESSION[COOKIENAME.'currentDB']['path']) === false)
		unset($_SESSION[COOKIENAME.'currentDB']);
	
	//- Delete an existing database
	if(isset($_GET['database_delete']))
	{
		$dbpath = $_POST['database_delete'];
		// check whether $dbpath really is a db we manage
		$checkDB = isManagedDB($dbpath);
		if($checkDB !== false)
		{
			unlink($dbpath);
			unset($_SESSION[COOKIENAME.'currentDB']);
			unset($databases[$checkDB]);
		} else die($lang['err'].': '.$lang['delete_only_managed']);
	}
	
	//- Rename an existing database
	if(isset($_GET['database_rename']))
	{
		$oldpath = $_POST['oldname'];
		$newpath = $_POST['newname'];
		$oldpath_parts = pathinfo($oldpath);
		$newpath_parts = pathinfo($newpath);
		// only rename?
		$newpath = $oldpath_parts['dirname'].DIRECTORY_SEPARATOR.basename($_POST['newname']);
		if($newpath != $_POST['newname'] && $subdirectories)
		{
			// it seems that the file should not only be renamed but additionally moved.
			// we need to make sure it stays within $directory...
			$new_realpath = realpath($newpath_parts['dirname']).DIRECTORY_SEPARATOR;
			$directory_realpath = realpath($directory).DIRECTORY_SEPARATOR;
			if(strpos($new_realpath, $directory_realpath)===0)
			{
				// its okay, the new directory is within $directory
				$newpath =  $_POST['newname'];
			}
			else die($lang['err'].': '.$lang['db_moved_outside']);
		}
		
		if(checkDbName($newpath))
		{
			$checkDB = isManagedDB($oldpath);
			if($checkDB !==false )
			{
				rename($oldpath, $newpath);
				$databases[$checkDB]['path'] = $newpath;
				$databases[$checkDB]['name'] = basename($newpath);
				$_SESSION[COOKIENAME.'currentDB'] = $databases[$checkDB]; 
				$justrenamed = true;
			}
			else die($lang['err'].': '.$lang['rename_only_managed']);
		}
		else
		{
			if(is_file($newpath) || is_dir($newpath)) $dbexists = true;
			else $extension_not_allowed = true;	
		}
	}

	
	//- Export (download) an existing database
	if(isset($_POST['export']))
	{
		if($_POST['export_type']=="sql")
		{
			header('Content-Type: text/sql');
			header('Content-Disposition: attachment; filename="'.$_POST['filename'].'.'.$_POST['export_type'].'";');
			if(isset($_POST['tables']))
				$tables = $_POST['tables'];
			else
			{
				$tables = array();
				$tables[0] = $_POST['single_table'];
			}
			$drop = isset($_POST['drop']);
			$structure = isset($_POST['structure']);
			$data = isset($_POST['data']);
			$transaction = isset($_POST['transaction']);
			$comments = isset($_POST['comments']);
			$db = new Database($_SESSION[COOKIENAME.'currentDB']);
			echo $db->export_sql($tables, $drop, $structure, $data, $transaction, $comments);
		}
		else if($_POST['export_type']=="csv")
		{
			header("Content-type: application/csv");
			header('Content-Disposition: attachment; filename="'.$_POST['filename'].'.'.$_POST['export_type'].'";');
			header("Pragma: no-cache");
			header("Expires: 0");
			if(isset($_POST['tables']))
				$tables = $_POST['tables'];
			else
			{
				$tables = array();
				$tables[0] = $_POST['single_table'];
			}
			$field_terminate = $_POST['export_csv_fieldsterminated'];
			$field_enclosed = $_POST['export_csv_fieldsenclosed'];
			$field_escaped = $_POST['export_csv_fieldsescaped'];
			$null = $_POST['export_csv_replacenull'];
			$crlf = isset($_POST['export_csv_crlf']);
			$fields_in_first_row = isset($_POST['export_csv_fieldnames']);
			$db = new Database($_SESSION[COOKIENAME.'currentDB']);
			echo $db->export_csv($tables, $field_terminate, $field_enclosed, $field_escaped, $null, $crlf, $fields_in_first_row);
		}
		exit();
	}
	
	//- Import a file into an existing database
	if(isset($_POST['import']))
	{
		$db = new Database($_SESSION[COOKIENAME.'currentDB']);
		$db->registerUserFunction($custom_functions);
		if($_POST['import_type']=="sql")
		{
			$data = file_get_contents($_FILES["file"]["tmp_name"]);
			$importSuccess = $db->import_sql($data);
		}
		else
		{
			$field_terminate = $_POST['import_csv_fieldsterminated'];
			$field_enclosed = $_POST['import_csv_fieldsenclosed'];
			$field_escaped = $_POST['import_csv_fieldsescaped'];
			$null = $_POST['import_csv_replacenull'];
			$fields_in_first_row = isset($_POST['import_csv_fieldnames']);
			$importSuccess = $db->import_csv($_FILES["file"]["tmp_name"], $_POST['single_table'], $field_terminate, $field_enclosed, $field_escaped, $null, $fields_in_first_row);
		}
	}
}