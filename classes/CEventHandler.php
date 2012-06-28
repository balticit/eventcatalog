<?php
class CEventHandler
{
	protected $object;
	
	protected $function;
	
	
	
	public function CEventHandler($function,&$object = null)
	{
		$this->function = $function;
		$this->object = &$object;
	}
	
	public function RaiseEvent(&$sender = null,&$args = array())
	{
		$met = is_null($this->object)?$this->function:array($this->object,$this->function);
		if (is_callable($met))
		{
			$fn = $this->function;
			if (is_null($this->object))
			{
				
				$fn(&$sender,&$args);
			}
			else 
			{
				$this->object->$fn(&$sender,&$args);
			}
		}
	}
}
?>