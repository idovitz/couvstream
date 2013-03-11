<?php

$xml = simplexml_load_file("/etc/couvstream/config.xml");
#$xml = simplexml_load_file($_SERVER["DOCUMENT_ROOT"]."/config/config.xml");

foreach($xml->children() as $gname => $group)
{
	foreach($group->children() as $name => $element)
	{
		define($name, strval($element));
	}
}

?>
