<?php
class CKeyTemplateObject extends CHTMLObject
{
	public $key;

	public $itemTemplates = array();

	public function CKeyTemplateObject()
	{
		$this->CHTMLObject();
	}

	public function RenderHTML()
	{
		if (is_array($this->itemTemplates))
		{
			$ikeys = array_keys($this->itemTemplates);
			foreach ($ikeys as $i) 
			{
				if (is_a($this->itemTemplates[$i],"CTemplateData"))
				{
					if ($this->itemTemplates[$i]->key==$this->key)
					{
						return CStringFormatter::Format($this->itemTemplates[$i]->GetTemplate(),$this->GetDataSourceData());
					}
				}
			}
		}
		return "";
	}
}
?>