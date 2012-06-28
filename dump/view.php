<?
//***************************************************
//	MySQL-Dumper v2.6 by Matthijs Draijer
//	
//	Use of this script is free, as long as
//	it's not commercial and you don't remove
//	my name.
//
//***************************************************

include("inc_config.php");
include("inc_functions.php");


if(in_array($_SERVER[REMOTE_ADDR], $IP)) {
	if($_REQUEST[dir] == "") {
		$dir = 'tabellen/';
	} else {
		$dir = $_REQUEST[dir];
	}

	echo "<h1><a href='?dir=tabellen/'>$dir</a></h1>\n";
	echo "<table border=0 align=center>\n";
	showDir($dir, true);
	echo "</table>";
} else {
	echo $NoAcces;
}
?>