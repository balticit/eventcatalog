<?php
class CMultipleLookupCheckboxDBGridDataField extends CMultipleLookupDBGridDataField 
{
	public $checkboxTemplate = "<div style=\"white-space:normal\">{item}</div>";
	
	public $hasEmpty = false;
	
	public function CMultipleLookupCheckboxDBGridDataField()
	{
		$this->CMultipleLookupDBGridDataField();
	}
	
	public function RenderAddItem()
	{
		$sels = GetParam(array($this->parentId,"$"."autoValues",$this->joinTable),"g",array());
		$data = $this->GetLookupDataSource();
		$html = "";
		$dkeys = array_keys($data);
		foreach ($dkeys as $dkey) 
		{
			$cb = new CTextBox();
			$cb->value = $data[$dkey][$this->lookupId];
			$cb->type = "checkbox";
			if (!(array_search($cb->value,$sels)===false))
			{
				$cb->htmlEvents["checked"] = "checked";
			}
			$cb->name = "$this->parentId[$this->joinTable][]";
			$htm = $cb->RenderHTML().$data[$dkey][$this->lookupTitle];
			$html.=CStringFormatter::Format($this->checkboxTemplate,array("item"=>$htm));
		}
		return CStringFormatter::Format($this->addModeTemplate,
		array("class"=>$this->BuildClassString($this->addClass),"value"=>$html));
	}
	
	public function RenderEditItem()
	{
		$sels = $this->GetLookupValues();
		$data = $this->GetLookupDataSource();
		$html = "";
		$dkeys = array_keys($data);
		foreach ($dkeys as $dkey) 
		{
			$cb = new CTextBox();
			$cb->value = $data[$dkey][$this->lookupId];
			$cb->type = "checkbox";
			if (!(array_search($cb->value,$sels)===false))
			{
				$cb->htmlEvents["checked"] = "checked";
			}
			$cb->name = "$this->parentId[$this->joinTable][]";
			$htm = $cb->RenderHTML().$data[$dkey][$this->lookupTitle];
			$html.=CStringFormatter::Format($this->checkboxTemplate,array("item"=>$htm));
		}
		return CStringFormatter::Format($this->editModeTemplate,
		array("class"=>$this->BuildClassString($this->editClass),"value"=>$html));
	}
}
?>