<?php
class CValidator extends CObject 
{
	public $validationType;
	
	public $message = "";
	
	public $alias = "";
	
	public $formatError = true;
	
	public function CValidator()
	{
		$this->CObject();
	}
	
	protected function ValidateData($data)
	{
		return true;
	}
	
	public function Validate($data)
	{
		if ($this->ValidateData($data)===false)
		{
			return  $this->GetErrorText();
		}
		return true;
	}
	
	public function ValidateNoMessage(&$data)
	{
		return $this->ValidateData($data);
	}
	
	public function ValidateSimple(&$data,&$error)
	{
		if ($this->ValidateData($data)===false)
		{
			$error = $this->GetErrorText();
			return false;
		}
		return true;
	}
	
	public function GetErrorText()
	{
		return  ($this->formatError)?CStringFormatter::Format($this->message,array("alias"=>$this->alias)):$this->message;
	}
	
	public function GetArrayInput()
	{
		return false;
	}
}
?>