<?php
function pla_autoload($classname)
{
	$classfile = __DIR__ . '/classes/' . $classname . '.php';

	if (is_readable($classfile)) {
		include $classfile;
		return true;
	}
	return false;
}