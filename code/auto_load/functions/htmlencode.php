<?php
// function to encode value into HTML just like htmlentities, but with adjusted default settings
function htmlencode($value, $flags=ENT_QUOTES, $encoding ="UTF-8")
{
	return htmlentities($value, $flags, $encoding);
}