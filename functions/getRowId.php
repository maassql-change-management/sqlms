<?php
function getRowId($table, $where=''){
	global $db;
	$query = "SELECT ROWID FROM ".$db->quote_id($table).$where;
	$result = $db->selectArray($query);
	return $result;
}