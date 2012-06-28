<?php
class CJionedTableGridDataField extends CGridDataField 
{
	public $bagTable;
	
	public $bagField;
	
	public $visibleOnAdd = false;
	
	public $visibleOnList = false;
	
	public $tableLink;
	
	public $refreshText;
	
	public $listText;
	
	public $valueTemplate = "<script type=\"text/javascript\" language=\"javascript\">RegisterIFrameAutosize(\"{id}\");</script>
	<a href=\"#\" onclick=\"javascript:return SetFrameURL('{id}','{link}'); \">{listText}</a>&nbsp;&nbsp;<a href=\"#\" onclick=\"javascript:return RefreshFrame('{id}')\">{refreshText}</a><br/>
	<iframe id=\"{id}\" src=\"{link}\" marginwidth=\"0\" marginheight=\"0\" frameborder=\"0\" vspace=\"0\" hspace=\"0\" style=\"overflow:visible; width:100%; display:none;\"  scrolling=\"no\"  ></iframe>";
	
	public function CJionedTableGridDataField()
	{
		$this->CGridDataField();
	}
	
	public function RenderEditItem()
	{

		$value = $this->GetValue();
		$link = CStringFormatter::Format($this->tableLink,array($this->dbField=>$value));
		$id = "$this->parentId[$this->bagTable]";
		$htm = CStringFormatter::Format($this->valueTemplate,array("id"=>$id,"refreshText"=>$this->refreshText,"link"=>$link,"listText"=>$this->listText));
		return CStringFormatter::Format($this->editModeTemplate,
		array("class"=>$this->BuildClassString($this->editClass),"value"=>$htm));
	}
}
?>