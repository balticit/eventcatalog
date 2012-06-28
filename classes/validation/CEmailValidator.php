<?php
class CEmailValidator extends CValidator 
{
	public function CEmailValidator()
	{
		$this->CValidator();
	}
	
	protected function ValidateData($data)
	{
		return (preg_match("/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/",$data)==1);
	}
}
?>