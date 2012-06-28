<?php
class CXMLTableDataSource extends CDataSource
{

	public $filename;
	public $rawdata;
	public $source;

	public function CXMLTableDataSource()
	{
		$this->CDataSource();
	}

	public function GetData()
	{
		$output = array();
		if ($this->source=="file")
		{
			$xmlparser = XMLParser::GetInstance();
			$xmlparser->useConversion = (($this->sourceEncoding!=$this->targetEncoding)&&(strlen($this->sourceEncoding)>0)&&(strlen($this->targetEncoding)>0));
			$xmlparser->sourceEncoding = $this->sourceEncoding;
			$xmlparser->targerEncoding = $this->targetEncoding;
			$this->rawdata = $xmlparser->GetXMLArray(RealFile($this->filename));
		}
		else
		{
			if (($this->sourceEncoding!=$this->targetEncoding)&&(strlen($this->sourceEncoding)>0)&&(strlen($this->targetEncoding)>0))
			{
				$this->rawdata = iconv($this->sourceEncoding,$this->targetEncoding,$this->rawdata);
			}
		}
		if (isset($this->rawdata["datasource"][0]["/"]["type"]))
		{
			if ($this->rawdata["datasource"][0]["/"]["type"]=="xmltable")
			{
				if ((isset($this->rawdata["datasource"][0]["column"][0]))&&(isset($this->rawdata["datasource"][0]["row"][0])))
				{
					$columns = array();
					foreach ($this->rawdata["datasource"][0]["column"] as $column)
					{
						if (isset($column["/"]["name"]))
						{
							array_push($columns,$column["/"]["name"]);
						}
					}
					$index = (isset($this->rawdata["datasource"][0]["index"][0]["/"]["name"]))?$this->rawdata["datasource"][0]["index"][0]["/"]["name"]:null;
					foreach ($this->rawdata["datasource"][0]["row"] as $row)
					{
						foreach ($columns as $column)
						{
							if (isset($row[$column][0]))
							{
								$row["/"][$column] = $row[$column][0]["\\"];
							}
						}
						if (is_null($index))
						{
							array_push($output,$row["/"]);
						}
						else
						{
							$output[$row["/"][$index]]=$row["/"];
						}
					}

				}
			}
		}
		return $output;
	}
}
?>