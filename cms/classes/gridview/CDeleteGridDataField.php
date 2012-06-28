<?php
class CDeleteGridDataField extends CGridDataField 
{
	public $deleteTemplate = "<a href=\"{link}\" {confirm}>{deleteText}</a>";

	public $deleteText = "";

	public $deleteConfirmFunction;
	
	public $visibleOnEdit = false;
	
	public $visibleOnAdd = false;
	
	public $sortable = false;
	
	public function CDeleteGridDataField()
	{
		$this->CGridDataField();
	}
	
	protected  function BuildDeleteValue($value)
	{
		$params = array();
		CopyArray(&$_GET,&$params);
		$params[$this->parentId]["mode"] = "delete";
		$params[$this->parentId]["id"] = $value;
		$params[$this->parentId]["idField"] = $this->dbField;
		$params[$this->parentId]["idType"] = $this->fieldType;
		$link = CURLHandler::BuildFullLink($params);
		$confirm = IsNullOrEmpty($this->deleteConfirmFunction)?"":"onclick=\"javascript:return $this->deleteConfirmFunction;\"";
		return CStringFormatter::Format($this->deleteTemplate,
		array("deleteText"=>$this->deleteText,"link"=>$link,"confirm"=>$confirm));
	}
	
	public function RenderItem()
	{
		$value = $this->GetValue();
		$cellContent = $this->BuildDeleteValue($value);
		return CStringFormatter::Format($this->itemTemplate,
		array("class"=>$this->BuildItemClassString(),"value"=>$cellContent));
	}
}
?>