<?php
class CDBLookupLinkDataField extends CDBLookupGridDataField
{
  public $linkFields;
	public $itemTemplateFile;
	public $itemTemplateEncoding = "windows-1251";

	public function CDBLookupLinkDataField()
	{
	  $this->CDBLookupGridDataField();
	}

	public function RenderItem()
	{
		$value = $this->GetValue();
		$qt = $this->fieldType=="numeric"?"":"'";
		$cellContent = "&nbsp;";
		$comma = empty($this->linkFields)?"":",";
		$linkData = array();
		if (!($this->fieldType=="numeric"&&IsNullOrEmpty($value)))
		{
			$data = SQLProvider::ExecuteQuery("select $this->lookupTitle$comma$this->linkFields from $this->lookupTable where $this->lookupId=$qt$value$qt");
			if (sizeof($data)>0)
			{
				$linkData = $data[0];
				$cellContent = $data[0][$this->lookupTitle];
			}elseif(($value==$this->emptyValue)&&($this->hasEmpty))
			{
				$cellContent = $this->emptyTitle;
			}

		}
		$app = CApplicationContext::GetInstance();
		if ($this->itemTemplateEncoding != $app->appEncoding)
		  $this->itemTemplate = iconv($this->itemTemplateEncoding,$app->appEncoding,file_get_contents(RealFile($this->itemTemplateFile)));
	  else
		  $this->itemTemplate = file_get_contents(RealFile($this->itemTemplateFile));
		return CStringFormatter::Format($this->itemTemplate,
		array_merge($linkData,array("class"=>$this->BuildItemClassString(),"value"=>$cellContent)));
	}

}
?>