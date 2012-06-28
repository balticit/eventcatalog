<?php
class CXLSXMLTableDumper extends CHTMLObject
{
	public $fields = array();

	public $tableName;

	public $useHeaders = true;

	public $htmlEncode = true;

	public function CXLSXMLTableDumper()
	{
		$this->CHTMLObject();
	}

	public function RenderHTML()
	{
		$html = '<?xml version="1.0" encoding="windows-1251"?>
					<?mso-application progid="Excel.Sheet"?>
					<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
					 xmlns:o="urn:schemas-microsoft-com:office:office"
					 xmlns:x="urn:schemas-microsoft-com:office:excel"
					 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
					 xmlns:html="http://www.w3.org/TR/REC-html40">
					 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
					 </DocumentProperties>
					 <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
					 </ExcelWorkbook>
					 <Worksheet ss:Name="export">
					  <Table>';
		$fkeys = array_keys($this->fields);
		if ($this->useHeaders)
		{
			$html.="<Row>";
			foreach ($fkeys as $fkey) {
				$html.='<Cell><Data ss:Type="String">'.$this->fields[$fkey]."</Data></Cell>";
			}

			$html.="</Row>";
		}
		$fl = CStringFormatter::FromArray($fkeys);
		$data = SQLProvider::ExecuteQuery("select $fl from $this->tableName");
		$dkeys = array_keys($data);
		foreach ($dkeys as $dkey) {
			$html.="<Row>";
			foreach ($fkeys as $fkey) {
				$html.= '<Cell><Data ss:Type="String">'.(($this->htmlEncode)?htmlspecialchars($data[$dkey][$fkey]):$data[$dkey][$fkey])."</Data></Cell>";
			}
			$html.="</Row>";
		}
		$html.="</Table>
				 </Worksheet>
				</Workbook>";
		return $html;
	}
}
?>