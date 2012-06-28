<?php
class CEqFilter extends CCompareFilter 
{
	protected $operator = "=";
	
	public function CEqFilter(&$field = null,$value = null)
	{
		$this->CCompareFilter();
		$this->field =$field;
		$this->value = $value;
	}
}
?>