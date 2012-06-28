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
	if($_REQUEST[path] == '') { $path = $DIR;}
	
	if(is_file($_REQUEST[DelPath]))
	{		
		if(@unlink ($_REQUEST[DelPath]))
		{
			echo "The file ". $_REQUEST[DelPath] ." is removed<br>";
		}
		else
		{
			echo "The file ". $_REQUEST[DelPath] ." couldn't be removed<br>";
		}
		
		echo "<p>\n";
	}
	elseif(is_dir($_REQUEST[DelPath]))
	{
		$path = $_REQUEST[DelPath].'/';
	}
	
	$handle_1 = opendir("$path");
	echo "<table>\n";
	while ($file_1 = readdir($handle_1))
	{		
		if ( $file_1 != "." && $file_1 != "..")
		{
			echo "<tr><td>". $file_1 ."</td><td><a href=\"?DelPath=". $path.$file_1 ."\">Delete</a><br></td></tr>\n";
		}
	}
	echo "</table>";
	closedir($handle_1);	
} else {
	echo $NoAcces;
}
?>