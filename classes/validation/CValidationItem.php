<?php
class CValidationItem  extends CValidator implements IPropertyOverloaded 
{
	public $validationClass = "";

	public $messageId = null;

	public $datakey;

	//public $arrayInput = false;
	
	public $canOmit = false;
	
	private $addParams = array();
	
	
	public function CValidationItem()
	{
		$this->CValidator();
	}

	protected function ValidateData(&$data)
	{
		$val = CClassFactory::CreateClassIntance($this->validationClass,array_merge(array("messageId"=>$this->messageId,
		                                                                                  "datakey"=>$this->datakey,
																						  "compareKey"=>$this->compareKey,
																						  "canOmit"=>$this->canOmit),$this->addParams));
		if ((!isset($data[$this->datakey]))&&(!$this->canOmit))
		{
			return false;
		}
		$value = null;
		if ($val->GetArrayInput())
		{
			$value = &$data;
		}
		else 
		{
			$value = &$data[$this->datakey];
		}
		return method_exists($val,"ValidateNoMessage")?$val->ValidateNoMessage($value):false;		
	}
	
	public function __get($name)
	{
		return (isset($this->addParams[$name]))?$this->addParams[$name]:null;
	}
	
	public function __set($name,$value)
	{
		$this->addParams[$name] = $value;
	}
}
?>