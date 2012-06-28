<?php
class CGoogleMapsField extends CGridDataField
{
	public function CGoogleMapsField()
	{
		$this->CGridDataField();
	}
	
	public function RenderAddItem()
	{
		return $this->RenderEditItem();
	}
	
	public function RenderEditItem()
	{
	
		$LatLng = "55.75370903771494,37.61986970901489";
		
		$value = $this->GetValue();
	
		if ($value!="")
			$LatLng = $value;
			
	


	
		$render = "<td>

	<div align=left>
	После ввода адреса нажмите \"Искать адрес\"<br>

		<input style=\"width: 300px;\" type=text name=adr id=adr>&nbsp;
		<input type=button value=\"Искать адрес\" onclick=\"process(document.getElementById('adr').value);\">

	<span id='example'></span>
	</div>
	
    <div id=\"map\" style=\"width: 700px; height: 500px\"></div>
	
	<script>
		var xmlHttp = createXmlHttpRequestObject();
	var latlng=\"$LatLng\".split(',');";
	
	if ($value!="") $render .= "var is=1;"; else $render .= "var is=0;";
	
	$render .= "</script>	
		
	<input type=\"hidden\" id=\"mapcoords\" name=\"$this->parentId[$this->dbField]\" value=\"".(($value!="")?$LatLng:"")."\" />
		
		
	</td>
		";
		
		return $render;
		
		
	
	}
	
	public function RenderItem()
	{
		//return $this->RenderEditItem();
	}
}
?>