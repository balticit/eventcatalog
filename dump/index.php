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

if(in_array($_SERVER[REMOTE_ADDR], $IP)) {
	echo "<table width=100% height=100% border=0>";
	echo "<tr valign='center'>";
	echo "	<td align='center'>";
	echo "	<table width=50% height=33% align=center>";
	echo "	<tr valign='center'>";
	echo "		<td align='center' bgcolor='#EAEAEA'>";
	echo "		<font color='#9C0000' face='Arial'>";
	echo "		<a href='view.php'>Overview</a><br>";
	echo "		<a href='format.php'>Remove all backups</a><br>";
	echo "		<a href='delete.php'>Remove 1 backup</a><br>";
	echo "		<a href='admin.php'>Administrator area</a>";
	echo "		</font>";
	echo "		</td>";
	echo "	</tr>";
	echo "	</table>";
	echo "	</td>";
	echo "</tr>";
	echo "</table>";
} else {
	echo $NoAcces;
}

?>