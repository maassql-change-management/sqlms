<?php

// from a typename of a colun, get the type of the column's affinty
// see http://www.sqlite.org/datatype3.html section 2.1 for rules
function get_type_affinity($type)
{
	if (preg_match("/INT/i", $type))
		return "INTEGER";
	else if (preg_match("/(?:CHAR|CLOB|TEXT)/i", $type))
		return "TEXT";
	else if (preg_match("/BLOB/i", $type) || $type=="")
		return "NONE";
	else if (preg_match("/(?:REAL|FLOA|DOUB)/i", $type))
		return "REAL";
	else
		return "NUMERIC";
}