<?php
class CHashMapValidator extends CPageObject
{
	public $validationItems = array();

	public $messages = array();

	public function CHashMapValidator()
	{
		$this->CPageObject();
	}

	public function Validate(&$data,$leaveOnFirst = false,$returnAssoc = false)
	{
		$errors = array();
		$arr = array();
		if (is_array($data))
		{
			$arr = $data;
		}
		if (method_exists($data,"ToHashMap"))
		{
			$arr = $data->ToHashMap();
		}
		$dkeys = array_keys($this->validationItems);
		
		$error = "";
		foreach ($dkeys as $dkey)
		{
			$this->validationItems[$dkey]->message = $this->messages[$this->validationItems[$dkey]->messageId];
			if ($this->validationItems[$dkey]->ValidateSimple(&$arr,&$error)===false)
			{
				array_push($errors,$error);
				if ($leaveOnFirst)
				{
					return $errors;
				}
			}
		}
		return $errors;
	}
}
?>