<?php
class CLessFilter extends CCompareFilter 
{
	protected $operator = "<";
	
	public $equal;
	
	public function CLessFilter(&$field = null,$value = null,$equal=false)
	{
		$this->CCompareFilter();
		$this->field =$field;
		$this->value = $value;
		$this->equal = $equal;
	}
	
	public function PreSet()
	{
		$this->operator = ($this->equal)?"<=":"<";
	}
}
?>