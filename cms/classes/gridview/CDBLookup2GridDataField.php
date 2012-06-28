<?php
class CDBLookup2GridDataField extends CGridDataField
{
	public $dbField2;
  public $driver;
	public $lookupId;
  public $lookupId2;
	public $lookupTitle;
	public $lookupTable;
	public $hasEmpty = true;
	public $emptyValue = "";
	public $emptyTitle = "";
	public $disabled = false;
  public $size;

	public function CDBLookup2GridDataField()
	{
		$this->CGridDataField();
    $this->size = 1;
	}

	public function RenderItem()
	{
		$value = $this->GetValue();
    $value2 = $this->GetValue($this->dbField2);
		$cellContent = null;
		if (!IsNullOrEmpty($value)){
			$data = SQLProvider::ExecuteQuery(
        "select $this->lookupTitle from $this->lookupTable
        where $this->lookupId='$value' and $this->lookupId2='$value2'");
			if (sizeof($data)>0)
				$cellContent = $data[0][$this->lookupTitle];
    }
    if(is_null($cellContent) && $this->hasEmpty &&
       ($value==$this->emptyValue || empty($value)))
				$cellContent = $this->emptyTitle;
    if(is_null($cellContent))
      $cellContent="&nbsp;";
		return CStringFormatter::Format($this->itemTemplate,
		array("class"=>$this->BuildItemClassString(),"value"=>$cellContent));
	}

	public function RenderAddItem()
	{
    return null;
	}

	public function RenderEditItem()
	{
		return null;
	}
}
?>