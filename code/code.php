<?php
define('PATH_SITE_CODE', join_paths(__DIR__, 'code'))
define('PATH_SITE_CODE_DOCS', join_paths(PATH_SITE_CODE, 'docs'))
define('PATH_SITE_CODE_HTML', join_paths(PATH_SITE_CODE, 'html'))
define('PATH_SITE_CODE_LANGUAGE', join_paths(PATH_SITE_CODE, 'languages'))

function inc_doc($name)
{
    return( join_paths(PATH_SITE_CODE_DOCS, $name ));
}

function inc_html($name)
{
    return( join_paths(PATH_SITE_CODE_HTML, $name . '.php'));
}

function inc_language($name)
{
    return( join_paths(PATH_SITE_CODE_LANGUAGE, $name . '.php'));
}