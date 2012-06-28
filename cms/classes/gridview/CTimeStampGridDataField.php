<?php
class CTimeStampGridDataField extends CGridDataField
{

	public $visibleOnEdit = false;

	public $visibleOnAdd = false;

	public function CTimeStampGridDataField()
	{
		$this->CGridDataField();
	}

	public function PreAdd()
	{
		$this->SetValue(date());
		return false;
	}
	
	public function PreEdit()
	{
		$this->SetValue(date());
		return false;
	}
}
?>