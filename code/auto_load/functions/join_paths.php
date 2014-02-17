<?php
// http://stackoverflow.com/questions/1091107/how-to-join-filesystem-path-strings-in-php
// echo join_paths(array('my/path', 'is', '/an/array'));
//or
// echo join_paths('my/paths/', '/are/', 'a/r/g/u/m/e/n/t/s/');
function join_paths() {
    $args = func_get_args();
    $paths = array();
    foreach ($args as $arg) {
        $paths = array_merge($paths, (array)$arg);
    }

    $paths = array_map(create_function('$p', 'return trim($p, "/");'), $paths);
    $paths = array_filter($paths);
    return join('/', $paths);
}