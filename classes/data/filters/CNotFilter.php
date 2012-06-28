<?php
class CNotFilter extends CSQLFilter 
{
	public $denyFilter;
	public $useBrakets;
	
	public function CNotFilter($denyFilter,$useBrakets = true)
	{
		$this->CSQLFilter();
		$this->denyFilter = $denyFilter;
		$this->useBrakets = $useBrakets;
	}
	
	
	public function ToSqlString()
	{
		$lb = ($this->useBrakets)?"(":"";
		$rb = ($this->useBrakets)?")":"";
		
		return " not $lb".$this->denyFilter->ToSqlString()."$rb ";
	}
}
?>