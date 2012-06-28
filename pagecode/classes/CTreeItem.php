<?php
	class CTreeItem extends CHTMLObject  
	{
		public $headerTemplate = "";
		public $itemTemplate = "";
		public $footerTemplate = "";
		
		public $level = 0;
		
		public function CTreeItem()
		{
			$this->CHTMLObject();
			
		}
		
		public function RenderHTML()
		{
			$html = $this->headerTemplate;
			$html.=CStringFormatter::Format($this->itemTemplate,$this->GetDataSourceData());
			foreach ($this->controls as $control)
			{

				$html.=$control->RenderHTML();
			}
			$html .=$this->footerTemplate;
			return CStringFormatter::Format($html,$this->GetDataSourceData());
		} 
	}
?>