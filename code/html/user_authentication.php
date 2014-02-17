<?php

//- Check user authentication, login and logout
$auth = new Authorization(); //create authorization object

// check if user has attempted to log out
if (isset($_POST['logout']))
	$auth->revoke();
// check if user has attempted to log in
else if (isset($_POST['login']) && isset($_POST['password']))
	$auth->attemptGrant($_POST['password'], isset($_POST['remember']));

