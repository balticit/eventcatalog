<?php
class CSimpleBagGridDataField extends CGridDataField 
{
	public $listTemplatePath;
	
	public $listTemplateEncoding;
	
	public $listTemplate;
	
	public $bagTable;
	
	public $bagField;
	
	public $visibleOnAdd = false;
	
	public $visibleOnList = false;
	
	public $addLink;
	
	public $addText;
	
	public function CSimpleBagGridDataField()
	{	
		$this->CGridDataField();
	}
	
	public function RenderEditItem()
	{
		if (IsNullOrEmpty($this->listTemplate))
		{
			$file = RealFile($this->listTemplatePath);
			if (is_file($file))
			{
				$this->listTemplate = file_get_contents($file);
				if ((!IsNullOrEmpty($this->listTemplateEncoding))&&($this->listTemplateEncoding!=CSiteMapHandler::$appEncoding))
				{
					$this->listTemplate = iconv($this->listTemplateEncoding,CSiteMapHandler::$appEncoding,$this->listTemplate);
				}
			}
		}
		$value = $this->GetValue();
		$addLink = new CLinkLabel();
		$addLink->innerHTML = $this->addText;
		$addLink->href = CStringFormatter::Format($this->addLink,array($this->dbField=>$value));
		$addLink->target="_blank";
		$table = new CNativeDataTable($this->bagTable);
		$repeater = new CRepeater();
		$repeater->headerTemplate = $addLink->RenderHTML()."<br/>";
		$repeater->itemTemplate = $this->listTemplate;
		$table->filter = new CEqFilter($table->fields[$this->bagField],$value);
		$repeater->dataSource = $table->SelectObjects();
		return CStringFormatter::Format($this->editModeTemplate,
		array("class"=>$this->BuildClassString($this->editClass),"value"=>$repeater->RenderHTML()));
	}
}
?>