<?php
class CDataRow extends CObject
{
	private $fields;
	private $values = array();
	private $modified = array();

	public function CDataRow(&$fields,$values,$empty = false,$ekeys = null)
	{
		$this->fields = $fields;
		if (!is_array($ekeys))
		{
			$fkeys = array_keys($fields);
			$vkeys = array_keys($values);
			$ekeys =  array_intersect($fkeys,$vkeys);
		}
		if (!$empty)
		{
			foreach ($ekeys as $ekey)
			{
				$this->values[$ekey] = $values[$ekey];
			}
		}
	}

	public function GetModified()
	{
		return array_keys($this->modified);
	}
	
	public function IsModified()
	{
		return sizeof($this->modified)>0;
	}
	
	public function GetData()
	{
		return $this->values;
	}
	
	public function RawSet($name,$value)
	{
		$this->values[$name] = $value;
	}
	
	public function __set($name,$value)
	{
		if (array_key_exists($name,$this->fields))
		{
			if ($this->fields[$name]->IsValid($value))
			{
				if (!isset($this->values[$name]))
				{
					$this->modified[$name] = true;
				}
				elseif ($this->values[$name]!=$value)
				{
					$this->modified[$name] = true;
				}
				$this->values[$name] = $value;
			}
		}
	}
	
	public function __get($name)
	{
		if (array_key_exists($name,$this->fields))
		{
			return (array_key_exists($name,$this->values))?$this->values[$name]:null;
		}
	}


}
?>