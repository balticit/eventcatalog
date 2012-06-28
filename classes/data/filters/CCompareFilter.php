<?php
class CCompareFilter extends CSQLFilter  
{
	protected $operator;

	
	public $value;
	
	public function CCompareFilter()
	{
		$this->CSQLFilter();
	}
	
	public function PreSet()
	{
		
	}
	

	
	public function ToSqlString()
	{
		$this->PreSet();
		$value = $this->PrepareValue($this->value);
		$name = $this->field->name;
		return " $name ".$this->operator." $value ";
	}
}
?>