<?php
class CNativeHTMLObject extends CHTMLObject
{

	public $id;
	public $name;
	public $class;
	public $style = array();

	public $htmlEvents = array();
	
	protected $attributes = array();

	public function __set($name,$value)
	{
		$this->attributes[$name] = $value;
	}
	
	public function __get($name)
	{
		return $this->attributes[$name];
	}
	
	public function CNativeHTMLObject()
	{
		$this->CHTMLObject();
	}

	protected function BuildHTMLEvents()
	{
		$events = "";
		foreach ($this->htmlEvents as $key => $value) {
			$events.=$key.'="'.str_replace('"','\"',$value).'"';
		}
		return $events;
	}
	
	protected function BuildHTMLAttributes()
	{
		$attrs = "";
		foreach ($this->attributes as $key => $value) {
			$attrs.=$key.'="'.str_replace('"','\"',$value).'"';
		}
		return $attrs;
	}
	
	protected function BuildHTMLStyle()
	{
		$style = "";
		foreach ($this->style as $key => $value) {
			$style.=$key.':'.str_replace('"','\"',$value).';';
		}
		return " style=\"$style\" ";
	}
	
	
}
?>