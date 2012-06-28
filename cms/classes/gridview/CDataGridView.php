<?php
class CDataGridView extends CHTMLObject
{
  public $fields = array();
  private  $customActions = array();
  public $mode = "list";
  public $tableName;
  public $page;
  public $pageSize = 0;
  public $id;
  public $name;
  public $cssClass;
  public $orderField;
  public $orderType;
  public $border = 1;
  public $pagerId;
  public $submitText;
  public $resetText;
  public $submitClass;
  public $resetClass;
  public $keyId;
  public $keyType;
  public $addText;
  public $addClass;
  public $hiddenFields = array();
  public $logicalOperator = "and";
  public $findValue;

  public function CDataGridView()
  {
    $this->CHTMLObject();
  }

  public function PostInit()
  {
    $params = GP($this->uniqueId);

    $this->page = GP(array($this->uniqueId,"page"),$this->page);
    $this->orderField = GP(array($this->uniqueId,"orderField"),$this->orderField);
    $this->orderType = GP(array($this->uniqueId,"orderType"),$this->orderType);
    $this->mode = GP(array($this->uniqueId,"mode"),$this->mode);
    $this->hiddenFields = GP(array($this->uniqueId,"$"."hiddenFields"),$this->hiddenFields);
    $this->ValidateFields();
    $this->SetMode();

    if ($this->mode != "add" && $this->mode != "edit"){
      $getParams = GetParam(array($this->uniqueId,"$"."autoValues"),"g",array());
        if (sizeof($getParams)>0)
          foreach ($getParams as $gparam){
            if (isset($gparam) && is_string($gparam) && trim($gparam,'%'))
              $this->findValue = trim($gparam,'%');
          }
    }
  }

  private function ValidateFields()
  {
    $fkeys = array_keys($this->fields);
    foreach ($fkeys as $fkey) {
      if ((!is_subclass_of($this->fields[$fkey],"CGridDataField"))
      &&(!is_a($this->fields[$fkey],"CGridDataField"))||(!(array_search($fkey,$this->hiddenFields)===false)))
      {
        unset($this->fields[$fkey]);
      }
      else
      {
        $this->fields[$fkey]->parentId = $this->uniqueId;
        if (!IsNullOrEmpty($this->fields[$fkey]->customAction))
        {
          $this->customActions[$this->fields[$fkey]->customAction] = &$this->fields[$fkey];
        }
      }
    }
  }

  private function BuildParams()
  {
    $params = "";
    $params.= IsNullOrEmpty($this->id)?"":" id=\"$this->id\" ";
    $params.= IsNullOrEmpty($this->name)?"":" name=\"$this->name\" ";
    $params.= IsNullOrEmpty($this->cssClass)?"":" class=\"$this->cssClass\" ";
    return $params;
  }

  private function BuildPager($count)
  {
    $app = CApplicationContext::GetInstance();
    $pager = $app->page->GetControl($this->pagerId);
    if (!is_null($pager))
    {
      $pager->currentPage = $this->page;
      $pager->totalPages = $count;
      $pager->pageParam = array($this->uniqueId,'page');
      return $pager->RenderHTML();
    }
    return null;
  }

  private function SetMode()
  {
    if ($this->mode=="delete")
    {
      $id = GP(array($this->uniqueId,"id"));
      $type = GP(array($this->uniqueId,"idType"));
      $field = GP(array($this->uniqueId,"idField"));
      if ((!IsNullOrEmpty($id))&&(!IsNullOrEmpty($type))&&(!IsNullOrEmpty($field)))
      {
        $qt = $type=="numeric"?"":"'";
        SQLProvider::ExecuteNonReturnQuery("delete from $this->tableName where $field=$qt$id$qt limit 1");
      }
      $this->mode = "list";
    }
    if ($this->mode=="customaction")
    {
      $action = GP(array($this->uniqueId,"action"));
      if (isset($this->customActions[$action]))
      {
        $args = GP($this->uniqueId);
        $this->customActions[$action]->CustomAction(&$this,&$args );
      }
    }
    if ($this->mode=="add")
    {
      if (CURLHandler::IsPost())
      {
        $table = new CNativeDataTable($this->tableName);
        $dataRow = $table->CreateNewRow(true);
        $fkeys = array_keys($this->fields);
        foreach ($fkeys as $fkey)
        {
          if ($this->fields[$fkey]->visibleOnAdd||$this->fields[$fkey]->activeOnAdd)
          {
            $this->fields[$fkey]->dataSource = &$dataRow;
            $this->fields[$fkey]->PreAdd();
          }
        }
        $params = GP($this->uniqueId);
        $dataRow->FromHashMap($params);
        $table->InsertObject(&$dataRow);
        foreach ($fkeys as $fkey)
        {
          if ($this->fields[$fkey]->visibleOnAdd||$this->fields[$fkey]->activeOnAdd)
          {
            $this->fields[$fkey]->dataSource = &$dataRow;
            if ($this->fields[$fkey]->PostAdd())
            {
              $table->UpdateObject(&$dataRow);
            }
          }
        }
        $params = array();
        $key = $this->keyId;
        CopyArray(&$_GET,&$params);
        $params[$this->uniqueId]["mode"] = "edit";
        $params[$this->uniqueId]["id"] = $dataRow->$key;
        $params[$this->uniqueId]["idField"] = $key;
        $params[$this->uniqueId]["idType"] = $this->keyType;
        $link = CURLHandler::BuildFullLink($params);
        CURLHandler::Redirect($link);
      }
    }
  }

  private function BuildAddRow()
  {

  }

  private function BuildTopAnchor()
  {
    return CURLHandler::IsPost()?"":"<a name=\"$this->uniqueId$"."top\"></a>";
  }

  public function RenderHTML()
  {
	//die('!!');
    $html = "";
    switch ($this->mode) {
      case "add":
      {
        $table = new CNativeDataTable($this->tableName);
        $dataRow = $table->CreateNewRow(true);
        $getParams = GetParam(array($this->uniqueId,"$"."autoValues"),"g",array());

        $dataRow->FromHashMap($getParams);
        $fkeys = array_keys($this->fields);

        /*handling postback*/

        $html.="<form method=\"post\" enctype=\"multipart/form-data\">".$this->BuildTopAnchor()."<table ".$this->BuildParams()." border=\"$this->border\">";

        foreach ($fkeys as $fkey) {
          if ($this->fields[$fkey]->visibleOnAdd)
          {
            $this->fields[$fkey]->dataSource = $dataRow;
            $html.="<tr>".$this->fields[$fkey]->RenderAddPair()."</tr>";
          }
        }
        $save = new CTextBox();
        $save->type= "submit";
        $save->value = $this->submitText;
        $cancel = new CTextBox();
        $cancel->type = "reset";
        $cancel->value = $this->resetText;
        $html.="<tr><td class=\"$this->submitClass\">".$save->RenderHTML()."</td><td class=\"$this->resetClass\">".$cancel->RenderHTML()."</td><tr>";
        $html.="</table></form>";

      }
      break;

      case "edit":
      {
        $id = GP(array($this->uniqueId,"id"));
        $type = GP(array($this->uniqueId,"idType"));
        $field = GP(array($this->uniqueId,"idField"));
        if ((!IsNullOrEmpty($id))&&(!IsNullOrEmpty($field)))
        {
          $table = new CNativeDataTable($this->tableName);
          $dataRow = $table->SelectUnique(new CEqFilter($table->fields[$field],$id));
          $fkeys = array_keys($this->fields);
          if (!is_null($dataRow))
          {
            /*handling postback*/
            if (CURLHandler::IsPost())
            {
              foreach ($fkeys as $fkey)
              {
                if ($this->fields[$fkey]->visibleOnEdit||$this->fields[$fkey]->activeOnEdit)
                {
                  $this->fields[$fkey]->dataSource = &$dataRow;
                  if ($this->fields[$fkey]->PreEdit())
                  {
                    $table->UpdateObject(&$dataRow);
                  }
                }
              }
              $params = GP($this->uniqueId);
              $dataRow->FromHashMap($params);
              $table->UpdateObject(&$dataRow);
              foreach ($fkeys as $fkey)
              {
                if ($this->fields[$fkey]->visibleOnEdit||$this->fields[$fkey]->activeOnEdit)
                {
                  $this->fields[$fkey]->dataSource = &$dataRow;
                  if ($this->fields[$fkey]->PostEdit())
                  {
                    $table->UpdateObject(&$dataRow);
                  }
                }
              }
            }
            $html.="<form method=\"post\" enctype=\"multipart/form-data\">".$this->BuildTopAnchor()."<table ".$this->BuildParams()." border=\"$this->border\">";

            foreach ($fkeys as $fkey) {
              if ($this->fields[$fkey]->visibleOnEdit)
              {
                $this->fields[$fkey]->dataSource = $dataRow;
                $html.="<tr>".$this->fields[$fkey]->RenderEditPair()."</tr>";
              }
            }
            $save = new CTextBox();
            $save->type= "submit";
            $save->value = $this->submitText;
            $cancel = new CTextBox();
            $cancel->type = "reset";
            $cancel->value = $this->resetText;
            $html.="<tr><td class=\"$this->submitClass\">".$save->RenderHTML()."</td><td class=\"$this->resetClass\">".$cancel->RenderHTML()."</td><tr>";
            $html.="</table></form>";
          }
        }
      }
      break;

      default:
      {
        $table = new CNativeDataTable($this->tableName);
        $table->page = $this->page;
        $table->pageSize = $this->pageSize;
        $fkeys = array_keys($this->fields);
        $getParams = GetParam(array($this->uniqueId,"$"."autoValues"),"g",array());
        if (sizeof($getParams)>0)
        {
          $gkeys = array_keys($getParams);
          $filter = null;
          foreach ($gkeys as $gkey)
          {
            foreach ($fkeys as $fkey)
            {
              if (isset($getParams[$gkey])){
                if (is_string($getParams[$gkey]) && trim($getParams[$gkey],'%'))
                  $this->findValue = trim($getParams[$gkey],'%');
                $this->fields[$fkey]->GetFilter($gkey,$getParams[$gkey],&$table,&$filter,$this->logicalOperator);
              }
            }
          }
          $table->filter = $filter;
        }
        if (!IsNullOrEmpty($this->orderField))
        {
          $table->SetSort($this->orderField,$this->orderType);
        }
        $html.="<table ".$this->BuildParams()." border=\"$this->border\">";
        //creating headers
        $html.="<tr>";
        $span = 0;
        foreach ($fkeys as $fkey) {
          if ($this->fields[$fkey]->visibleOnList)
          {
            $html.=$this->fields[$fkey]->RenderHead();
            $span++;
          }
        }
        $html.="</tr>";
        //creating rows
        $rows = $table->SelectRaw();
        $rkeys = array_keys($rows);
        $counter = 0;
        foreach ($rkeys as $rkey) {
          $html.="<tr>";

          foreach ($fkeys as $fkey)
          {
            if ($this->fields[$fkey]->visibleOnList)
            {
              $this->fields[$fkey]->dataSource = &$rows[$rkey];
              $this->fields[$fkey]->useAlter = $counter % 2==1;
              $html.=$this->fields[$fkey]->RenderItem();
            }
          }
          $html.="</tr>";
          $counter++;
        }
        if ($this->pageSize>0)
        {
          $filterString = is_null($table->filter)?"":" where ".$table->filter->ToSqlString()." ";
          $count = SQLProvider::ExecuteQuery("select count(*) as counter from  $this->tableName $filterString");
          $count = $count[0]["counter"];
          $pages = floor($count/$this->pageSize)+(($count%$this->pageSize==0)?0:1);
          $html.="<tr><td colspan=\"$span\">".$this->BuildPager($pages)."</td></tr>";
        }
        $addLink = new CLinkLabel();
        $params = array();
        CopyArray(&$_GET,&$params);
        $params[$this->uniqueId]["mode"] = "add";
        $addLink->href = CURLHandler::BuildFullLink($params)."#$this->uniqueId$"."top";
        $addLink->innerHTML = $this->addText;
        $html.="<tr><td colspan=\"$span\" class=\"$this->addClass\">".$addLink->RenderHTML()."</td></tr>";
        $html.="</table>";
      }
      break;
    }
    return $html;
  }
}
?>
