<?php
class CJSDBLookupGridDataField extends CDBLookupGridDataField 
{
	public $triggersHierarchy = true;
	
	public $hasParent;
	
	public $parentField;
	
	public $lookupParent;
	
	public $parentEvent;
	
	public $dataTemplate = "{\"key\":\"{key}\",\"text\":\"{text}\",\"pkey\":\"{pkey}\"}\n";
	
	public $jsTemplate = " <script type=\"text/javascript\" language=\"javascript\">
			
			var gid = document.getElementById('{id}');
			gid.dataSource = new Array(
				{data}
			);
			gid.DataBind = DataBind;
			if ({hasParent}==true)
			{
   				gid.parentControl = '{pid}';
				RegisterHierarchy('{pid}','{pevent}','{id}','DataBind');
			}
      		gid.DataBind();
      		gid.value = '{value}';
    </script>";
	
	public function CJSDBLookupGridDataField()
	{
		$this->CDBLookupGridDataField();
	}
	
	protected function GetLookupDataSource()
	{
		return $data = SQLProvider::ExecuteQuery("select `$this->lookupId`,`$this->lookupTitle`".(IsNullOrEmpty($this->lookupParent)?"":",`$this->lookupParent`")." from `$this->lookupTable` order by `$this->lookupTitle`");
	}
	
	protected function RenderControl()
	{
		$ibox = new CSelect();
		$ibox->name = "$this->parentId[$this->dbField]";
		$ibox->id = "$this->parentId[$this->dbField]";
		if ($this->triggersHierarchy)
		{
			$ibox->htmlEvents["onchange"]="javascript:TriggerHierarchy(event); if (window.self!=window.top){window.parent.resizeCaller();};";
		}
		$html = $ibox->RenderHTML();
		//JS data
		$data = $this->GetLookupDataSource();
		$items = array();
		$dkeys = array_keys($data);
		foreach ($dkeys as $dkey) 
		{
			array_push($items,CStringFormatter::Format($this->dataTemplate,
			array("key"=>$data[$dkey][$this->lookupId],
			"text"=>addslashes($data[$dkey][$this->lookupTitle]),
			"pkey"=>IsNullOrEmpty($this->lookupParent)?"":$data[$dkey][$this->lookupParent])));	
		}
		$jsdata = CStringFormatter::FromArray($items);
		$pid = "$this->parentId[$this->parentField]";
		return $html.CStringFormatter::Format($this->jsTemplate,array("id"=>$ibox->id,
		"pid"=>$pid,"data"=>$jsdata,"pevent"=>$this->parentEvent,"value"=>$this->GetValue(),
		"hasParent"=>$this->hasParent?"true":"false"));
	}
	
	public function RenderAddItem()
	{
		/*$ibox = new CSelect();
		$ibox->name = "$this->parentId[$this->dbField]";
		$ibox->dataSource = $this->GetLookupDataSource();
		$ibox->valueName = $this->lookupId;
		$ibox->titleName = $this->lookupTitle;
		$ibox->selectedValue = $this->GetValue();*/
		return CStringFormatter::Format($this->addModeTemplate,
		array("class"=>$this->BuildClassString($this->addClass),"value"=>$this->RenderControl()));
	}

	public function RenderEditItem()
	{
		/*$ibox = new CSelect();
		$ibox->name = "$this->parentId[$this->dbField]";
		$ibox->dataSource =  $this->GetLookupDataSource();
		$ibox->valueName = $this->lookupId;
		$ibox->titleName = $this->lookupTitle;
		$ibox->selectedValue = $this->GetValue();*/
		return CStringFormatter::Format($this->editModeTemplate,
		array("class"=>$this->BuildClassString($this->editClass),"value"=>$this->RenderControl()));
	}
}
?>