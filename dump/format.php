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
	$path = $DIR;
	
	$handle = opendir($path);
	while ($dir = readdir($handle))
	{
		if ( $dir != "." && $dir != "..")
		{
			$handle2 = opendir($path . $dir);	
			while ($file = readdir($handle2))
			{
				if ( $file != "." && $file != "..")
				{
					unlink($path.$dir."/".$file);
					echo "<i>$path"."$dir/$file</i> verwijderd<br>";
				}
			}	
			closedir($handle2);
			
			rmdir($path.$dir);
			echo "<b>$path"."$dir</b> verwijderd<br>";
		}
	}
	closedir($handle);	
} else {
	echo $NoAcces;
}
?>