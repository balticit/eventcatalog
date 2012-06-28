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
require_once('pclzip.lib.php');
setlocale (LC_TIME, $Taal);
$x=0;

if(in_array($_SERVER[REMOTE_ADDR], $IP)) {
	// When there's nothing given about showing the results on the screen or not, the value of '$TonenDefault' (from the config-file) will be taken
	if($_POST[tonen] == '') {
		$tonen = $TonenDefault;
	} else {
		$tonen = $_POST[tonen];
	}
	
	if(!is_array($_POST[tabel])) {
		// Get the list of tables in the database '$dbName' and remove the tables from '$NotTable' of that list
		$tabellen = getTableList($dbName);
	} else {
		foreach($_POST[tabel] as $key => $value) $tabellen[] = $key;
	}
	
	foreach ($tabellen as $tabel) {
		if($tabel != "") {		
			// Generate a filename for the dump-file and the ZIP-file
			$file_name		= $DIR . $tabel .'/'. $tabel . $extensie;
			$ZIPfile_name	= $DIR . $tabel .'/'. strftime("%A_%d_%B_%G") . '.zip';
				
			// Check if the directory to store the dump-files exist
			checkDir($DIR);
			checkDir($DIR . $tabel);		
			
			// Check to see if it's necessary to make a dump-file at this moment
			$Files				= getBackupDates($DIR . $tabel. '/');
			$temp				= each($Files);
			$FileChangeDate		= $temp[0];
			$TableChangeDate	= getTableStatus($tabel);
	
			if($FileChangeDate < $TableChangeDate OR $_POST[Alles] == 'true') {
				// Check if an old back-up should be removed
				checkNumberBackups($DIR . $tabel.'/');
				
				// Check if the dump-file already exist
				checkFile($file_name);
				
				// Write the header to the dump-file
				setTekst($file_name, $KopTekst);
					
				// Write the structure of the table to the dump-file (if selected so in the config-file)
				if($StructuurSchrijven) { $defenitie = getDefenitie($tabel); setTekst($file_name, $defenitie); }
						
				// Write the data of the table to the dump-file (if selected so in the config-file)
				if($DataSchrijven) { $data = getData($tabel); setTekst($file_name, $data); }
				
				// Write the footer to the dump-file
				setTekst($file_name, $Footer);
				
				// Get the filesize of the dump-file  
				$grootte = filesize($file_name) / 1024;
				
				//Maak ZIP-file
				$archive	= new PclZip($ZIPfile_name);
				$v_list		= $archive->add($file_name);
				unlink($file_name);
				
				// Put filenames and sizes in an array
				if($tonen) { $kolomen[0][$x] = $tabel; $kolomen[1][$x] = number_format($grootte,1) ." Kb"; $x++;}
			} else {
				// Put filenames and 'no backup' in an array
				if($tonen) { $kolomen[0][$x] = $tabel; $kolomen[1][$x] = "No back-up"; $kolomen[2][$x] =  date("d M H:i:s", fileatime($file_name)); $x++;}
			}
		}	
	}
	
	// Show the results on the screen (is selected so)
	if($tonen) { echo setTable(0, "Tables from <b>". ucfirst($dbName) ."</b>", $kolomen); }
} else {
	echo $NoAcces;
}
?>