<?php
	class CTemplateObject extends CHTMLObject 
	{
		public $template;
		
		public function CTemplateObject($template = "",$data = array())
		{
			$this->CHTMLObject();
			$this->template = $template;
			$this->dataSource = $data;
		}
		
		public function RenderHTML()
		{
			return CStringFormatter::Format($this->template,$this->GetDataSourceData());
		}
	}
?>