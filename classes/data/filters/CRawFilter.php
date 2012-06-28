<?php
class CRawFilter extends CSQLFilter  
{
	public $sql;
	
	public function CRawFilter($sql = null)
	{
		$this->CSQLFilter();
		$this->sql = $sql;
	}
	
	public function ToSqlString()
	{
		return $this->sql;
	}
}
?>