<?php
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//there is no reason for the average user to edit anything below this comment
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!


// include default configuration and language
include './phpliteadmin.config.sample.php';
include './languages/lang_en.php';

// load language file
if($language != 'en') {
	if(is_file('languages/lang_'.$language.'.php'))
		include('languages/lang_'.$language.'.php');
	elseif(is_file('lang_'.$language.'.php'))
		include('lang_'.$language.'.php');
}



//------------------------------------------------------------------------------------------
//- Initialization - BEGIN

// load optional configuration file
$config_filename = './phpliteadmin.config.php';
if (is_readable($config_filename)) {
	include_once $config_filename;
}


//constants 1
define("PROJECT", "phpLiteAdmin");
define("VERSION", "1.9.5");
define("PAGE", basename(__FILE__));
define("FORCETYPE", false); //force the extension that will be used (set to false in almost all circumstances except debugging)
define("SYSTEMPASSWORD", $password); // Makes things easier.
define('PROJECT_URL','http://phpliteadmin.googlecode.com');
define('PROJECT_BUGTRACKER_LINK','<a href="http://code.google.com/p/phpliteadmin/issues/list" target="_blank">http://code.google.com/p/phpliteadmin/issues/list</a>');

define('PATH_SITE_ROOT', __DIR__)
include join_paths(PATH_SITE_ROOT, code, 'code.php');



// Resource output (css and javascript files)
// we get out of the main code as soon as possible, without inizializing the session
if (isset($_GET['resource'])) {
	Resources::output($_GET['resource']);
	exit();
}


// don't mess with this - required for the login session
ini_set('session.cookie_httponly', '1');
session_start();

if($debug==true)
{
	ini_set("display_errors", 1);
	error_reporting(E_STRICT | E_ALL);
} else
{
	@ini_set("display_errors", 0);
}


// start the timer to record page load time
$pageTimer = new MicroTimer();





// version-number added so after updating, old session-data is not used anylonger
// cookies names cannot contain symbols, except underscores
define("COOKIENAME", preg_replace('/[^a-zA-Z0-9_]/', '_', $cookie_name . '_' . VERSION) );


// stripslashes if MAGIC QUOTES is turned on
// This is only a workaround. Please better turn off magic quotes!
// This code is from http://php.net/manual/en/security.magicquotes.disabling.php
if (get_magic_quotes_gpc()) {
	$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
	while (list($key, $val) = each($process)) {
		foreach ($val as $k => $v) {
			unset($process[$key][$k]);
			if (is_array($v)) {
				$process[$key][stripslashes($k)] = $v;
				$process[] = &$process[$key][stripslashes($k)];
			} else {
				$process[$key][stripslashes($k)] = stripslashes($v);
			}
		}
	}
	unset($process);
}


//data types array
$sqlite_datatypes = array("INTEGER", "REAL", "TEXT", "BLOB","NUMERIC","BOOLEAN","DATETIME");


//available SQLite functions array (don't add anything here or there will be problems)
$sqlite_functions = array("abs", "hex", "length", "lower", "ltrim", "random", "round", "rtrim", "trim", "typeof", "upper");


//- Initialization - END
//------------------------------------------------------------------------------------------






//- Support functions


include './server_side/user_authentication.php';


include './server_side/actions_on_database_files_and_bulk_data.php';





















//-----------------------------------------------------------------------------------------------------------------------------------
//- HTML: output starts here
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<!-- Copyright <?php echo date("Y").' '.PROJECT.' ('.PROJECT_URL.')'; ?> -->
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
<link rel="shortcut icon" href="?resource=favicon" />
<title><?php echo PROJECT ?></title>

<?php


include './html/themes.php';


include './html/output_help_screen_then_exit.php';



//- Javascript include
?>
<!-- JavaScript Support -->
<script type='text/javascript' src='?resource=javascript'></script>
</head>







<body style="direction:<?php echo $lang['direction']; ?>;">
<?php
if(ini_get("register_globals") == "on" || ini_get("register_globals")=="1") //check whether register_globals is turned on - if it is, we need to not continue
{
	echo "<div class='confirm' style='margin:20px;'>".$lang['bad_php_directive']."</div>";
	echo "</body></html>";
	exit();
}


include './html/login_screen_if_not_authorized_exit.php';


//- User is authorized, display the main application


include './html/select_database.php';


include './html/switch_to_a_different_db_with_drop_down_menu.php';


if(isset($_SESSION[COOKIENAME.'currentDB']) && in_array($_SESSION[COOKIENAME.'currentDB'], $databases))
	$currentDB = $_SESSION[COOKIENAME.'currentDB'];

//- Open database (creates a Database object)
$db = new Database($currentDB); //create the Database object
$db->registerUserFunction($custom_functions);

// collect parameters early, just once
$target_table = isset($_GET['table']) ? $_GET['table'] : null;


include './html/operations_without_output.php';


// are we working on a view? let's check once here
$target_table_type = $target_table ? $db->getTypeOfTable($target_table) : null;


include './html/table_list.php';


include './html/form_to_create_a_new_database.php';


include './html/breadcrumb_navigation.php';


include './html/confirmation_panel.php';


include './html/operations_with_output.php';


$view = "structure";


include './html/tabs_for_databases.php';


include './html/page_footer.php';


//- End of main code
