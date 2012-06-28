<?
//***************************************************
//	MySQL-Dumper v2.6 by Matthijs Draijer
//	
//	Use of this script is free, as long as
//	it's not commercial and you don't remove
//	my name.
//
//***************************************************

/*
	CONNECT TO DB
*/
function connect_db()
{
	global $dbHostname, $dbUsername, $dbPassword, $dbName;
	
	$db = mysql_pconnect($dbHostname, $dbUsername, $dbPassword) OR sess_connect_error();
	@mysql_select_db( "$dbName", $db) or sess_select_db(); 
	return $db;
}

/*
	ECHOS CONNECTION ERROR
*/
function connect_error()
{
	echo "There went something wrong";
	die;
}


/*
	ECHOS CONNECTION ERROR
*/
function select_db()
{
	echo "Cannot reach the database";
	die;
}

/*
	CHECK IF '$DIR' IS A DIRECTORY. IF NOT, MAKE DIRECTORY '$DIR'
*/
function checkDir($DIR)
{
	if (!is_dir($DIR))
	{
		mkdir($DIR, 0777);
	}	
}

/*
	CHECKS IF '$file_name' EXIST. IF EXIST, FILE WILL BE DELETED OF RENAMED. 
*/
function checkFile($file_name)
{	
	if (file_exists($file_name))		// Does '$file_name' exist
	{
		unlink($file_name);		
	}
}

/*
	CHECKS THE NUMBER OF BACKUPS IN '$dir'. IF THERE ARE TOO MUCH, THE OLDEST ONES WILL BE DELETED.
*/
function checkNumberBackups($dir)
{
	global $MaxBackup;
	
	$bestanden = getBackupDates($dir);
			
	while(count($bestanden) > ($MaxBackup-1))
	{
		$bestand = array_shift($bestanden);	
		unlink($dir . $bestand);
		$bestanden = getBackupDates($dir);		
	}
}

/*
	GIVES AN OVERVIEW OF ALL FILES AND DIRECTORIES IN '$dir' WHEN '$all' = TRUE, OTHERWISE ONLY THE FIRST LEVEL OF DIRECTORIES AND FILES WILL BE SHOWN
*/
function showDir($dir, $all)
{
	$handle = opendir($dir); 
	
	while (false!==($file = readdir($handle)))
	{
		if ($file != "." && $file != "..")
		{
			$temp_dir = $dir.$file;
			if(is_dir($temp_dir.'/')) {
				echo "<tr><td colspan=6><b><a href='?dir=$temp_dir/'>$temp_dir</a></b></td></tr>\n";
				if($all)	showDir($temp_dir.'/', $all);
			} else {
				echo "<tr><td>&nbsp;</td><td><a href='$temp_dir'>$file</a></td><td>&nbsp;</td><td>". date("d-m-y H:i", filemtime($temp_dir)) ."</td><td>&nbsp;</td><td align='right'>". formatFileSize(filesize($temp_dir)) ."</td></tr>\n";
			}
		}
	}
	if($all)	echo '<tr><td colspan=6>&nbsp;</td></tr>';
	
	closedir($handle);
}

/*
	GETS AN ARRAY WITH THE LAST MODIFICATION DATES OF THE BACKUPS IN '$dir'.
*/
function getBackupDates($dir)
{
	$files = array();
	
	$handle = opendir("$dir");
	while ($file = readdir($handle))
	{		
		if ($file != "." && $file != "..")
		{
			$tijd = @fileatime($dir.$file);			
			$files[$tijd] = $file;
		}
	}
	closedir($handle);
	
	ksort($files);
		
	return $files;
}

/*
	PUTS THE TEXT '$tekst' IN THE FILE '$file_name'
*/
function setTekst($file_name, $tekst)
{
	$file = fopen($file_name,"a+");
	fwrite($file, $tekst);
	fclose($file);
}

/*
	GET A LIST OF TABLES IN DATABASE '$database' EXEPT THE TABELS LISTED IN '$NOT'
*/
function getTableList($database)
{
	global $NotTable;
	
	$db				= connect_db();
	$result			= mysql_list_tables($database, $db);
	$tabel_index	= 0;
	$max			= mysql_num_rows($result);

	for($x=0; $x < $max ; $x++) 
	{
		$tabel = mysql_tablename($result,$x);
						
		if ($tabel <> "" AND !@in_array($tabel, $NotTable))		// If the table name isn't empty and the table is not in '$NOT' than the table can be out in the list of tables
		{
			$tabel_array[] = mysql_tablename($result,$x);
		}
	}
	
	return $tabel_array;
}

/*
	RETURNS THE DEFENITION-PART (the field-names, type of the fields, length of the fields, etc.) OF THE TABLE
	'$tabel'
*/
function getDefenitie($tabel)
{
	global $DefenitieTekst;
		
	$db = connect_db();
	
	$def  = "";
	$def .= $DefenitieTekst;
	$def .= "\n";
	$def .= "DROP TABLE IF EXISTS $tabel;\n";
	$def .= "CREATE TABLE $tabel (\n";
		
	$result = mysql_query("SHOW FIELDS FROM $tabel");
	
	while($row = mysql_fetch_array($result))
	{
		$def .= "   $row[Field] $row[Type]";
		
		if ($row["Default"] != "")
		{
			$def .= " DEFAULT '$row[Default]'";
		}
		
		if ($row["Null"] != "YES")
		{
			$def .= " NOT NULL";
		}
		
		if ($row[Extra] != "")
		{
			$def .= " $row[Extra]";
		}
		
        	$def .= ",\n";
        }
        
        $def = ereg_replace(",\n$","", $def);

	$qkey = mysql_query("SHOW INDEX FROM $tabel");

	/* retrieve the key info if it exists and use it */
	$knames = array();
	if($rkey = @mysql_fetch_array($qkey))
	{
		do
		{
			/* add the key info to the arrays */
			$keys[$rkey["Key_name"]]["nonunique"] = $rkey["Non_unique"];
			if(!$rkey["Sub_part"])
			{
				$keys[$rkey["Key_name"]]["order"][$rkey["Seq_in_index"]-1] = $rkey["Column_name"];
			}
			else
			{
				$keys[$rkey["Key_name"]]["order"][$rkey["Seq_in_index"]-1] = $rkey["Column_name"]."(".$rkey["Sub_part"].")";
			}
			
			if(!in_array($rkey["Key_name"], $knames))
			{
				$knames[] = $rkey["Key_name"];
			}
			
		}
		while($rkey = @mysql_fetch_array($qkey));

		/* add the key information to the $creatinfo creation variable */
		for($kl=0; $kl<sizeof($knames); $kl++)
		{
			if($knames[$kl] == "PRIMARY")
			{
				$def .=",\n   PRIMARY KEY";
			}
			else
			{
				if($keys[$knames[$kl]]["nonunique"] == "0")
				{
					$def .= ",\n   UNIQUE ". $knames[$kl];
				}
				else
				{
					$def .= ",\n   KEY ". $knames[$kl];
				}
			}
			
			$temp = @implode(",", $keys[$knames[$kl]]["order"]);
			$def .= " (". $temp .")";
		}				
	}
	
	$def .= "\n);";
	
	$def .= "\n";
	$def .= "\n";
        
	return $def;       
}


/*
	RETURNS THE DATA-PART (the field-names and field values) OF THE TABLE '$tabel'
*/
function getData($tabel)
{
	global $DataTekst;
	
	if ($tabel > "")
	{
		$result = mysql_query("SELECT * FROM $tabel");
		
		$aantal_rij = mysql_num_rows ($result);
		
		$aantal_kolom = mysql_num_fields ($result);
		
		$data  ="";
		$data .= $DataTekst;
		$data .="\n";
		
		for ($i=0; $i < $aantal_rij; $i++)
		{
			$myrow = mysql_fetch_array($result);
								
			$data .="INSERT INTO $tabel";
			
			if ($UitgebreidInvoeren)
			{
				$data .= "(";
							
				for ($a = 0; $a < $aantal_kolom; $a++)
				{
				 	$veldnaam = mysql_field_name($result, $a);
				 	
				 	if($a == ($aantal_kolom - 1))
				 	{
				 		$data.= $veldnaam;
				 	}
				 	else 
				 	{
				 		$data.= $veldnaam.",";
				 	}
				}
				
				$data .=")";
			}
			
			$data .= " VALUES (";
			
			for ($k=0; $k < $aantal_kolom; $k++)
			{
				if($k == ($aantal_kolom - 1))
				{
					$data.="'".addslashes($myrow[$k])."'";
				}
				else
				{
					$data.="'".addslashes($myrow[$k])."',";
				}
			}
			
			$data.= ");\n"; 
		}
		
		$data.= "\n";
		
	}
	else
	{
		$data = "Error";
	}
		
	return $data;
}

/*
	RETURNS A STRING CONTAINING THE HTML-CODE OF A TABLE WITH THE DATA OF '$kolomen' IN THE COLUMS, A BORDER OF WIDTH
	'$border' AND '$kop' CENTERED IN THE FIRST ROW
*/
function setTable($border, $kop, $kolomen)
{
	$aantal_kolomen = count($kolomen);
	
	$temp  = "<center>\n";
	$temp .= "<table border=". $border .">\n";
	$temp .= "<tr>\n   <td colspan='$aantal_kolomen' align='center'>$kop</td>\n</tr>\n";
	$temp .= "<tr>\n   <td colspan='$aantal_kolomen'>&nbsp;</td>\n</tr>\n";
	
	$aantal_rijen = count($kolomen[1]);
	for($r=0; $r < $aantal_rijen; $r++)
	{
		$temp .= "<tr>\n";
		for($k=0; $k < $aantal_kolomen; $k++)
		{
			$temp .= "   <td width='". number_format(100 / $aantal_kolomen, 1) ."%'>". $kolomen[$k][$r] ."</td>\n";
		}
		$temp .= "</tr>\n";
	}
	
	$temp .= "</table>\n";
	$temp .= "</center>\n";
	
	return $temp;
}

/*
	RETURNS A STRING CONTAINING THE HTML-CODE OF A TABLE WITH A CHECKBOX IN THE FIRST COLOM, THE DATA OF '$tabellen'
	IN THE SECOND COLUM AND A BORDER OF WIDTH '$border'.
	THE CHECKBOX IN THE FIRST COLUM WILL BE CHECKED, EXEPT WHEN THE TABLE IS IN '$NOT'.
*/
function setTabelList($border, $tabellen)
{
	global $NotTable, $AdminCheck;
	
	$temp  = "<center>\n";	
	$temp .= "<table border=$border>\n";
	
	foreach ($tabellen as $tabel)
	{
		$temp .= "<tr>\n";
		$temp .= "   <td><input type='checkbox' name='tabel[$tabel] value='1' ". ((!in_array($tabel, $NotTable) AND $AdminCheck) ? ' checked' : '') ."></td>\n";
		$temp .= "   <td>". $tabel ."</td>\n";
		$temp .= "</tr>\n";
	}
	
	$temp .= "</table>\n";
	$temp .= "</center>\n";
	
	return $temp;
}

/*
	RETURNS THE HTML-CODE FOR A FORM IN A TABLE, WITH THE DATA OF '$HiddenName' AND '$HiddenValue' RESPECTIVELY AS
	HIDDEN NAMES AND HIDDEN VALUES OF THE FORM.
	THE STRING '$string' WILL BE PUT IN THE TABLE AND IN TEH LAST ROW A SUBMIT-BUTTON WILL BE MADE WITH VALUE
	'$Opschrift' AND NAME '$VariabeleNaam'. 
*/
function setForm($HiddenName, $HiddenValue, $string, $VariabeleNaam, $Opschrift)
{
	$temp  = "<center>\n";
	$temp .= "<form method='post' action='backup.php'>\n";
	
	$aantalHiddenName = count($HiddenName);
	
	for($q=0; $q < $aantalHiddenName; $q++)
	{
		$temp .= "<input type='hidden' name='". $HiddenName[$q] ."' value='". $HiddenValue[$q] ."'>\n";
	}
		
	$temp .= "<table>\n";
	$temp .= "<tr>\n";
	$temp .= "   <td>\n";
	
	$temp .= $string;
	
	$temp .= "   </td>\n";
	$temp .= "</tr>\n";
	$temp .= "<tr>\n";
	$temp .= "   <td>&nbsp;</td>\n";
	$temp .= "</tr>\n";
	$temp .= "<tr>\n";
	$temp .= "   <td><center><input type='Submit' name='". $VariabeleNaam ."' value='". $Opschrift ."'></center></td>\n";
	$temp .= "</tr>\n";
	$temp .= "</table>\n";
	$temp .= "</form>\n";
	$temp .= "</center>\n";
	
	echo $temp;
}

/*
	RETURNS THE TIME (Unix) WHEN THE TABLE '$table' IS UPDATED FOR THE LAST TIME
*/
function getTableStatus($table)
{
	global $dbName;
	
	$db	= connect_db();
	$result	= mysql_query("SHOW TABLE STATUS FROM $dbName LIKE '$table'", $db);
	
	if($row = mysql_fetch_array($result))
	{		
		$lastChange = strtotime($row['Update_time']);
	}
	else
	{
		$lastChange = 0;
	}
	
	return $lastChange;
	
}

/*
	RETURNS THE FILESIZE of '$size' in kB, MB or GB
*/
function formatFileSize($size)
{
	$file_size = $size;
	$i=0;
	
	$name = array('byte','kB', 'MB', 'GB');
	
	while($file_size > 1000)
	{
		$i++;
		$file_size = $file_size/1024;
	}
	
	return round($file_size,2) ." ". $name[$i];

}

?>