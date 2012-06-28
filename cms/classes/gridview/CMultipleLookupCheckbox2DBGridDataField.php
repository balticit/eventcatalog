<?php
class CMultipleLookupCheckbox2DBGridDataField extends CMultipleLookupDBGridDataField 
{
	public $checkboxTemplate = "<div style=\"white-space:normal\">{item}</div>";
	
	public $advancedField;
	
	public $hasEmpty = false;
	
	public function CMultipleLookupCheckboxDBGridDataField()
	{
		$this->CMultipleLookupDBGridDataField();
	}

	public function GetLookupValues()
	{
		$value = $this->GetValue();
		$qt = $this->fieldType=="numeric"?"":"'";
		$values = SQLProvider::ExecuteQueryReverse("select `$this->childField`,`$this->advancedField` from `$this->joinTable` where `$this->parentField`=$qt$value$qt");
		if (!isset($values[$this->childField]))
		{
			return array();
		}
		if (!is_array($values[$this->childField]))
		{
			return array();
		}
		return $values;
		

	}
	
	public function RenderAddItem()
	{
		$sels = GetParam(array($this->parentId,"$"."autoValues",$this->joinTable),"g",array());
		$data = $this->GetLookupDataSource();
		$html = "";
		$dkeys = array_keys($data);
		foreach ($dkeys as $dkey) 
		{
			$cb = new CTextBox();//Основные ключевые поля
			$cb->value = $data[$dkey][$this->lookupId];
			$cb->type = "checkbox";
			
			
			$cb_sub = new CTextBox();//Дополнительные ключевые поля
			$cb_sub->value = $data[$dkey][$this->lookupId];
			$cb_sub->type = "checkbox";
			if (isset($sels[$this->childField])) {
				if (!(($k = array_search($cb->value,$sels[$this->childField]))===false))
				{
					if ($sels[$this->advancedField][$k] == "1")
						$cb_sub->htmlEvents["checked"] = "checked";
					else
						$cb->htmlEvents["checked"] = "checked";
				}
			}
			$cb->name = "$this->parentId[$this->joinTable][]";
			$htm = $cb->RenderHTML().$data[$dkey][$this->lookupTitle]."&nbsp;".$cb_sub->RenderHTML();
			$html.=CStringFormatter::Format($this->checkboxTemplate,array("item"=>$htm));
		}
		$html = "Два места для галочки:<br> 1-е - для основных ключевых слов<br>2-е - для дополнительных".$html;
		return CStringFormatter::Format($this->editModeTemplate,
		array("class"=>$this->BuildClassString($this->editClass),"value"=>$html));
	}
	
	public function RenderEditItem()
	{
		$sels = $this->GetLookupValues();
		$data = $this->GetLookupDataSource();
		$html = "";
		$dkeys = array_keys($data);
		foreach ($dkeys as $dkey) 
		{
			$cb = new CTextBox();//Основные ключевые поля
			$cb->value = $data[$dkey][$this->lookupId];
			$cb->type = "checkbox";
			
			
			$cb_sub = new CTextBox();//Дополнительные ключевые поля
			$cb_sub->value = $data[$dkey][$this->lookupId];
			$cb_sub->type = "checkbox";
			if (isset($sels[$this->childField])) {
				if (!(($k = array_search($cb->value,$sels[$this->childField]))===false))
				{
					if ($sels[$this->advancedField][$k] == "1")
						$cb_sub->htmlEvents["checked"] = "checked";
					else
						$cb->htmlEvents["checked"] = "checked";
				}
			}
			$cb->name = "$this->parentId[$this->joinTable][nosub][]";
			$cb_sub->name = "$this->parentId[$this->joinTable][sub][]";
			$htm = $cb->RenderHTML().$data[$dkey][$this->lookupTitle]."&nbsp;".$cb_sub->RenderHTML();
			$html.=CStringFormatter::Format($this->checkboxTemplate,array("item"=>$htm));
		}
		
		$html = "Два места для галочки:<br> 1-е - для основных ключевых слов<br>2-е - для дополнительных".$html;
		
		return CStringFormatter::Format($this->editModeTemplate,
		array("class"=>$this->BuildClassString($this->editClass),"value"=>$html));
	}
	
	public function PostEdit()
	{
		$value = $this->GetValue();
		$qt = $this->fieldType=="numeric"?"":"'";
		SQLProvider::ExecuteNonReturnQuery("delete from `$this->joinTable` where `$this->parentField` = $qt$value$qt");
		$rets = GetParam(array($this->parentId,$this->joinTable),"p",array());
		
		$joinTable = new CNativeDataTable($this->joinTable);
		$pf = $this->parentField;
		$cf = $this->childField;
		$adv = $this->advancedField;
		if (isset($rets['nosub']))
			foreach ($rets['nosub'] as $ret)
			{
				if (!IsNullOrEmpty($ret))
				{
					$dr = $joinTable->CreateNewRow();
					$dr->$pf = $value;
					$dr->$cf = $ret;
					$dr->$adv = 0;
					$joinTable->InsertObject($dr,false);
				}
			}
		
		if (isset($rets['sub']))
			foreach ($rets['sub'] as $ret)
			{
				if (!IsNullOrEmpty($ret))
				{
					$dr = $joinTable->CreateNewRow();
					$dr->$pf = $value;
					$dr->$cf = $ret;
					$dr->$adv = 1;
					$joinTable->InsertObject($dr,false);
				}
			}		
		return false;
	}
	
 	public function PostAdd()
	{
		$value = $this->GetValue();
		$qt = $this->fieldType=="numeric"?"":"'";
		SQLProvider::ExecuteNonReturnQuery("delete from `$this->joinTable` where `$this->parentField` = $qt$value$qt");
		$rets = GetParam(array($this->parentId,$this->joinTable),"p",array());
		
		$joinTable = new CNativeDataTable($this->joinTable);
		$pf = $this->parentField;
		$cf = $this->childField;
		$adv = $this->advancedField;
		foreach ($rets[nosub] as $ret)
		{
			if (!IsNullOrEmpty($ret))
			{
				$dr = $joinTable->CreateNewRow();
				$dr->$pf = $value;
				$dr->$cf = $ret;
				$dr->$adv = 0;
				$joinTable->InsertObject($dr,false);
			}
		}
		
		foreach ($rets[sub] as $ret)
		{
			if (!IsNullOrEmpty($ret))
			{
				$dr = $joinTable->CreateNewRow();
				$dr->$pf = $value;
				$dr->$cf = $ret;
				$dr->$adv = 1;
				$joinTable->InsertObject($dr,false);
			}
		}		
		return false;
	}	
	
}
?>
