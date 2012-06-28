<?
//***************************************************
//	MySQL-Dumper v2.6 by Matthijs Draijer
//	
//	Use of this script is free, as long as
//	it's not commercial and you don't remove
//	my name.
//
//***************************************************

$dbHostname			= "baze.eventcatalog.ru.postman.ru:64000";				// The hostname
$dbUsername			= "eventcatalog_ru";				// Your username to access the database
$dbPassword			= "iCdpkkOP";				// Your password to access the database
$dbName				= "eventcatalog_ru";				// The name of the database

$SoftwareTitle		= "MySQL-Dumper";	// Name of the script
$Version			= "2.6";			// Version of the script

$UitgebreidInvoeren	= false;			// 'false' = print only the values of the table-fields in the dump-file, 'true' = print also the table-names in the dump-file
$StructuurSchrijven	= true;				// Do you want the structure of the table in the dump-file (true = yes, false = no)
$DataSchrijven		= true;				// Do you want the data of the table in the dump-file (true = yes, false = no)
$TonenDefault		= false;			// Do you want to see every the results/statics of the dump-session (true = yes, false = no)
$AdminCheck			= false;			// Do you want all table's to be checked in admin.php (true = yes, false = no)

$NotTable 			= array();			// To save time and space, tables in this array will not be backuped automatically
										// So enter the names of the tables you don't use often and which shouldn't be beackuped

$DIR				= "tabellen/";		// the dir where the dump-files will be stored
$extensie			= ".sql";			// the extention of the dump-files
$MaxBackup			= 5;				// Maximum number of stored backups of a table 
$Taal				= 'nl_NL';			// Which language do you want to use. See http://www.unicode.org/onlinedat/countries.html for names
$IP					= array('','');		// Whate are te IP's which should get acces to the files ?

// The header of every dump-file
$KopTekst 	 = "#\n";
$KopTekst  	.= "# Generated by : ". $SoftwareTitle ."\n";
$KopTekst 	.= "# Version ". $Version ."\n";
$KopTekst 	.= "#\n";
$KopTekst 	.= "# Host            : ". $dbHostname ."\n";
$KopTekst 	.= "# Generation time : ". date ("d M Y \o\m H:i:s",time()). "\n";
$KopTekst 	.= "# Server version  : ". php_uname() ."\n";
$KopTekst 	.= "# PHP Version     : ". phpversion() ."\n";
$KopTekst 	.= "# Database        : ". $dbName ."\n";
$KopTekst 	.= "# Script owner    : ". get_current_user() ."\n";
$KopTekst 	.= "#\n";
$KopTekst 	.= "# --------------------------------------------------------\n";
$KopTekst 	.= "\n";

// The text in the dump-file on top of the table-strucure
$DefenitieTekst	 = "#\n";
$DefenitieTekst	.= "# Table structure of table `". $tabel ."`\n";
$DefenitieTekst	.= "#\n";

// The text in the dump-file on top of the table-data
$DataTekst	 = "#\n";
$DataTekst	.= "# Table data of table `". $tabel ."`\n";
$DataTekst	.= "#\n";

// The footer of every dump-file
$Footer		 = "# Made with ". $SoftwareTitle ." v". $Version ."\n";
$Footer		.= "# (c) Matthijs Draijer, 2002-". date("Y") ."\n";
$Footer		.= "# ICQ# 46739124\n";
  
$NoAcces = "<table width=100% height=100% border=0>";
$NoAcces .= "<tr valign='center'>";
$NoAcces .= "	<td align='center'>";
$NoAcces .= "	<table width=50% height=33% align=center>";
$NoAcces .= "	<tr valign='center'>";
$NoAcces .= "		<td align='center' bgcolor='#EAEAEA'>";
$NoAcces .= "		<font color='#9C0000' face='Arial'><b>$SoftwareTitle v$Version</b><br>You don't have permission to enter this area</font>";
$NoAcces .= "		</td>";
$NoAcces .= "	</tr>";
$NoAcces .= "	</table>";
$NoAcces .= "	</td>";
$NoAcces .= "</tr>";
$NoAcces .= "</table>";


    	
?>