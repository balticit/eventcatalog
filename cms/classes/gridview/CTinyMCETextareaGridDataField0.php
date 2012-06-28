<?php


/*
class CTinyMCETextareaGridDataField extends CTextareaGridDataField 
{
	public $rows = 20;
	
	public $cols = 60;
	
	public function CTinyMCETextareaGridDataField()
	{
		$this->CTextareaGridDataField();
	}
	
	public function RenderAddItem()
	{
		$ibox = new CTinyMCETextarea();
		$ibox->name = "$this->parentId[$this->dbField]";
		$ibox->id =  "$this->parentId[$this->dbField]";
		$ibox->rows = $this->rows;
		$ibox->cols = $this->cols;
		return CStringFormatter::Format($this->addModeTemplate,
		array("class"=>$this->BuildClassString($this->addClass),"value"=>$ibox->RenderHTML()));
	}
	
	public function RenderEditItem()
	{
		$ibox = new CTinyMCETextarea();
		$ibox->name = "$this->parentId[$this->dbField]";
		$ibox->id =  "$this->parentId[$this->dbField]";
		$ibox->innerHTML = $this->GetValue();
		$ibox->rows = $this->rows;
		$ibox->cols = $this->cols;
		return CStringFormatter::Format($this->editModeTemplate,
		array("class"=>$this->BuildClassString($this->editClass),"value"=>$ibox->RenderHTML()));
	}
}


*/
?>