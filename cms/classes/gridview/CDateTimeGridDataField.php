<?php
class CDateTimeGridDataField extends CGridDataField 
{
	public $dateFormat = "yyyymmdd";
	
	public $useTime = true;
	
	public function CDateTimeGridDataField()
	{
		$this->CGridDataField();
	}
	
	public function RenderAddItem()
	{
		$ibox = new CDateTimePicker();
		$ibox->name = "$this->parentId[$this->dbField]";
		$ibox->id = "$this->parentId[$this->dbField]";
		$ibox->dateFormat = $this->dateFormat;
		$ibox->showTime = $this->useTime;
		return CStringFormatter::Format($this->addModeTemplate,
		array("class"=>$this->BuildClassString($this->addClass),"value"=>$ibox->RenderHTML()));
	}
	
	public function RenderEditItem()
	{
		$ibox = new CDateTimePicker();
		$ibox->name = "$this->parentId[$this->dbField]";
		$ibox->id = "$this->parentId[$this->dbField]";
		$ibox->dateFormat = $this->dateFormat;
		$ibox->showTime = $this->useTime;
		$ibox->value = $this->GetValue();
		return CStringFormatter::Format($this->editModeTemplate,
		array("class"=>$this->BuildClassString($this->editClass),"value"=>$ibox->RenderHTML()));
	}
}
?>
