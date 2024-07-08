<?php

// Hilfsroutinen die das Leben erleichtern sollen



// Alle Parameter aus dem ursprünglichen Aufruf rausfieseln und wieder neu zusammensetzen
// Optional: Ein Array mit Ausnahmen angeben, die dann bei dem Vorgang ausgelassen werden 

function JoinGet($Ignore = array())
{
	$result = '';
	$Get = $_GET;
	
	if (!empty($Get))
	{
		// printpre ($Get);
		foreach($Get as $key => $value)
		{
			if( !in_array($key, $Ignore) )
				$result .= (empty($result) ? '?' : '&').$key.'='.$value;
		}
	}
	
	return $result;
}
	
?>
