<?php
class CSQLFilter extends CObject
{
  protected $field;

  public function CSQLFilter()
  {
    $this->CObject();
  }

  protected function PrepareValue($value)
  {
    if ($this->field->systemType == "string")
      $value="'".mysql_real_escape_string($value)."'";
    else if ($this->field->systemType == "date"){
      $value = is_string($value)?strtotime($value):$value;
      $value = "'".date("Y-m-d H:i:s",$value)."'";
    }
    return $value;
  }

  public function ToSqlString()
  {
    return "";
  }
}
?>