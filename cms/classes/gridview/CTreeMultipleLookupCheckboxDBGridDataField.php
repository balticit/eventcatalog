<?php
class CTreeMultipleLookupCheckboxDBGridDataField extends CMultipleLookupDBGridDataField 
{
	public $checkboxTemplate = "<div style=\"white-space:normal\">{item}</div>";
	
	public $hasEmpty = false;
	
	public $lookupParent;
	
	public $treeLevels;
	
	public $parentTemplate = "<div style=\"margin: 10px 0 0 0; font-weight: bold;\">{title}</div>";
	public $childTemplate = "<label style=\"margin: 0 0 0 10px;\"><input type=\"checkbox\" name=\"{name}\" value=\"{child_id}\" {checked} style=\"vertical-align: middle;\"/>{title}</label><br/>";
	
	public function CMultipleLookupCheckboxDBGridDataField()
	{
		$this->CMultipleLookupDBGridDataField();
	}
	
	protected function GetLookupDataSource()
	{
		
		$filter = IsNullOrEmpty($this->filterString)?"":" where $this->filterString ";
		$data =  SQLProvider::ExecuteQuery("select `$this->lookupId`,`$this->lookupTitle`".(IsNullOrEmpty($this->lookupParent)?"":",`$this->lookupParent`")." from `$this->lookupTable` $filter");
		if ($this->hasEmpty)
		{
			array_unshift($data,array($this->lookupId=>$this->emptyValue,$this->lookupTitle=>$this->emptyTitle));
		}
		return $data;
	}
	
	public function RenderAddItem()
	{
		$sels = GetParam(array($this->parentId,"$"."autoValues",$this->joinTable),"g",array());
		$data = $this->GetLookupDataSource();
		$dkeys = array_keys($data);
		foreach ($dkeys as $dkey) 
		{
			$data[$dkey]["checked"] = "";
			if (!(array_search($data[$dkey][$this->lookupId],$sels)===false))
			{
				$data[$dkey]["checked"] = 'checked="checked"';
			}
		}
		$tr = new CTreeRepeater();
		$tr->dataSource = $data;
		$tr->childField = $this->lookupId;
		$tr->parentField = $this->lookupParent;
		$ctp = CStringFormatter::Format($this->childTemplate,array("name"=>"$this->parentId[$this->joinTable][]", "child_id"=>'{'.$this->lookupId.'}'));
		$tr->itemTemplatesArray = array($this->parentTemplate,$ctp);
		$tr->levels = $this->treeLevels;
		$html = $tr->Render();
		return CStringFormatter::Format($this->addModeTemplate,
		array("class"=>$this->BuildClassString($this->addClass),"value"=>$html));
	}
	
	public function RenderEditItem()
	{
		$sels = $this->GetLookupValues();
		$data = $this->GetLookupDataSource();
		$dkeys = array_keys($data);
		foreach ($dkeys as $dkey) 
		{
			$data[$dkey]["checked"] = "";
			if (!(array_search($data[$dkey][$this->lookupId],$sels)===false))
			{
				$data[$dkey]["checked"] = 'checked="checked"';
			}
		}
		$tr = new CTreeRepeater();
		$tr->dataSource = $data;
		$tr->childField = $this->lookupId;
		$tr->parentField = $this->lookupParent;
		$ctp = CStringFormatter::Format($this->childTemplate,array("name"=>"$this->parentId[$this->joinTable][]", "child_id"=>'{'.$this->lookupId.'}'));
		$tr->itemTemplatesArray = array($this->parentTemplate,$ctp);
		$tr->levels = $this->treeLevels;
		$html = $tr->Render();
		return CStringFormatter::Format($this->editModeTemplate,
		array("class"=>$this->BuildClassString($this->editClass),"value"=>$html));
	}
}
?>
