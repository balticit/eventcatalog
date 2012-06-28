<?php
class CSessionUser extends CUser
{
	public $admintype;
	
	public function CSessionUser($type)
	{
		$this->CUser();
		$this->type = $type;
		$this->admintype = 0;
		$this->Load();
	}

	private function ValidateSession()
	{
		if (!isset($_SESSION[SESSION_USER_STORAGE]) ||
        !is_array($_SESSION[SESSION_USER_STORAGE]))
			$_SESSION[SESSION_USER_STORAGE] = array();
	}

	public function Save()
	{
		$this->ValidateSession();
		$_SESSION[SESSION_USER_STORAGE][$this->type] = $this->ToHashMap();
	}

	public function Load()
	{
		$this->ValidateSession();
		$reset=true;
		if (isset($_SESSION[SESSION_USER_STORAGE][$this->type]))
		{
			if ($_SESSION[SESSION_USER_STORAGE][$this->type]["type"]==$this->type)
			{
				$reset=false;
			}
		}
		if ($reset)
		{
			$tUser = new CUser();
			$tUser->type=$this->type;
			$_SESSION[SESSION_USER_STORAGE][$this->type]=$tUser->ToHashMap();
		}
		$this->FromHashMap($_SESSION[SESSION_USER_STORAGE][$this->type]);
	}
	
	public function Clear()
	{
		$tu = new CUser();
		$tu->type = $this->type;
		$this->FromHashMap($tu->ToHashMap());
		$this->Save();
	}
}
?>