<?php
class CIsNullFilter extends CSQLFilter 
{ 
	public function CIsNullFilter(&$field)
	{	
		$this->CSQLFilter();
		$this->field = &$field;	
	}
	
	public function ToSqlString()
	{
		$name = $this->field->name;
		return " $name is null ";
	}
}
?>