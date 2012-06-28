<?php
	class CRepeater2 extends CHTMLObject 
	{
		public $headerTemplate;
		public $itemTemplate;
		public $footerTemplate;
		public $separatorTemplate;
		
		public function CRepeater2()
		{
			$this->CHTMLObject();
			
		}
		
		public function RenderHTML()
		{
			$html = "";
			if (sizeof($this->dataSource)){
				$html = $this->headerTemplate;
				$i = 0;
				$last = sizeof($this->dataSource) - 1;
				foreach ($this->dataSource as $key=>$value){
					$value = CHTMLObject::GetBindableData(&$value);
					$html.=CStringFormatter::Format($this->itemTemplate,$value);
					if (!empty($this->separatorTemplate) && $i < $last)
						$html .= $this->separatorTemplate;
					$i++;
				}
				$html .=$this->footerTemplate;
			}
			return $html;
		}
	}
?>
