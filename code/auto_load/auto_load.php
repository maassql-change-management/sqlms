<?php
// setup class autoloading
// http://us1.php.net/spl_autoload_register

define('PATH_SITE_CODE_CLASSES', join_paths(PATH_SITE_CODE, 'classes'))
function auto_load_function_classes($classname)
{
    $classfile = join_paths(PATH_SITE_CODE_CLASSES , $classname . '.php');

    if (is_readable($classfile)) {
        include $classfile;
        return true;
    }
    return false;
}
spl_autoload_register('auto_load_function_classes');





define('PATH_SITE_CODE_FUNCTIONS', join_paths(PATH_SITE_CODE, 'functions'))
function auto_load_function_functions($classname)
{
    $classfile = join_paths(PATH_SITE_CODE_FUNCTIONS , $classname . '.php');

    if (is_readable($classfile)) {
        include $classfile;
        return true;
    }
    return false;
}
spl_autoload_register('auto_load_function_functions');





define('PATH_SITE_CODE_VENDOR', join_paths(PATH_SITE_CODE, 'vendor'))
function auto_load_function_vendor($classname)
{
    $classfile = join_paths(PATH_SITE_CODE_VENDOR , $classname . '.php');

    if (is_readable($classfile)) {
        include $classfile;
        return true;
    }
    return false;
}
spl_autoload_register('auto_load_function_vendor');

