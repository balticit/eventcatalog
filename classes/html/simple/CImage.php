<?php
class CImage extends CNativeSimpleHTMLObject 
{
	protected $template = "<img src=\"{src}\" class=\"class\" alt=\"{alt}\" title=\"{title}\" border=\"{border}\" {style} {htmlEvents}/>";
	
	public $src;
	
	public $alt;
	
	public $title;
	
	public $border = 0;
	
	public function CImage()
	{
		$this->CNativeHTMLObject();
	}
}
?>