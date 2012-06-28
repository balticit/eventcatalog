<?php
class CScriptSrc extends CNativeSimpleHTMLObject 
{
	protected $template = "<script src=\"{src}\" type=\"{type}\" language=\"{language}\" ></script>";
	
	public $src;
	
	public $type = "text/javascript";
	
	public $language = "javascript";
	
	
	public function CScriptSrc()
	{
		$this->CNativeHTMLObject();
	}
}
?>