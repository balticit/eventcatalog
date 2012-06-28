<?php
class CJoinedTableGridDataField2 extends CGridDataField 
{
	public $bagTable;
	
	public $bagField;
	
	public $visibleOnAdd = false;
	
	public $visibleOnList = false;
	
	public $tableLink;
	
	public $refreshText;
	
	public $listText;
	
	public $resident_type;
	
	public $valueTemplate = "<script type=\"text/javascript\" language=\"javascript\">RegisterIFrameAutosize(\"{id}\");</script>
	<a href=\"#\" onclick=\"javascript:return SetFrameURL('{id}','/cms/usermark/?fi_from_resident_id={resident_id}&fi_from_resident_type={resident_type}'); \">{listText}</a>
	&nbsp;&nbsp;
	<a href=\"#\" onclick=\"javascript:return RefreshFrame('{id}')\">{refreshText}</a><br/>
	<iframe id=\"{id}\" src=\"/cms/usermark/?fi_from_resident_id={resident_id}&fi_from_resident_type={resident_type}\" marginwidth=\"0\" marginheight=\"0\" frameborder=\"0\" vspace=\"0\" hspace=\"0\" style=\"overflow:visible; width:100%; display:none;\"  scrolling=\"no\"  ></iframe><br>
	";
	
	public function CJoinedTableGridDataField2()
	{
		$this->CGridDataField();
	}
	
	public function RenderEditItem()
	{

		//print_r($this->parentId);
		$value = $this->GetValue();
		$resident_id = CStringFormatter::Format("{tbl_obj_id}",array($this->dbField=>$value));
		//echo $resident_id;
		$id = "$this->parentId[$this->bagTable]";
		//$resident_id = "{tbl_obj_id}";
		$resident_type = $this->resident_type;
		$htm = CStringFormatter::Format($this->valueTemplate,array("id"=>$id,"refreshText"=>$this->refreshText,"listText"=>$this->listText,"resident_id"=>$resident_id,"resident_type"=>$this->resident_type));
		return CStringFormatter::Format($this->editModeTemplate,
		array("class"=>$this->BuildClassString($this->editClass),"value"=>$htm));
	}
}
?>