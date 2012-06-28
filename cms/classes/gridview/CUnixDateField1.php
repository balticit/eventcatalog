<?php
class CUnixDateField1 extends CGridDataField
{
	public function CUnixDateField1()
	{
		$this->CGridDataField();
	}
	
	public function RenderAddItem()
	{
		$ibox = new CTextBox();
		$ibox->name = "$this->parentId[$this->dbField]";
		$ibox->value = date('Y-m-d');
		return CStringFormatter::Format($this->editModeTemplate,
		array("class"=>$this->BuildClassString($this->editClass),"value"=>$ibox->RenderHTML()));
	
		if (($this->GetValue()==0)or($this->GetValue()==null))
			return "<td></td>";
		else
			return "<td>".date('r', $this->GetValue())."</td>";
	}
	
	public function RenderEditItem()
	{

	
		$ibox = new CTextBox();
		$ibox->name = "$this->parentId[$this->dbField]";
		$ibox->value = date('Y-m-d', $this->GetValue());
		return CStringFormatter::Format($this->editModeTemplate,
		array("class"=>$this->BuildClassString($this->editClass),"value"=>$ibox->RenderHTML()));
	
		if (($this->GetValue()==0)or($this->GetValue()==null))
			return "<td></td>";
		else
			return "<td>".date('r', $this->GetValue())."</td>";
	}
	
	public function RenderItem()
	{
		if (($this->GetValue()==0)or($this->GetValue()==null))
			return "<td></td>";
		else
			return "<td>".date('Y-m-d H-i-s P', $this->GetValue())."</td>";
	}
	
	public function PreEdit(){
		
		$this->SetValue(strtotime($_POST["99_30\$dataTable"]["date"],time()));
		//print_r($_POST);
		//echo "<br>".$_POST['$dataTable']["date"]."<br>"."$this->parentId[$this->dbField]";
	}
}
?>