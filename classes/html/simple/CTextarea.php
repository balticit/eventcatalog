<?php
class CTextarea extends CNativeSimpleHTMLObject 
{
	protected $template = "<textarea id=\"{id}\" rows=\"{rows}\" cols=\"{cols}\" name=\"{name}\" class=\"{class}\" {style} {htmlEvents}>{innerHTML}</textarea>";
	
	public $rows = 5;
	
	public $cols = 20;
	
	public function CTextarea()
	{
		$this->CNativeSimpleHTMLObject();
	}
}
?>