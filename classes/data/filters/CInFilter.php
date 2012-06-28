<?php
class CInFilter extends CSQLFilter  
{
	public $values = array();
	
	public function CInFilter(&$field = null,$values = array())
	{
		$this->CSQLFilter();
		$this->field =$field;
		$this->values = $values;
	}
	
	public function ToSqlString()
	{
		$vkeys = array_keys($this->values);
		$filter = "(";
		foreach ($vkeys as $key)
		{
			$filter.=$this->PrepareValue($this->values[$key]).",";
		}
		$filter = substr($filter,0,strlen($filter)-1).")";
		$name = $this->field->name;
		return " $name in $filter ";
	}
}
?>