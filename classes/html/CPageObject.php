<?php
class CPageObject extends CObject
{
  public $uniqueId;
  public $pageId;
  public $relativeId;
  public $parentId;
  public $controls = array();
  public $ownerPage;

  public $dataSource = array();

  public function CPageObject()
  {
    $this->CObject();
  }

  public static function GetBindableData(&$dataSource)
  {
    return CDataSource::GetBindableData(&$dataSource);
  }

  public function GetDataSourceData()
  {
    return self::GetBindableData(&$this->dataSource);
  }

  public function PreInit()
  {

  }

  public function PostInit()
  {

  }

  public function SetUniqueId()
  {
    if (is_null($this->uniqueId))
    {
      $this->uniqueId = $this->parentId.CONTROL_SEMISECTOR.$this->relativeId;
    }
  }

  public function Init()
  {
    $this->PostInit();
  }
}
?>