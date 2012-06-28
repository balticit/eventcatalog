<?php
class CAndFilter extends CSQLFilter
{
  public $leftOperand = null;
  public $rightOperand = null;
  public $useBrakets;

  public function CAndFilter($left = null,$right = null,$useBrakets = true)
  {
    $this->CSQLFilter();
    $this->leftOperand = $left;
    $this->rightOperand = $right;
    $this->useBrakets = $useBrakets;
  }

  public function ToSqlString()
  {
    $lb = ($this->useBrakets)?"(":"";
    $rb = ($this->useBrakets)?")":"";
    if ($this->leftOperand)
      return " $lb".$this->leftOperand->ToSqlString()."$rb and $lb".$this->rightOperand->ToSqlString()."$rb ";
    else
      return " $lb".$this->rightOperand->ToSqlString()."$rb ";
  }
}
?>