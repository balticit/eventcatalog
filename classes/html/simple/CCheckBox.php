<?php
	class CCheckBox extends CHTMLObject 
	{
		public $title = "";
		public $value = "";
		public $checked = false;
    public $name = "";
		
		protected static $template =  "<label><input type=\"checkbox\" name=\"{name}\" value=\"{value}\" {checked} style=\"vertical-align: middle; margin:3px 3px 3px 0;\">{title}</label>";
		protected static $checkedTemplate = "checked=\"checked\"";
		
		public function CCheckBox($name,$title,$value,$checked = false)
		{
			$this->CHTMLObject();
			$this->title = $title;
			$this->value = $value;
			$this->checked = $checked;
      $this->name = $name;
		}
		
		public function RenderHTML()
		{
		 	return 	CStringFormatter::Format(
        CCheckBox::$template,
		 		array("name" => $this->name,
        "title"   => $this->title,
        "value"   => $this->value,
        "checked" => ($this->checked)?CCheckBox::$checkedTemplate:""));
		}
	}
?>
