<?php
class CAddToEventCalendar extends CGridDataField
{
	public function CAddToEventCalendar()
	{
		$this->CGridDataField();
	}
	
	public function RenderAddItem()
	{
		return $this->RenderEditItem();
	}
	
	public function RenderEditItem()
	{
	
		if (($this->GetValue()==0)or($this->GetValue()==null))
			return "<td></td>";
		else
			return "<td>".date('Y-m-d H-i-s P', $this->GetValue())."</td>";
	}
	
	public function RenderItem()
	{
		if (($this->GetValue()==0)or($this->GetValue()==null))
			return "<td></td>";
		else
			return "<td>".date('Y-m-d H-i-s P', $this->GetValue())."</td>";
	}
}
?>