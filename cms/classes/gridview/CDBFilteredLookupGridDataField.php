<?php
class CDBFilteredLookupGridDataField extends CDBLookupGridDataField
{
  public $filterField;
  public $filterLookupField;

  public function CDBFilteredLookupGridDataField()
  {
    $this->CDBLookupGridDataField();
  }

  protected function GetLookupDataSource()
  {
    $where = "";
    if(!empty($this->filterField) &&
       !empty($this->filterLookupField)){
      $value = $this->GetValue($this->filterField);
      if (!empty($value)){
        $where = "where `$this->filterLookupField`=";
        if (is_string($value))
          $where .= "'".  mysql_real_escape_string($value)."'";
        else
          $where .= $value;
      }
    }
    $data = SQLProvider::ExecuteQuery(
      "select `$this->lookupId`,`$this->lookupTitle`
      from `$this->lookupTable` $where
      order by `$this->lookupTitle`");
    if ($this->hasEmpty)
      array_unshift($data,array($this->lookupId=>$this->emptyValue,$this->lookupTitle=>$this->emptyTitle));
    return $data;
  }
}
?>
