<?php
class CRecommendedGridDataField extends CGridDataField
{
	
	public $editModeTemplate = "<td {class}>{value}&nbsp;&nbsp; ����� 1,2,3 ������ ������ ��� ����� ��������� �� ���� ���������� (������������ max 7 ����������)</td>";

	public $addModeTemplate = "<td {class}>{value}&nbsp;&nbsp; ����� 1,2,3 ������ ������ ��� ����� ��������� �� ���� ���������� (������������ max 7 ����������)</td>";
	
	public function CRecommendedGridDataField()
	{
		$this->CGridDataField();
	}
} 
?>