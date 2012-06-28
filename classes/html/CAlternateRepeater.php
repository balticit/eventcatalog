<?php
	class CAlternateRepeater extends CRepeater 
	{
		
		public $alternateTemplate;
		public $alternateKey;
		public $alternateValue;
		
		public function CAlternateRepeater()
		{
			$this->CRepeater();
		}
		
		public function RenderHTML()
		{
			$html = $this->headerTemplate;
			
			foreach ($this->dataSource as $key=>$value)
			{
				$value = CHTMLObject::GetBindableData(&$value);
				$alt = false;
				if (isset($value[$this->alternateKey]))
				{
					$alt = ($value[$this->alternateKey]==$this->alternateValue);
				}
				$html.=CStringFormatter::Format(($alt)?$this->alternateTemplate:$this->itemTemplate,$value);
			}
			
			$html .=$this->footerTemplate;
			
			return $html;
		}
	}
?>