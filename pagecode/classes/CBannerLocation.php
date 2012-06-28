<?php
class CBannerLocation extends CTemplateObject  
{
	public $locationID;
	
	public $locationTable;
	
	public $bannersTable;
	
	public $description;
	
	public function CBanner()
	{
		$this->CKeyTemplateObject();
	}
	
	public function PreRender()
	{
		$locTable = new CNativeDataTable($this->locationTable);
		$curloc = $locTable->SelectUnique(new CEqFilter(&$locTable->fields["tbl_obj_id"],$this->locationID));
		if (is_null($curloc))
		{
			$curloc = $locTable->CreateNewRow(true);
			$curloc->tbl_obj_id = $this->locationID;
			$curloc->path = CURLHandler::$currentPath;
			$curloc->title = $this->description;
			$curloc->active = 1;
			$locTable->InsertObject(&$curloc);
		}
		$this->dataSource["locationID"] = $this->locationID;
	}
}
?>