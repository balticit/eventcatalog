<?php
class CPasswordGridDataField extends CGridDataField
{
	public $visibleOnList = false;

	public function CPasswordGridDataField()
	{
		$this->CGridDataField();
	}

	public function RenderAddItem()
	{
		$ibox = new CTextBox();
		$ibox->name = "$this->parentId[$this->dbField]";
		$ibox->type = "password";
		return CStringFormatter::Format($this->addModeTemplate,
		array("class"=>$this->BuildClassString($this->addClass),"value"=>$ibox->RenderHTML()));
	}

	public function RenderEditItem()
	{
		$ibox = new CTextBox();
		$ibox->name = "$this->parentId[$this->dbField]";
		$ibox->type = "password";
		return CStringFormatter::Format($this->editModeTemplate,
		array("class"=>$this->BuildClassString($this->editClass),"value"=>$ibox->RenderHTML()));
	}

	public function PreAdd()
	{
		if (isset($_POST[$this->parentId][$this->dbField]))
		{
			$_POST[$this->parentId][$this->dbField] = md5($_POST[$this->parentId][$this->dbField]);
		}
		return false;
	}
	
	public function PreEdit()
	{
		if (isset($_POST[$this->parentId][$this->dbField]))
		{
			if (IsNullOrEmpty($_POST[$this->parentId][$this->dbField]))
			{
				unset($_POST[$this->parentId][$this->dbField]);
			}
			else
			{
				$_POST[$this->parentId][$this->dbField] = md5($_POST[$this->parentId][$this->dbField]);
			}
		}
		return false;
	}
}
?>