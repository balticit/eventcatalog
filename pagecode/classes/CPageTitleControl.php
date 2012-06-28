<?php
	class CPageTitleControl extends CHTMLObject 
	{
		public $text;
		
		public $usetags;
		
		public function  RenderHTML()
		{
			if ($this->usetags)
			{
				return "<title>".$this->text."</title>";
			}
			else
			{
				return $this->text;
			} 
			
		}
	}
?>