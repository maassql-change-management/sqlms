<?php
// 22 August 2011: gkf added this function to support display of
//                 default values in the form used to INSERT new data.
function deQuoteSQL($s)
{
	return trim(trim($s), "'");
}