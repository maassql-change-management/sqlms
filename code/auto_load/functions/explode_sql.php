<?php
//function that allows SQL delimiter to be ignored inside comments or strings
function explode_sql($delimiter, $sql)
{
	$ign = array('"' => '"', "'" => "'", "/*" => "*/", "--" => "\n"); // Ignore sequences.
	$out = array();
	$last = 0;
	$slen = strlen($sql);
	$dlen = strlen($delimiter);
	$i = 0;
	while($i < $slen)
	{
		// Split on delimiter
		if($slen - $i >= $dlen && substr($sql, $i, $dlen) == $delimiter)
		{
			array_push($out, substr($sql, $last, $i - $last));
			$last = $i + $dlen;
			$i += $dlen;
			continue;
		}
		// Eat comments and string literals
		foreach($ign as $start => $end)
		{
			$ilen = strlen($start);
			if($slen - $i >= $ilen && substr($sql, $i, $ilen) == $start)
			{
				$i+=strlen($start);
				$elen = strlen($end);
				while($i < $slen)
				{
					if($slen - $i >= $elen && substr($sql, $i, $elen) == $end)
					{
						// SQL comment characters can be escaped by doubling the character. This recognizes and skips those.
						if($start == $end && $slen - $i >= $elen*2 && substr($sql, $i, $elen*2) == $end.$end)
						{
							$i += $elen * 2;
							continue;
						}
						else
						{
							$i += $elen;
							continue 3;
						}
					}
					$i++;
				}
				continue 2;
			}		
		}
		$i++;
	}
	if($last < $slen)
		array_push($out, substr($sql, $last, $slen - $last));
	return $out;
}