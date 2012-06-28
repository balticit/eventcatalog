<?php
class CDataProcessor
{
	public static function PrepareDataForXML($string)
	{
		return 	str_replace(array("<",">"),array("&lt;","&gt;"), str_replace("&","&amp;",$string));
	}
}
?>