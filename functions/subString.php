<?php
// reduce string chars
function subString($str)
{
	global $charsNum;
	if($charsNum > 10){
		if(strlen($str)>$charsNum) $str = substr($str, 0, $charsNum).'...';
	}
	return $str;
}