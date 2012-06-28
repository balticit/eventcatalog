<?php
class CRecommendedGridDataField extends CGridDataField
{
	
	public $editModeTemplate = "<td {class}>{value}&nbsp;&nbsp; цифры 1,2,3 задают первые три места остальные по дате добавления (отображается max 7 резидентов)</td>";

	public $addModeTemplate = "<td {class}>{value}&nbsp;&nbsp; цифры 1,2,3 задают первые три места остальные по дате добавления (отображается max 7 резидентов)</td>";
	
	public function CRecommendedGridDataField()
	{
		$this->CGridDataField();
	}
} 
?>