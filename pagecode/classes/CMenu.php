<?php
	class CMenu extends CHTMLObject 
	{
		public $headerTemplate;
		public $itemTemplate;
		public $activeItemTemplate;
		public $activeItem;
		public $footerTemplate;
		
		public function CMenu()
		{
			$this->CHTMLObject();
		}
		
		public function RenderHTML()
		{
			$forHeader = array();
			$items_html = "";
			foreach ($this->dataSource as $key => $value) 
			{
				$value = CHTMLObject::GetBindableData(&$value);
				$forHeader = $value;
				$items_html.=CStringFormatter::Format(($key==$this->activeItem)?$this->activeItemTemplate:$this->itemTemplate,$value);
			}
			return CStringFormatter::Format($this->headerTemplate,$forHeader).$items_html.$this->footerTemplate;
		}
	}
?>