<?php
class CCompareValidator extends CValidator 
{
	public $datakey;
	
	public $compareKey;
	
	public function CCompareValidator()
	{
		$this->CValidator();
	}
	
	protected function ValidateData($data)
	{
		if(!is_array($data))
		{
			return false;
		}
		if ((!isset($data[$this->datakey])||(!isset($data[$this->compareKey]))))
		{
			return false;
		}
		return ($data[$this->datakey]==$data[$this->compareKey]);
	}
	
	public function GetArrayInput()
	{
		return true;
	}
}
?>