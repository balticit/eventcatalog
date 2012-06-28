<?php
class CFullObject extends CObject
{
	private $data = array();

	private static  $data_types = array();

	private static  $data_nullable = array();

	private static  $data_size = array();
	
	private static  $data_max_value = array();

	private static $data_names = array();
	
	function  __get($param)
	{
		return (isset($this->data[$param]))?$this->data[$param]:null;
	}

	public static function GetTableStructure()
	{
		return array("data_types"=>CFullObject::$data_types,
		"data_nullable"=>CFullObject::$data_nullable,
		"data_size"=>CFullObject::$data_size,
		"data_max_value"=>CFullObject::$data_max_value,
		);
	}
	
	public function GetClassFieldsData()
	{
		return CFullObject::$data_types[$this->class];
	}
	
	function CFullObject($class)
	{
		if (!isset(CFullObject::$data_types[$class]))
		{
			CFullObject::$data_types[$class] = array();
		}
		if (!isset(CFullObject::$data_nullable[$class]))
		{
			CFullObject::$data_nullable[$class] = array();
		}
		if (!isset(CFullObject::$data_size[$class]))
		{
			CFullObject::$data_size[$class] = array();
		}
		if (!isset(CFullObject::$data_max_value[$class]))
		{
			CFullObject::$data_max_value[$class] = array();
		}
		if (!isset(CFullObject::$data_names[$class]))
		{
			CFullObject::$data_names[$class] = array();
		}
		$this->class = $class;
	}
	
	function __set($param,$value)
	{
		$this->data[$param] = $value;
		if ($param=="class")
		{
			return ;
		}
		CFullObject::$data_names[$this->class][$param] = $param;
		if (strlen($value)==0)
		{
			CFullObject::$data_nullable[$this->class][$param] = true;
			return ;
		}
		if (!isset(CFullObject::$data_nullable[$this->class][$param]))
		{
			CFullObject::$data_nullable[$this->class][$param] = false;
		}
		$type = (isset(CFullObject::$data_types[$this->class][$param]))?CFullObject::$data_types[$this->class][$param]:"int";
		$strval = $value;
		$intval = null;
		$floatval = null;
		if ((is_numeric($value))&&($type!="string"))
		{
			$strval = null;
			$floatval = floatval($value);
			$intval = intval($value);
			if (($intval!=$floatval)||($type=="float"))
			{
				$type="float";
			}
		}
		else
		{
			$type="string";
		}
		CFullObject::$data_types[$this->class][$param] = $type;
		
		if (!isset(CFullObject::$data_size[$this->class][$param]))
		{
			CFullObject::$data_size[$this->class][$param]=0;
		}
		$len = strlen($value);
		if ($len>CFullObject::$data_size[$this->class][$param])
		{
			CFullObject::$data_size[$this->class][$param]=$len;
		}
		
		if ((!isset(CFullObject::$data_max_value[$this->class][$param]))&&($type!="string"))
		{
			CFullObject::$data_max_value[$this->class][$param]=($type=="float")?$floatval:$intval;
		}
		if ($type=="float")
		{
			if (CFullObject::$data_max_value[$this->class][$param]<$floatval)
			{
				CFullObject::$data_max_value[$this->class][$param]=$floatval;
			}
		}
		if ($type=="int")
		{
			if (CFullObject::$data_max_value[$this->class][$param]<$intval)
			{
				CFullObject::$data_max_value[$this->class][$param]=$intval;
			}
		}
	}

}
?>