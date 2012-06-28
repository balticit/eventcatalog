<?php

include_once("fckeditor/fckeditor.php");

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
	
		$oFCKeditor = new FCKeditor("$this->parentId[$this->dbField]");
		$oFCKeditor->Width = '700';
		$oFCKeditor->Height = '500';
		$oFCKeditor->BasePath = '/fckeditor/';
		$oFCKeditor->Value = $this->GetValue();

		return CStringFormatter::Format($this->addModeTemplate,
		array("class"=>$this->BuildClassString($this->addClass),"value"=>$oFCKeditor->CreateHtml()));
	}
	
	public function RenderEditItem()
	{
	
		$oFCKeditor = new FCKeditor("$this->parentId[$this->dbField]");
		$oFCKeditor->Width = '700';
		$oFCKeditor->Height = '500';
		$oFCKeditor->BasePath = '/fckeditor/' ;
		$oFCKeditor->Value = $this->GetValue();	

		return CStringFormatter::Format($this->addModeTemplate,
		array("class"=>$this->BuildClassString($this->addClass),"value"=>$oFCKeditor->CreateHtml()));	
	

	}
}
?>