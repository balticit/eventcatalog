<?php
class CDataTable extends CObject
{
	public $tableName = "";
	public $fields = array();

	public $page;
	public $pageSize = 0;
	public $autoIncField = null;
	public $keys = array();
	public $filter;

	private $orderMap = array();
	private $sortMap = array();

	public function CDataTable($tableName, $fields = array())
	{
		$this->CObject();
		$this->tableName= $tableName;
		$fkeys = array_keys($fields);
		foreach ($fkeys as $key) {
			$this->fields[$fields[$key]->name] = $fields[$key];
			if ($fields[$key]->autoInc)
			{
				$this->autoIncField = $fields[$key]->name;
			}
			if ($fields[$key]->primary)
			{
				array_push($this->keys , $fields[$key]->name);
			}
		}
	}

	private function CreateFilterString($filter = null)
	{
		$fi = "";
		if (is_object($filter))
		{
			if (method_exists($filter,"ToSqlString"))
			{
				foreach ($_GET as $key=>$value) {
					if (substr($key,0,3)=="fi_")
						$fi .= "and ".substr($key,3)."='".addslashes($value)."' ";
				}
				$sfilter = $filter->ToSqlString();
				return (strlen($sfilter)>0)?" where $sfilter $fi ":"";
			}
		}
		else {
			
			foreach ($_GET as $key=>$value) {
				if (substr($key,0,3)=="fi_")
					$fi .= "and ".substr($key,3)."='".addslashes($value)."' ";
			}
			if ($fi!="")
				return "where ".substr($fi,3);
		}
		return "";
	}

	private function CreateLimitString()
	{
		$page =(intval($this->page)==$this->page)&&is_numeric($this->page)?$this->page:0;
		$pv = $page*$this->pageSize;
		return ($this->pageSize>0)?" limit $pv,$this->pageSize ":"";
	}

	private function CreateSortString()
	{
		$order = "";
		for ($i=0;$i<sizeof($this->orderMap);$i++)
		{
			$order.= (($i>0)?",":" ")."`".$this->orderMap[$i]."` ".$this->sortMap[$this->orderMap[$i]];
		}
		return (strlen($order)>0)?" order by $order ":"";
	}

	public function SetSort($field,$type = "asc",$position = null)
	{
		$this->DeleteSort($field);
		$count = sizeof($this->orderMap);
		$position = (is_int($position))?$position:$count;
		if ($count>1)
		{
			for ($i=$count;$i>$position;$i--)
			{
				$this->orderMap[$i+1] = $this->orderMap[$i];
			}
		}
		$this->orderMap[$position] = $field;
		$this->sortMap[$field] = (strtolower($type)=="desc")?"desc":"asc";
	}

	public function DeleteSort($field)
	{
		$key= array_search($field,$this->orderMap);
		$count = sizeof($this->orderMap);
		if ((!($key===false)))
		{
			if ($count>1)
			{
				for ($i=$key;$i<$count-1;$i++)
				{
					$this->orderMap[$i] = $this->orderMap[$i+1];
				}
			}
			unset($this->orderMap[$count+1]);
		}
	}

	private function CreateFieldsString()
	{
		$fkeys = array_keys($this->fields);
		return "`".CStringFormatter::FromArray($fkeys,"`,`")."`";
	}

	private function BuildSelectString($fieldsString = "*",$filterString = "",$orderString="",$limitString = "")
	{
		return "select $fieldsString from $this->tableName $filterString $orderString $limitString";
	}

	public function SelectRaw($query = null)
	{
		if (is_null($query))
		{
			$fieldsString = $this->CreateFieldsString();
			$filterString = $this->CreateFilterString($this->filter);
			$orderString = $this->CreateSortString();
			$limitString = $this->CreateLimitString();
			$query = $this->BuildSelectString($fieldsString ,$filterString ,$orderString,$limitString );
		}
		return SQLProvider::ExecuteQuery($query);
	}

	public function SelectObjects($query = null)
	{
		$objs = array();
		$raws = $this->SelectRaw($query);
		$rkeys = array_keys($raws);
		foreach ($rkeys as $rkey) {
			array_push($objs,new CDataRow(&$this->fields,$raws[$rkey]));
		}
		return $objs;
	}

	public function  SelectUnique($ufilter = null,$join = true)
	{
		$filterString = "";
		if (is_string($ufilter))
		{
			$ufilter = new CRawFilter( $ufilter);
		}
		$filter = (method_exists($ufilter,"ToSqlString"))?$ufilter:null;
		if ($join&&(method_exists($this->filter,"ToSqlString")))
		{
			$filter = (method_exists($filter,"ToSqlString"))?new CAndFilter($this->filter,$filter):$this->filter;
		}
		$filterString = $this->CreateFilterString($filter);
		$query = $this->BuildSelectString($this->CreateFieldsString(),$filterString,""," limit 1 ");
		$data = SQLProvider::ExecuteQuery($query);
		if (sizeof($data)==0)
		{
			return null;
		}
		return new CDataRow(&$this->fields,$data[0]);
	}

	public function Delete($ufilter = null,$join = true)
	{
		$filterString = "";
		if (is_string($ufilter))
		{
			$ufilter = new CRawFilter( $ufilter);
		}
		$filter = (method_exists($ufilter,"ToSqlString"))?$ufilter:null;
		if ($join&&(method_exists($this->filter,"ToSqlString")))
		{
			$filter = (method_exists($filter,"ToSqlString"))?new CAndFilter($this->filter,$filter):$this->filter;
		}
		$filterString = $this->CreateFilterString($filter);
		$query = "delete from $this->tableName $filterString";
		SQLProvider::ExecuteNonReturnQuery($query);
	}
	
	/**
	 * Returns new row
	 *
	 * @param bool $filled
	 * @return CDataRow
	 */
	public function CreateNewRow($filled = false)
	{
		$data = array();
		if ($filled)
		{
			$fkeys = array_keys($this->fields);
			foreach ($fkeys as $fkey)
			{
				$data[$fkey] = ($this->fields[$fkey]->hasDefault)?$this->fields[$fkey]->default:null;
			}
		}
		return new CDataRow(&$this->fields,$data,!$filled);
	}

	private function BuildInsertString($fields,$values)
	{
		return "Insert into $this->tableName($fields) values ($values)";
	}

	private function PrepareData($fieldName,$value)
	{
		switch ($this->fields[$fieldName]->systemType) {
			case "int":
				return is_null($value)?"null":"$value";
			
			case "float":
			  return is_null($value)?"null":"$value";
			
			case "string":			
				return empty($value)?"null":"'".mysql_real_escape_string($value)."'";

			case "date":
			  if (empty($value) && $this->fields[$fieldName]->hasDefault)
				  return $this->fields[$fieldName]->default;
				else
			    return "'".date("Y-m-d H:i:s",is_string($value)?strtotime($value):$value)."'";
		}
		return null;
	}

	public function InsertObject(&$dataRow,$updateAfterInsert = true)
	{
		$data = $dataRow->GetData();
		if (sizeof($data)>0)
		{
			$dataKeys = array_keys($data);
			$fields = "";
			$values = "";
			foreach ($data as $key=>$value)
			{
				if(!( $this->autoIncField == $key ||
				      ( is_null($value) && 
							  ($this->fields[$key]->hasDefault ||
								 $this->fields[$key]->nullable) 
							) 
					  ) ){
					if (!empty($fields)){
						$values .= ",";
						$fields .= ",";
					}
					$fields.="`$key`";
					$values .= $this->PrepareData($key,$value);
				}
			}
			$query = $this->BuildInsertString($fields,$values);
			if (strlen($this->autoIncField)>0)
			{
				$iid = SQLProvider::ExecuteIdentityInsert($query);
				$dataRow->RawSet($this->autoIncField,$iid);
				$data[$this->autoIncField]=$iid;
			}
			else
			{
				SQLProvider::ExecuteNonReturnQuery($query);
			}
			if ($updateAfterInsert)
			{
				$filter = null;
				for ($i=0;$i<sizeof($this->keys);$i++)
				{
					$filter = ($i==0)?new CEqFilter(&$this->fields[$this->keys[$i]],$data[$this->keys[$i]])
					:new CAndFilter($filter,new CEqFilter(&$this->fields[$this->keys[$i]],$data[$this->keys[$i]]));
				}
				$dataRow = $this->SelectUnique($filter,false);
			}

		}
	}

	private function BuildUpdateQuery($modified,$filter,$limit=" limit 1")
	{
		return "update $this->tableName set $modified $filter $limit";
	}

	public function UpdateObject(&$dataRow)
	{
		$data = $dataRow->GetData();
		$mkeys = $dataRow->GetModified();
		if ((sizeof($data)>0)&&(sizeof($mkeys)>0))
		{
			$filter = null;
			for ($i=0;$i<sizeof($this->keys);$i++)
			{
				$filter = ($i==0)?new CEqFilter(&$this->fields[$this->keys[$i]],$data[$this->keys[$i]])
				:new CAndFilter($filter,new CEqFilter(&$this->fields[$this->keys[$i]],$data[$this->keys[$i]]));
			}
			$sfilter =" where ".$filter->ToSqlString()." ";
			$modString = "";
			for ($i=0;$i<sizeof($mkeys);$i++)
			{
				if ($i>0)
				{
					$modString.=",";
				}
				$modString.="`".$mkeys[$i]."`=".$this->PrepareData($mkeys[$i],$data[$mkeys[$i]]);
			}
			SQLProvider::ExecuteNonReturnQuery($this->BuildUpdateQuery($modString,$sfilter));
			$dataRow = new CDataRow(&$this->fields,$data);
		}
	}
}
?>
