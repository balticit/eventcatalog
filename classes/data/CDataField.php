<?php
class CDataField extends CObject
{
	public static $systemTypes = array("int"=>false,"float"=>false,"string"=>true,"date"=>true);

	public $name = "";
	public $needsApos = false;
	public $primary = false;
	public $autoInc = false;
	public $nativeType = "";
	public $nullable = false;
	public $systemType;
	public $default = null;
	public $hasDefault = false;
	
	public function CDataField($name,$systemType,$nullable = false,$hasDefault = false, $default=null ,$primary = false,$autoInc = false)
	{
		$this->CObject();
		$this->name = $name;
		$this->systemType = strtolower($systemType);
		$this->needsApos= array_key_exists($this->systemType,CDataField::$systemTypes)?CDataField::$systemTypes[$this->systemType]:die("unknown data type");
		$this->nullable = $nullable;
		$this->primary = $primary;
		$this->autoInc = $autoInc;
		$this->default = $default;
		$this->hasDefault = $hasDefault;
	}

	public function IsValid($data)
	{
		if (is_null($data))
		{
			return $this->nullable;
		}
		switch ($this->systemType) {
			case "int":
			{
				if (is_int($data))
				{
					return true;
				}
				if (is_numeric($data))
				{
					return intval($data)==floatval($data);
				}
				return false;
			}
			break;
			case "float":
			{
				if (is_double($data)||is_float($data))
				{
					return true;
				}
				return is_numeric($data);
			}
			break;
			case "string":
			{
				return is_string($data);
			}
			case "date":
			{
				return strtotime($data)!=-1;
			}
			break;
		}
		return false;
	}
}
?>