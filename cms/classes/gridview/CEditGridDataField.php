<?php
class CEditGridDataField extends CGridDataField 
{
	public $editTemplate = "<a href=\"{link}\">{editText}</a>";

	public $editText = "";
	
	public $editLink;
		
	public $visibleOnEdit = false;
	
	public $visibleOnAdd = false;
	
	public $sortable = false;
	
	public function CEditGridDataField()
	{
		$this->CGridDataField();
	}
	
	protected  function BuildEditValue($value)
	{
		$link = "";
		if (IsNullOrEmpty($this->editLink))
		{
			$params = array();
			CopyArray(&$_GET,&$params);
			$params[$this->parentId]["mode"] = "edit";
			$params[$this->parentId]["id"] = $value;
			$params[$this->parentId]["idField"] = $this->dbField;
			$params[$this->parentId]["idType"] = $this->fieldType;
			$link = CURLHandler::BuildFullLink($params)."#$this->parentId$"."top";
		}
		else 
		{
			$link = CStringFormatter::Format($this->editLink,$this->GetDataSourceData());
		}
		return CStringFormatter::Format($this->editTemplate,
		array("editText"=>$this->editText,"link"=>$link));
	}
	
	public function RenderItem()
	{
		$value = $this->GetValue();
		$cellContent = $this->BuildEditValue($value);
		return CStringFormatter::Format($this->itemTemplate,
		array("class"=>$this->BuildItemClassString(),"value"=>$cellContent));
	}
}
?>