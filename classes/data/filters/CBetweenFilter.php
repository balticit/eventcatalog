<?php
class CBetweenFilter extends CSQLFilter  
{
	public $lowLimit;
	public $highLimit;
	
	public function CBetweenFilter(&$field = null,$lowLimit = null,$highLimit)
	{
		$this->CSQLFilter();
		$this->field =$field;
		$this->lowLimit = $lowLimit;
		$this->highLimit = $highLimit;
	}
	
	public function ToSqlString()
	{
		$lw = $this->PrepareValue($this->lowLimit);
		$hw = $this->PrepareValue($this->highLimit);
		$name = $this->field->name;
		return " $name between $lw and $hw ";
	}
}
?>