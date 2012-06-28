<?php
class CChildItemsList extends CHTMLObject 
{
	public $listKey = "itemList";
	
	public $bodyTemplate = "";
	
	public $itemTemplate = "";
	
	public $addLink;
	
	public $editLink;
	
	public $addTitle;
	
	public $color = "#FFFFFF";
	
	public $addLinkVisible = true;
	
	public $title;
	
	public $tableName;
	
	public $idField;
	
	public $pidField;
	
	public $titleField;
	
	public $deleteKey;
	
	public $pid;
	
	public function CChildItemsList()
	{
		$this->CHTMLObject();
	}
	
	public function RenderHTML()
	{
		$pars = array();
		$pars["add_link"] = $this->addLink;
		$pars["add_text"] = $this->addTitle;
		$pars["add_visible"] = $this->addLinkVisible?"visible":"hidden";
		$pars["color"] = $this->color;
		$pars["name"] = $this->title;
		$dels = GP($this->deleteKey,array());
		if (sizeof($dels)>0)
		{
			$dstr = CStringFormatter::FromArray($dels);
			SQLProvider::ExecuteNonReturnQuery("delete from $this->tableName where `$this->idField` in ($dstr)");
		}
		$items = SQLProvider::ExecuteQuery("select `$this->idField`,`$this->titleField`,`$this->pidField` from `$this->tableName` where `$this->pidField`=$this->pid ");
		$dataItems = array();
		foreach ($items as $item) 
		{
			$dataItem = array();
			$dataItem["tbl_obj_id"] = $item[$this->idField];
			$dataItem["edit_link"] = CStringFormatter::Format($this->editLink,array("tbl_obj_id"=>$item[$this->idField]));
			$dataItem["delete_key"]	= $this->deleteKey;
			$dataItem["title"]	=$item[$this->titleField];
			array_push($dataItems,$dataItem);		
		}		
		$itemList = new CRepeater();
		$itemList->itemTemplate = &$this->itemTemplate;
		$itemList->dataSource = $dataItems;
		$pars[$this->listKey] = $itemList->RenderHTML();
		return CStringFormatter::Format($this->bodyTemplate,$pars);
	}
}
?>