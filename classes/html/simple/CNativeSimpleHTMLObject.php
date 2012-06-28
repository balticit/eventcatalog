<?php
class CNativeSimpleHTMLObject extends CNativeHTMLObject 
{
	protected $template = "";
	
	public $innerHTML;
	
	public function CNativeSimpleHTMLObject()
	{
		$this->CNativeHTMLObject();
	}
	
	public function  RenderHTML()
	{
		$params = array();
		foreach ($this as $key => $value) {
			if (!is_array($value)&&!is_object($value))
			{
				$params[$key]=$value;
			}
		}
		$params["htmlEvents"] = $this->BuildHTMLEvents();
		$params["style"] = $this->BuildHTMLStyle();
		$params["attributes"] = $this->BuildHTMLAttributes();
		return CStringFormatter::Format($this->template,$params);
	}
}
?>