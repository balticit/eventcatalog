<?php
class CXMLLookupGridDataField extends CGridDataField 
{
	
	public $sourceEncoding;
	
	public $targetEncoding;
	
	public $driver;
	
	public $lookupId;
	
	public $lookupTitle;
	
	public $lookupTable;
	
	protected $rawLookupData = array();
	
	public function CXMLLookupGridDataField()
	{
		$this->CGridDataField();
	}
	
	protected function BuildXMLLookupTable()
	{
		$app = CApplicationContext::GetInstance();
		$driver = CClassFactory::CreateClassIntance($this->driver,$this->subParams);
		if (!is_null($driver))
		{
			$driver->sourceEncoding = $this->sourceEncoding;
			$driver->targetEncoding = IsNullOrEmpty($this->targetEncoding)?$app->appEncoding:$this->targetEncoding;
			$driver->source = "file";
			$this->rawLookupData = $driver->GetData();
			$dkeys = array_keys($this->rawLookupData);
			$lookTab = array();
			foreach ($dkeys as $dkey)
			{
				$lookTab[$this->rawLookupData[$dkey][$this->lookupId]] = $this->rawLookupData[$dkey][$this->lookupTitle];
			}
			$this->lookupTable = $lookTab;
		}
	}
	
	public function PostInit()
	{
		$this->BuildXMLLookupTable();
	}
	
	public function RenderItem()
	{
		$value = $this->GetValue();
		$cellContent =  isset($this->lookupTable[$value])?$this->lookupTable[$value]:"&nbsp;";
		return CStringFormatter::Format($this->itemTemplate,
		array("class"=>$this->BuildItemClassString(),"value"=>$cellContent));
	}
	
	public function RenderAddItem()
	{
		$ibox = new CSelect();
		$ibox->name = "$this->parentId[$this->dbField]";
		$ibox->dataSource = $this->rawLookupData;
		$ibox->valueName = $this->lookupId;
		$ibox->titleName = $this->lookupTitle;
		return CStringFormatter::Format($this->addModeTemplate,
		array("class"=>$this->BuildClassString($this->addClass),"value"=>$ibox->RenderHTML()));
	}
	
	public function RenderEditItem()
	{
		
		
 		$ibox = new CSelect();
		$ibox->name = "$this->parentId[$this->dbField]";
		$ibox->dataSource = $this->rawLookupData;
		$ibox->valueName = $this->lookupId;
		$ibox->titleName = $this->lookupTitle;
		$ibox->selectedValue = $this->GetValue();
		return CStringFormatter::Format($this->editModeTemplate,
		array("class"=>$this->BuildClassString($this->editClass),"value"=>$ibox->RenderHTML()));
	}
}
?>