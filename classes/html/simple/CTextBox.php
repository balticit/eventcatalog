<?php
class CTextBox extends CNativeSimpleHTMLObject  
{
	protected $template = "<input type=\"{type}\" id=\"{id}\" name=\"{name}\" value=\"{value}\" class=\"{class}\" {style} {htmlEvents} {attributes} />";
	
	public $value;
	
	public $type = "text";
	
	public function CTextBox()
	{
		$this->CNativeSimpleHTMLObject();
	}
}

?>