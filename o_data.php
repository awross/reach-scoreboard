<?php
	$gtag = array();
	$f=file("old_data.txt");
	$cnt=count($f);
	for($i=0;$i<$cnt;$i++)
	{
		if(preg_match("/<a class=\"fc-gtag-link\"/", $f[$i]))
		{
				$arr=explode(">", $f[$i]);
				$name=explode("<", $arr[1]);
				array_push($gtag, $name[0]);
		}	
	}
	foreach ($gtag as $tag)
	{
		echo($tag . "\n");
	}
?>
