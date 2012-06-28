<?php
class SQLProvider
{
	private static $dbLink;
	private static $lastRowsAffected;


	public static function ReopenConnection()
	{
		if (!is_null(SQLProvider::$dbLink))
		{
			SQLProvider::CloseConnection();
			SQLProvider::OpenConnection();
		}
	}

	public static function OpenConnection()
	{
		if (is_null(SQLProvider::$dbLink))
		{
			SQLProvider::$dbLink = mysql_connect(MYSQL_HOST.":".MYSQL_PORT,MYSQL_USER,MYSQL_PASSWORD) or die("Could not connect: " . mysql_error());;
			mysql_select_db(MYSQL_DATABASE,SQLProvider::$dbLink)or die ('Can\'t use db : ' . mysql_error());
			SQLProvider::ExecuteNonReturnQuery("SET CHARACTER SET ".MYSQL_CHARSET);
			SQLProvider::ExecuteNonReturnQuery("SET NAMES ".MYSQL_CHARSET);
			SQLProvider::ExecuteNonReturnQuery("SET max_sort_length = 2000;");
		}
	}

	public static function CloseConnection()
	{
		mysql_close(SQLProvider::$dbLink);
	}

	public static function ExecuteQueryReverse($query,$resultType = MYSQL_ASSOC)
	{
		SQLProvider::OpenConnection();
		$result = mysql_query($query,SQLProvider::$dbLink) or die("Invalid query: $query" . mysql_error());
		$retrows = array();
		$i=0;
		while ($row=mysql_fetch_array($result,$resultType))
		{
			foreach ($row as $key=>$value)
			{
				$retrows[$key][$i]=$value;
			}
			$i++;
		}
		SQLProvider::$lastRowsAffected=$i;
		return $retrows;
	}

	public static function ExecuteQuery($query,$resultType = MYSQL_ASSOC)
	{
		SQLProvider::OpenConnection();
		$result = mysql_query($query,SQLProvider::$dbLink) or die("Invalid query: $query error: " . mysql_error());
		$retrows = array();
		$i=0;
		while ($row=mysql_fetch_array($result,$resultType))
		{
			$retrows[$i] = $row;
			$i++;
		}
		SQLProvider::$lastRowsAffected=$i;
		return $retrows;
	}
	
	public static function ExecuteScalar($query)
	{
		SQLProvider::OpenConnection();
		$result = mysql_query($query,SQLProvider::$dbLink) or die("Invalid query: $query error: " . mysql_error());
    if (mysql_num_rows($result)==0)
      return null;
    else  
		  return mysql_result($result,0);
	}
	
	public static function ExecuteQueryIndexed($query,$key,$resultType = MYSQL_ASSOC)
	{
		SQLProvider::OpenConnection();
		$result = mysql_query($query,SQLProvider::$dbLink) or die("Invalid query: $query error: " . mysql_error());
		$retrows = array();
		$i=0;
		while ($row=mysql_fetch_array($result,$resultType))
		{
			$retrows[$row[$key]] = $row;
			$i++;
		}
		SQLProvider::$lastRowsAffected=$i;
		return $retrows;
	}
	
	public static function ExecuteNonReturnQuery($query)
	{
		SQLProvider::OpenConnection();
		mysql_query($query,SQLProvider::$dbLink) or die("Invalid query: '$query'" . mysql_error());
		SQLProvider::$lastRowsAffected = mysql_affected_rows(SQLProvider::$dbLink);
	}

	public static function ExecuteIdentityInsert($query)
	{
		SQLProvider::OpenConnection();
		mysql_query($query,SQLProvider::$dbLink) or die("Invalid query: '$query'" . mysql_error());
		SQLProvider::$lastRowsAffected = mysql_affected_rows(SQLProvider::$dbLink);
		return mysql_insert_id(SQLProvider::$dbLink);
	}
	
	public static function GetLastAffectedRows()
	{
		return SQLProvider::$lastRowsAffected;
	}
}
?>
