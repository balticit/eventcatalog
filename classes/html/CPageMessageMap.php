<?php
class CPageMessageMap extends CPageObject 
{
	public $messages = array();
	
	public $mapKey = "default";
	
	public function CPageMessageMap()
	{
		$this->CPageObject();
	}
	
	public function GetMessage($key)
	{
		return isset($this->messages[$key])?$this->messages[$key]:null;
	}
	
	public function GetFormattedMessage($key,$args = array())
	{
		return isset($this->messages[$key])?CStringFormatter::Format($this->messages[$key],$args):null;
	}
}
?>