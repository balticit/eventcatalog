<?php
class CDataSource extends CObject
{
	public $sourceEncoding;
	public $targetEncoding;

	public function CDataSource()
	{
		$this->CObject();
	}

	public function GetData()
	{
		return array();
	}

	public static function GetBindableData(&$dataSource)
	{
		if (is_array($dataSource))
		{
			return $dataSource;
		}
		if (is_object($dataSource))
		{
			if (method_exists($dataSource,"GetData"))
			{
				return $dataSource->GetData();
			}
			if (method_exists($dataSource,"ToHashMap"))
			{
				return $dataSource->ToHashMap();
			}
		}
		return array($dataSource);
	}
}
?>