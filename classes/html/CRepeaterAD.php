<?php
class CRepeaterAD extends CHTMLObject 
{
	public $headerTemplate;
	public $itemTemplate;
	public $footerTemplate;

	public $adTemplate;
  public $adAfterLine = 5;	
  
  public $adTemplate2;
  public $adAfterLine2 = 10;	
	
	public function CRepeater()
	{
		$this->CHTMLObject();
		
	}
	
	public function RenderHTML()
	{
		$html = $this->headerTemplate;
		$lineNum = 0;
		foreach ($this->dataSource as $key=>$value)
		{
			$lineNum++;
			$value = CHTMLObject::GetBindableData(&$value);
			$html.=CStringFormatter::Format($this->itemTemplate,$value);
			if ($this->adTemplate && $lineNum == $this->adAfterLine) {
				$html .= $this->adTemplate;
			}
			
			if ($this->adTemplate2 && $lineNum == $this->adAfterLine2) {
				$html .= $this->adTemplate2;
			}
			
		}
		
		$html .=$this->footerTemplate;
		
		return $html;
	}
}
?>
