<?php
class CLikeFilter extends CCompareFilter
{
  protected $operator = "like";
  protected $grBegin;
  protected $grEnd;

  public $grep;

  public function CLikeFilter(&$field = null,$value = null,$grep="*.*")
  {
    $this->CCompareFilter();
    $this->field =$field;
    $this->value = $value;
    $this->grep = $grep;
  }

  protected function PrepareValue($value)
  {
    if ($this->field->systemType == "string")
    {
      $value="'".$this->grBegin.mysql_real_escape_string($value).$this->grEnd."'";
    }
    if ($this->field->systemType == "date")
    {
      $value = is_string($value)?strtotime($value):$value;
      $value = "'".date("Y-m-d H:i:s",$value)."'";
    }
    return $value;
  }

  public function PreSet()
  {
    switch ($this->grep) {
      case "*.*":
      {
        $this->grBegin = "%";
        $this->grEnd = "%";
      }
        break;
      case "*.":
      {
        $this->grBegin = "%";
        $this->grEnd = "";
      }
        break;
      case ".*":
      {
        $this->grBegin = "";
        $this->grEnd = "%";
      }
        break;

      default:
      {
        $this->grBegin = "";
        $this->grEnd = "";
      }
        break;
    }
  }
}
?>