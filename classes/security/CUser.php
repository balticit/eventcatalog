<?php
class CUser extends CObject 
{
	public $id;
	
	public $name;
	
	public $type;
	
	public $authorized;
	
	public function CUser()
	{
		$this->CObject();
	}
	
	public function GetTable()
	{
		if (!$this->authorized)
		{
			return "";
		}
		if ($this->type=="user")
		{
			return "tbl__registered_user";
		}
		return "tbl__".$this->type."_doc";
	}
}
?>