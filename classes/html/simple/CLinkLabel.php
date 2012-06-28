<?php
class CLinkLabel extends CNativeSimpleHTMLObject 
{
	protected $template = "<a href=\"{href}\" id=\"{id}\" name=\"{name}\" title=\"{title}\" class=\"{class}\" {style} target=\"{target}\" {htmlEvents}>{innerHTML}</a>";
	
	public $href = "#";
	
	public $target="_self";
	
	public $title;
	
	public function CLinkLabel()
	{
		$this->CNativeSimpleHTMLObject();
	}
	
}
?>