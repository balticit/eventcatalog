<?php
class CDBLookupGridDataField extends CGridDataField
{
	public $driver;
	public $lookupId;
	public $lookupTitle;
	public $lookupTable;
	public $hasEmpty = true;
	public $emptyValue = "";
	public $emptyTitle = "";
	public $disabled = false;
  public $size;

	public function CDBLookupGridDataField()
	{
		$this->CGridDataField();
    $this->size = 1;
	}

	public function RenderItem()
	{
		$value = $this->GetValue();
		$qt = $this->fieldType=="numeric"?"":"'";
		$cellContent = null;
		if (!IsNullOrEmpty($value)){
			$data = SQLProvider::ExecuteQuery(
        "select $this->lookupTitle from $this->lookupTable
        where $this->lookupId=$qt$value$qt");
			if (sizeof($data)>0)
				$cellContent = $data[0][$this->lookupTitle];
    }
    if(is_null($cellContent) && $this->hasEmpty && ($value==$this->emptyValue || empty($value)))
				$cellContent = $this->emptyTitle;
    if(is_null($cellContent))
      $cellContent="&nbsp;";
		return CStringFormatter::Format($this->itemTemplate,
		array("class"=>$this->BuildItemClassString(),"value"=>$cellContent));
	}

	protected function GetLookupDataSource()
	{
		$data = SQLProvider::ExecuteQuery("select `$this->lookupId`,`$this->lookupTitle` from `$this->lookupTable` order by `$this->lookupTitle`");
		if ($this->hasEmpty)
		{
			array_unshift($data,array($this->lookupId=>$this->emptyValue,$this->lookupTitle=>$this->emptyTitle));
		}
		return $data;
	}

	public function RenderAddItem()
	{
    $ibox = new CSelect();
		$ibox->name = "$this->parentId[$this->dbField]";
		$ibox->dataSource = $this->GetLookupDataSource();
		$ibox->valueName = $this->lookupId;
		$ibox->titleName = $this->lookupTitle;
		$ibox->selectedValue = $this->GetValue();
		$ibox->disabled = $this->disabled;
    $ibox->size = $this->size;
		return CStringFormatter::Format($this->addModeTemplate,
		array("class"=>$this->BuildClassString($this->addClass),"value"=>$ibox->RenderHTML()));
	}

	public function RenderEditItem()
	{
		$ibox = new CSelect();
		$ibox->name = "$this->parentId[$this->dbField]";
		$ibox->dataSource =  $this->GetLookupDataSource();
		$ibox->valueName = $this->lookupId;
		$ibox->titleName = $this->lookupTitle;
		$ibox->selectedValue = $this->GetValue();
		$ibox->disabled = $this->disabled;
    $ibox->size = $this->size;
		return CStringFormatter::Format($this->editModeTemplate,
		array("class"=>$this->BuildClassString($this->editClass),"value"=>$ibox->RenderHTML()));
	}
}
?>