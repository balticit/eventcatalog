<?php
class CGridTitleURLField extends CGridDataField
{
  public $dbField = "title_url";
  public $titleField = "title";
  public $activeOnAdd = true;
  protected $subParams = array();

  public function CGridTitleURLField()
  {
    $this->CGridDataField();
  }


  public function RenderHead()
  {
    return null;
  }

  public function RenderItem()
  {
    return null;
  }

  protected function RenderLabel()
  {
    return null;
  }

  public function RenderAddItem()
  {
    return null;
  }

  public function RenderEditItem()
  {
    return null;
  }

  public function PreAdd()
  {
    return false;
  }
  public function PreEdit()
  {
    $args = array("data"=>&$this->dataSource);
    $this->RaiseEvent(CDBLookupGridDataField::$_preEditHandler,$args);
    return false;
  }

  protected function changeUrl()
  {
    $key = $this->dbField;
    $keySource = $this->titleField;
    if (is_object($this->dataSource))
    {
      $this->dataSource->$key = translitURL($this->dataSource->$keySource);
    }
    elseif (is_array($this->dataSource))
    {
      $this->dataSource[$key] = translitURL($this->dataSource[$keySource]);
    }
    else
    {
      $this->dataSource = null;
    }
  }

  public function PostAdd(){
    $this->changeUrl();
    return true;
  }
  public function PostEdit()
  {
    $this->changeUrl();
    return true;
  }
}
?>