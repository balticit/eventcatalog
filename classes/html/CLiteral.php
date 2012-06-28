<?php
class CLiteral extends CHTMLObject 
{
	
	public $html;
	
	public function CLiteral()
	{
		$this->CHTMLObject();
		
	}
	
	public function RenderHTML()
	{
		return $this->html;
	}
}
?>