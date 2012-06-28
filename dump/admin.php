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
	
// Get the list of all tables from the database '$dbName'
$tabellen		= getTableList($dbName, "");

// Get a string to show a list of all tables in the array 'tabellen'
$tabel			= setTabelList(0, $tabellen);
	
// Get a string to show a form to select from which tables there should be made a dump-file 
$formulier		= setForm(array('tonen', 'Alles'), array('true', 'true'), $tabel, "opslaan", "Maak back-up");
	
// Show the form
echo $formulier;

?>