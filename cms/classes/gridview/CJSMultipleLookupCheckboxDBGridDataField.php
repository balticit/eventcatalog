<?php
class CJSMultipleLookupCheckboxDBGridDataField extends CMultipleLookupDBGridDataField 
{
	public $triggersHierarchy = true;
	
	public $hasParent;
	
	public $parentField;
	
	public $parentJSField;
	
	public $lookupParent;
	
	public $parentEvent;
	
	public $dataTemplate = "{\"key\":\"{key}\",\"text\":\"{text}\",\"pkey\":\"{pkey}\"}";
	
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
	
	public $checkboxTemplate = "<div style=\"white-space:normal\">{item}</div>";
	
	public $hasEmpty = false;
	
	public function CMultipleLookupCheckboxDBGridDataField()
	{
		$this->CMultipleLookupDBGridDataField();
	}
	
	protected function GetLookupDataSource()
	{
		
		$filter = IsNullOrEmpty($this->filterString)?"":" where $this->filterString ";
        if ($this->orderBy != "") {
            $filter .= "order by ".$this->orderBy;
        }
        $sql = "select `$this->lookupId`,`$this->lookupTitle`".(IsNullOrEmpty($this->lookupParent)?"":",`$this->lookupParent`")." from `$this->lookupTable` $filter";
        $data =  SQLProvider::ExecuteQuery($sql);
		if ($this->hasEmpty)
		{
			array_unshift($data,array($this->lookupId=>$this->emptyValue,$this->lookupTitle=>$this->emptyTitle));
		}
		return $data;
	}
	
	private function GetJSData()
	{
		$data = $this->GetLookupDataSource();
		$items = array();
		$dkeys = array_keys($data);
		foreach ($dkeys as $dkey) 
		{
			array_push($items,CStringFormatter::Format($this->dataTemplate,
			array("key"=>$data[$dkey][$this->lookupId],
			"text"=>$data[$dkey][$this->lookupTitle],
			"pkey"=>IsNullOrEmpty($this->lookupParent)?"":$data[$dkey][$this->lookupParent])));	
		}
		return CStringFormatter::FromArray($items,",
		");
	}
	
	public function RenderAddItem()
	{
		//js data
		$jsdata =$this->GetJSData();
		$hasParent = ($this->hasParent)?"true":false;
		$pid = "$this->parentId[$this->parentJSField]";
		//end js data
		$sels = GetParam(array($this->parentId,"$"."autoValues",$this->joinTable),"g",array());
		$data = $this->GetLookupDataSource();
		$uobj = $this->joinTable."DS";
		$html = "<script type=\"text/javascript\" language=\"javascript\">
		var $uobj = new Object();
		$uobj.dataArray = new Array(
				$jsdata
			);
		 </script>";
		$dkeys = array_keys($data);
		foreach ($dkeys as $dkey) 
		{
			$cb = new CTextBox();
			$cb->value = $data[$dkey][$this->lookupId];
			$cbid = $this->parentId."_".$this->joinTable."_".$cb->value;
			$cb->id = $cbid;
			$cb->type = "checkbox";
			if (!(array_search($cb->value,$sels)===false))
			{
				$cb->htmlEvents["checked"] = "checked";
			}
			$cb->name = "$this->parentId[$this->joinTable][]";
			$htm = $cb->RenderHTML().$data[$dkey][$this->lookupTitle];
			$html.=CStringFormatter::Format($this->checkboxTemplate,array("item"=>$htm));
			$html.="<script type=\"text/javascript\" language=\"javascript\">
			var gid_$cbid = document.getElementById('$cbid');
			gid_$cbid.dataSource = $uobj.dataArray;
			gid_$cbid.DataBind = CheckBoxDataBind;
			if ($hasParent==true)
			{
   				gid_$cbid.parentControl = '$pid';
				RegisterHierarchy('$pid','$this->parentEvent','$cbid','DataBind');
			}
      		gid_$cbid.DataBind();
			</script>";
		}
		return CStringFormatter::Format($this->addModeTemplate,
		array("class"=>$this->BuildClassString($this->addClass),"value"=>$html));
	}
	
	public function RenderEditItem()
	{
		$sels = $this->GetLookupValues();
		//js data
		$jsdata =$this->GetJSData();
		$hasParent = ($this->hasParent)?"true":false;
		$pid = "$this->parentId[$this->parentJSField]";
		//end js data
		//$sels = GetParam(array($this->parentId,"$"."autoValues",$this->joinTable),"g",array());
		$data = $this->GetLookupDataSource();
		$uobj = $this->joinTable."DS";
		$html = "<script type=\"text/javascript\" language=\"javascript\">
		var $uobj = new Object();
		$uobj.dataArray = new Array(
				$jsdata
			);
		 </script>";
		$dkeys = array_keys($data);
		foreach ($dkeys as $dkey) 
		{
			$cb = new CTextBox();
			$cb->value = $data[$dkey][$this->lookupId];
			$cbid = $this->parentId."_".$this->joinTable."_".$cb->value;
			$cb->id = $cbid;
			$cb->type = "checkbox";
			if (!(array_search($cb->value,$sels)===false))
			{
				$cb->htmlEvents["checked"] = "checked";
			}
			$cb->name = "$this->parentId[$this->joinTable][]";
			$htm = $cb->RenderHTML().$data[$dkey][$this->lookupTitle];
			$html.=CStringFormatter::Format($this->checkboxTemplate,array("item"=>$htm));
			$html.="<script type=\"text/javascript\" language=\"javascript\">
			var gid_$cbid = document.getElementById('$cbid');
			gid_$cbid.dataSource = $uobj.dataArray;
			gid_$cbid.DataBind = CheckBoxDataBind;
			if ($hasParent==true)
			{
   				gid_$cbid.parentControl = '$pid';
				RegisterHierarchy('$pid','$this->parentEvent','$cbid','DataBind');
			}
      		gid_$cbid.DataBind();
			</script>";
		}
		return CStringFormatter::Format($this->editModeTemplate,
		array("class"=>$this->BuildClassString($this->editClass),"value"=>$html));
	}
}
?>