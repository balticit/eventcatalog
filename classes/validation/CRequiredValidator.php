<?php
class CRequiredValidator extends CValidator 
{
	public function CRequiredValidator()
	{
		$this->CValidator();
	}
	
	protected function ValidateData($data)
	{
		return (strlen($data)>0);
	}
	
}
?>