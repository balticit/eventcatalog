<?php
class CTextareaGridDataFieldCL extends CGridDataField
{
	public $visibleOnList = false;

	public $rows = 8;
	
	public $cols = 60;
	
	public $listRenderLimit = 90;
		
	public function CTextareaGridDataFieldCL()
	{
		$this->CGridDataField();
		$this->itemTemplate = "<td {class}><a href=\"{target_link}\" target=\"_blank\"style=\"font-weight:normal; font-size:10px;\">{value}</a></td>";
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
		
		$target = "";
		$tbl_obj_id = "";
		if (is_object($this->dataSource))
		{
			$target = $this->dataSource->target_id;
			$tbl_obj_id = $this->dataSource->tbl_obj_id;
		}
		elseif (is_array($this->dataSource))
		{
			$target = $this->dataSource["target_id"];
			$tbl_obj_id = $this->dataSource["tbl_obj_id"];
		}
		
		if (strlen($target)>0)
		{
			$target_id = "";
			$target_type = "";
			for ($k = 0; $k<strlen($target); $k++)
			{
				if(is_numeric($target[$k]))
				{
					$target_id .= $target[$k];
				}
				else
				{
					$target_type .= $target[$k];
				}
			}		
			if ($target_type == "book" || $target_type == "news" || $target_type == "eventtv" || 
			    $target_type == "contractor" || $target_type == "artist" || $target_type == "agency" || $target_type == "area")
			{
				$target_link = "/$target_type/details/id/$target_id";
			}
			if ($target_type == "resident_news")
			{
				$target_link = "/resident_news/news/id/$target_id";
			}
			if ($target_type == "magazine")
			{
				$target_link = "/magazines/details/id/$target_id";
			}
			if ($target_type == "event")
			{
				$target_link = "/events/details/id/$target_id";
			}
			if ($target_type == "user")
			{
				$target_link = "/u_profile/type/user/id/$target_id";
			}
			$target_link .= "#comment$tbl_obj_id";
		}		
		return CStringFormatter::Format($this->itemTemplate,
		       array("class"=>$this->BuildItemClassString(),"value"=>$cellContent,"target_link"=>$target_link));
	}
}
?>