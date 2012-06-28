<?php
class CNativeTableBuilder extends CObject 
{
	private static $typemap = array("int"=>"int","bigint"=>"int","smallint"=>"int",
						"tinyint"=>"int","mediumint"=>"int","boolean"=>"int","bit"=>"int",
						"datetime"=>"date","date"=>"date","timestamp"=>"date","time"=>"date","year"=>"date",
						"varchar"=>"string","text"=>"","blob"=>"string","binary"=>"string",
						"char"=>"string","text"=>"string","blob"=>"string","varbinary"=>"string",
						"float"=>"float","decimal"=>"float","double"=>"float");
	
	public static function BuildTable($tableName,$exlude = array())
	{
		if (!is_array($exlude))
		{
			$exlude = array();
		}
		$natives = SQLProvider::ExecuteQuery("show columns from $tableName");
		$fields = array();
		foreach ($natives as $native) 
		{
			$native = array_change_key_case($native);
			if (array_search($native["field"],$exlude)===false)
			{	
				$type = preg_split("/\(/",$native["type"],2);
				$type = CNativeTableBuilder::$typemap[$type[0]];
				$field = new CDataField($native["field"],$type,(strtolower($native["null"])=="yes"),(!is_null($native["default"])),$native["default"],(strtolower($native["key"])=="pri"),(strtolower($native["extra"])=="auto_increment"))	;
				$field->nativeType = $native["type"];
				array_push($fields,$field);
			}
		}
		return new CDataTable($tableName,$fields);
	}
	
	public static function GetTableFields($tableName,$exlude = array())
	{
		if (!is_array($exlude))
		{
			$exlude = array();
		}
		$natives = SQLProvider::ExecuteQuery("show columns from $tableName");
		$fields = array();
		foreach ($natives as $native) 
		{
			$native = array_change_key_case($native);
			if (array_search($native["field"],$exlude)===false)
			{	
				$type = preg_split("/\(/",$native["type"],2);
				$type = CNativeTableBuilder::$typemap[$type[0]];
				$field = new CDataField($native["field"],$type,(strtolower($native["null"])=="yes"),
				                        (!is_null($native["default"])),$native["default"],
																(strtolower($native["key"])=="pri"),(strtolower($native["extra"])=="auto_increment"))	;
				$field->nativeType = $native["type"];
				if (($field->systemType=="date")&&(strlen($field->default)==0))
				{
					$field->hasDefault = false;
				}
				array_push($fields,$field);
			}
		}
		return $fields;
	}
}
?>
