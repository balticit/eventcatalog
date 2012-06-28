<?php
class COrFilter extends CSQLFilter
{
	public $leftOperand = null;
	public $rightOperand = null;
	public $useBrakets;

	public function COrFilter($left = null,$right = null,$useBrakets = true)
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
		  return " $lb".$this->leftOperand->ToSqlString()."$rb or $lb".$this->rightOperand->ToSqlString()."$rb ";
    else
      return " $lb".$this->rightOperand->ToSqlString()."$rb ";
	}
}
?>