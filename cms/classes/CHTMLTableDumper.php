<?php
class CHTMLTableDumper extends CHTMLObject
{
	public $fields = array();

	public $tableName;

	public $useHeaders = true;

	public $htmlEncode = true;

	public function CHTMLTableDumper()
	{
		$this->CHTMLObject();
	}

	public function RenderHTML()
	{
		$html = "<table>";
		$fkeys = array_keys($this->fields);
		if ($this->useHeaders)
		{
			$html.="<tr>";
			foreach ($fkeys as $fkey) {
				$html.="<th>".$this->fields[$fkey]."</th>";
			}

			$html.="</tr>";
		}
		$fl = CStringFormatter::FromArray($fkeys);
		$data = SQLProvider::ExecuteQuery("select $fl from $this->tableName");
		$dkeys = array_keys($data);
		foreach ($dkeys as $dkey) {
			$html.="<tr>";
			foreach ($fkeys as $fkey) {
				$html.= "<td>".(($this->htmlEncode)?htmlspecialchars($data[$dkey][$fkey]):$data[$dkey][$fkey])."</td>";
			}
			$html.="</tr>";
		}
		$html.="</table>";
		return $html;
	}
}
?>