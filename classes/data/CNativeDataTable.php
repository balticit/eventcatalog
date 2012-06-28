<?php
class CNativeDataTable extends CDataTable 
{
	public function CNativeDataTable($tableName)
	{
		$this->CDataTable($tableName,CNativeTableBuilder::GetTableFields($tableName));
	}
}
?>