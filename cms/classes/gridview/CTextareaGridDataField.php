<?php
class CTextareaGridDataField extends CGridDataField
{
	public $visibleOnList = false;

	public $rows = 8;
	
	public $cols = 60;
	
	public $listRenderLimit = 90;
	
	public function CTextareaGridDataField()
	{
		$this->CGridDataField();
	}
	
	public function RenderAddItem()
	{
		$ibox = new CTextarea();
		$ibox->name = "$this->parentId[$this->dbField]";
		$ibox->rows = $this->rows;
		$ibox->cols = $this->cols;
		return CStringFormatter::Format($this->addModeTemplate,
		array("class"=>$this->BuildClassString($this->addClass),"value"=>$ibox->RenderHTML()));
	}
	
	public function RenderEditItem()
	{
		$ibox = new CTextarea();
		$ibox->name = "$this->parentId[$this->dbField]";
		$ibox->innerHTML = $this->GetValue();
		$ibox->rows = $this->rows;
		$ibox->cols = $this->cols;
		return CStringFormatter::Format($this->editModeTemplate,
		array("class"=>$this->BuildClassString($this->editClass),"value"=>$ibox->RenderHTML()));
	}
	
	public function RenderItem()
	{
		$value = $this->GetValue();
		$value = (strlen($value)>$this->listRenderLimit)?substr($value,0,$this->listRenderLimit)." ...":$value;
		$cellContent = IsNullOrEmpty($value)? "&nbsp;":$value;
		return CStringFormatter::Format($this->itemTemplate,
		array("class"=>$this->BuildItemClassString(),"value"=>$cellContent));
	}
}
?>