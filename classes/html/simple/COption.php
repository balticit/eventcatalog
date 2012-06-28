<?php
	class COption extends CHTMLObject 
	{
		public $title = "";
		public $value = "";
		public $selected = "";
		
		protected static $template =  "<option value=\"{value}\" {selected}>{title}</option>";
		protected static $selectedTemplate = "selected=\"selected\"";
		
		public function COption($title,$value,$selected = false)
		{
			$this->CHTMLObject();
			$this->title = $title;
			$this->value = $value;
			$this->selected = $selected;	
		}
		
		public function RenderHTML()
		{
		 	return 	CStringFormatter::Format(COption::$template,
		 		array("title"=>$this->title,"value"=>$this->value,"selected"=>($this->selected)?COption::$selectedTemplate:""));
		}
	}
?>