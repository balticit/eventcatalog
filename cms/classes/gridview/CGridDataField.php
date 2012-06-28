<?php
class CGridDataField extends CPageObject
{
  public $dbField;
  public $label;
  public $headerTemplate = "<th {class}><a href=\"{orderLink}\" >{value}{ordered}</a></th>";
  public $itemTemplate = "<td {class}>{value}</td>";
  public $headerClass;
  public $itemClass;
  public $alterItemClass;
  public $useAlter;
  public $parentId;
  public $ordered;
  public $customTemplate;
  public $customAction;
  public $visibleOnEdit = true;
  public $visibleOnAdd = true;
  public $visibleOnList = true;
  public $activeOnAdd = false;
  public $activeOnEdit = false;
  public $labelTemplate = "<td {class}>{value}</td>";
  public $labelClass;
  public $editModeTemplate = "<td {class}>{value}</td>";
  public $addModeTemplate = "<td {class}>{value}</td>";
  public $addClass;
  public $editClass;
  public $sortable = true;
  public $preAddEvent;
  public $preEditEvent;
  public static $_preAddHandler = "_preAddEvent";
  public static $_preEditHandler = "_preEditEvent";

  protected $subParams = array();

  public function CGridDataField()
  {
    $this->CPageObject();
  }

  public function __set($name,$value)
  {
    $this->subParams[$name] = $value;
  }

  public function __get($name)
  {
    return isset($this->subParams[$name])?$this->subParams[$name]:null;
  }

  protected function BuildClassString($class)
  {
    return IsNullOrEmpty($class)?"":"class=\"$class\"";
  }

  protected  function BuildItemClassString()
  {
    $class = IsNullOrEmpty($this->alterItemClass)?$this->itemClass:
    ($this->useAlter)?$this->alterItemClass:$this->itemClass;
    return IsNullOrEmpty($class)?"":"class=\"$class\"";
  }

  public function PostInit()
  {
    if (!IsNullOrEmpty($this->preAddEvent))
    {
      $this->AddEvent(CGridDataField::$_preAddHandler,$this->preAddEvent,$this->ownerPage);
    }
    if (!IsNullOrEmpty($this->preEditEvent))
    {
      $this->AddEvent(CGridDataField::$_preEditHandler,$this->preEditEvent,$this->ownerPage);
    }
  }

  public function RenderHead()
  {
    $link = "javascript:return false;";
    $cOrder = GP(array($this->parentId,"orderField"));
    if ($this->sortable)
    {
      $params = array();
      CopyArray(&$_GET,&$params);

      $cType = GP(array($this->parentId,"orderType"));
      $type = ($cOrder!=$this->dbField)?"asc":(($cType=="asc")?"desc":"asc");
      $params[$this->parentId]["orderField"] = $this->dbField;
      $params[$this->parentId]["orderType"]  = $type;
      $link = CURLHandler::BuildFullLink($params);
    }
    $order = "";
    if (($cOrder==$this->dbField)&&$this->sortable)
    {
      $cType = GP(array($this->parentId,"orderType"));
      $order = $cType=="asc"?'<img src="/images/asc.gif" style="border:none;" alt=""/>':'<img src="/images/desc.gif" style="border:none;" alt=""/>';
    }
    return CStringFormatter::Format($this->headerTemplate,
    array("class"=>$this->BuildClassString($this->headerClass),
    "value"=>$this->label,"orderLink"=>$link,"ordered"=>$order));
  }

  public function CustomAction(&$sender,&$args = array())
  {

  }

  protected function GetValue($key = null)
  {
    if (empty($key))
      $key = $this->dbField;
    if (is_object($this->dataSource))
      return  $this->dataSource->$key;
    elseif (is_array($this->dataSource))
      return  $this->dataSource[$key];
    else
      return  $this->dataSource;
  }

  protected function SetValue($value)
  {
    $key = $this->dbField;
    if (is_object($this->dataSource))
    {
      $this->dataSource->$key = $value;
    }
    elseif (is_array($this->dataSource))
    {
      $this->dataSource[$key] = $value;
    }
    else
    {
      $this->dataSource = $value;
    }
  }

  protected function SetKeyValue($key,$value)
  {
    if (is_object($this->dataSource))
    {
      $this->dataSource->$key = $value;
    }
    elseif (is_array($this->dataSource))
    {
      $this->dataSource[$key] = $value;
    }
    else
    {
      $this->dataSource = $value;
    }
  }

  public function RenderItem()
  {
    $value = $this->GetValue();
    $cellContent = IsNullOrEmpty($value)? "&nbsp;":$value;
    return CStringFormatter::Format($this->itemTemplate,
    array("class"=>$this->BuildItemClassString(),"value"=>$cellContent));
  }

  protected function RenderLabel()
  {
    return CStringFormatter::Format($this->labelTemplate,
    array("class"=>$this->BuildClassString($this->labelClass),"value"=>$this->label));
  }

  public function RenderAddLabel()
  {
    return $this->RenderLabel();
  }

  public function RenderEditLabel()
  {
    return $this->RenderLabel();
  }

  public function RenderAddItem()
  {
    $ibox = new CTextBox();
    $ibox->name = "$this->parentId[$this->dbField]";
    $ibox->value = $this->GetValue();
    return CStringFormatter::Format($this->addModeTemplate,
    array("class"=>$this->BuildClassString($this->addClass),"value"=>$ibox->RenderHTML()));
  }

  public function RenderEditItem()
  {
    $ibox = new CTextBox();
    $ibox->name = "$this->parentId[$this->dbField]";
    $ibox->value = $this->GetValue();
    return CStringFormatter::Format($this->editModeTemplate,
    array("class"=>$this->BuildClassString($this->editClass),"value"=>$ibox->RenderHTML()));
  }

  public function RenderAddPair()
  {
    return $this->RenderAddLabel().$this->RenderAddItem();
  }

  public function RenderEditPair()
  {
    return $this->RenderEditLabel().$this->RenderEditItem();
  }

  public function GetFilter($fieldName,$value,&$table,&$filter,$logicalOperator)
  {
    if ($this->dbField==$fieldName){
      $stp = (strpos($value,"%")===0);
      $fp = $value?(strpos($value,"%",strlen($value)-1)===(strlen($value)-1)):false;
      if ($stp||$fp){
        $stp = $stp?"*":"";
        $fp = $fp?"*":"";
        if ($logicalOperator == "or")
          $filter = new COrFilter($filter, new CLikeFilter(&$table->fields[$this->dbField],trim($value,"%"),"$stp.$fp"));
        else
          $filter = new CAndFilter($filter, new CLikeFilter(&$table->fields[$this->dbField],trim($value,"%"),"$stp.$fp"));
      }
      else{
        if ($logicalOperator == "or")
          $filter = new COrFilter($filter, new CEqFilter(&$table->fields[$this->dbField],$value));
        else
          $filter = new CAndFilter($filter, new CEqFilter(&$table->fields[$this->dbField],$value));

      }
    }
  }

  public function PreAdd()
  {
    $args = array("data"=>&$this->dataSource);
    $this->RaiseEvent(CDBLookupGridDataField::$_preAddHandler,$args);
    return false;
  }
  public function PreEdit()
  {
    $args = array("data"=>&$this->dataSource);
    $this->RaiseEvent(CDBLookupGridDataField::$_preEditHandler,$args);
    return false;
  }
  public function PostAdd(){return false;}
  public function PostEdit(){return false;}

}
?>
