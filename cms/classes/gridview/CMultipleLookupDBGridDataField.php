<?php
class CMultipleLookupDBGridDataField extends CGridDataField
{
  public $driver;

  public $lookupId;

  public $lookupTitle;

  public $lookupTable;

  public $joinTable;

  public $parentField;

  public $childField;

  public $size = 10;

  public $multiple = true;

  public $hasEmpty = true;

  public $emptyValue = "";

  public $emptyTitle = "";
  //public $visibleOnList = false;

  public $filterString = "";

    public $orderBy = "";

  public $sortable = false;

  public function CMultipleLookupDBGridDataField()
  {
    $this->CGridDataField();
  }

  public function GetLookupValues()
  {
    $value = $this->GetValue();
    $qt = $this->fieldType=="numeric"?"":"'";
    $values = SQLProvider::ExecuteQueryReverse("select `$this->childField` from `$this->joinTable` where `$this->parentField`=$qt$value$qt");
    if (!isset($values[$this->childField]))
    {
      return array();
    }
    if (!is_array($values[$this->childField]))
    {
      return array();
    }
    return $values[$this->childField];

  }

  public function GetLookupTitles()
  {
    $value = $this->GetValue();
    $qt = $this->fieldType=="numeric"?"":"'";
    $values = SQLProvider::ExecuteQueryReverse("select m.`$this->lookupTitle`
    from `$this->lookupTable` m inner join `$this->joinTable` m2m
    on m.`$this->lookupId` = m2m.`$this->childField` where m2m.`$this->parentField`=$qt$value$qt");
    return isset($values[$this->lookupTitle])?$values[$this->lookupTitle]:array();
  }

  public function RenderItem()
  {
    $values = $this->GetLookupTitles();
    $value = CStringFormatter::FromArray($values);
    $cellContent = IsNullOrEmpty($value)? "&nbsp;":$value;
    return CStringFormatter::Format($this->itemTemplate,
    array("class"=>$this->BuildItemClassString(),"value"=>$cellContent));
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

  public function RenderAddItem()
  {
    $ibox = new CSelect();
    $ibox->name = "$this->parentId[$this->joinTable][]";
    $ibox->dataSource = $this->GetLookupDataSource();
    $ibox->valueName = $this->lookupId;
    $ibox->titleName = $this->lookupTitle;
    $ibox->size = $this->size;
    $ibox->multiple = $this->multiple;
    $ibox->selectedValue = GetParam(array($this->parentId,"$"."autoValues",$this->joinTable),"g",array());
    return CStringFormatter::Format($this->addModeTemplate,
    array("class"=>$this->BuildClassString($this->addClass),"value"=>$ibox->RenderHTML()));
  }

  public function RenderEditItem()
  {
    $ibox = new CSelect();
    $ibox->name = "$this->parentId[$this->joinTable][]";
    $ibox->dataSource =  $this->GetLookupDataSource();
    $ibox->valueName = $this->lookupId;
    $ibox->titleName = $this->lookupTitle;
    $ibox->selectedValue = $this->GetLookupValues();
    $ibox->size = $this->size;
    $ibox->multiple = $this->multiple;
    return CStringFormatter::Format($this->editModeTemplate,
    array("class"=>$this->BuildClassString($this->editClass),"value"=>$ibox->RenderHTML()));
  }

  public function PostAdd()
  {
    $value = $this->GetValue();
    $qt = $this->fieldType=="numeric"?"":"'";
    SQLProvider::ExecuteNonReturnQuery("delete from `$this->joinTable` where `$this->parentField` = $qt$value$qt");
    $rets = GetParam(array($this->parentId,$this->joinTable),"p",array());
    $joinTable = new CNativeDataTable($this->joinTable);
    $pf = $this->parentField;
    $cf = $this->childField;
    foreach ($rets as $ret)
    {
      if (!IsNullOrEmpty($ret))
      {
        $dr = $joinTable->CreateNewRow();
        $dr->$pf = $value;
        $dr->$cf = $ret;
        $joinTable->InsertObject($dr,false);
      }
    }
    return false;
  }

  public function GetFilter($fieldName,$value,&$table,&$filter,$logicalOperator)
  {
    if ($this->joinTable==$fieldName){
      if (is_array($value))
      {
        $joinTable = new CNativeDataTable($this->joinTable);
        $cf = $this->childField;
        $pf = $this->parentField;
        $joinTable->filter = new CInFilter(&$joinTable->fields[$cf],$value);
        $fvts = $joinTable->SelectRaw();
        $fvls = array();
        foreach ($fvts as $fvt)
        {
          array_push($fvls,$fvt[$pf]);
        }
        $filter = new CAndFilter($filter, (sizeof($fvls)>0)? new CInFilter(&$table->fields[$this->dbField],$fvls):new CRawFilter("1=2"));
      }
    }
  }

  public function PostEdit()
  {
    $value = $this->GetValue();
    $qt = $this->fieldType=="numeric"?"":"'";
    SQLProvider::ExecuteNonReturnQuery("delete from `$this->joinTable` where `$this->parentField` = $qt$value$qt");
    $rets = GetParam(array($this->parentId,$this->joinTable),"p",array());
    $joinTable = new CNativeDataTable($this->joinTable);
    $pf = $this->parentField;
    $cf = $this->childField;
    foreach ($rets as $ret)
    {
      if (!IsNullOrEmpty($ret))
      {
        $dr = $joinTable->CreateNewRow();
        $dr->$pf = $value;
        $dr->$cf = $ret;
        $joinTable->InsertObject($dr,false);
      }
    }
    return false;
  }
}
?>