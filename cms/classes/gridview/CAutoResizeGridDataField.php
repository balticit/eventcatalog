<?php
class CAutoResizeGridDataField extends CGridDataField
{
	public $uploadLink;
	public $clearText;
	public $clearPostfix = "_delete";
	public $uploadPath = IMAGES_UPLOAD_DIR;
	public $uploadTemplate;
	public $saveFormat;
	public $splitValue = true;

	public $largeImageField;
	public $mediImageField;
	public $smallImageField;

	public $largeImageWidth  = IMAGE_RES_MAX_WIDTH;
	public $largeImageHeight = IMAGE_RES_MAX_HEIGHT;
  public $mediImageWidth   = IMAGE_RES_MEDIUM_WIDTH;
	public $mediImageHeight  = IMAGE_RES_MEDIUM_HEIGHT;
	public $smallImageWidth  = IMAGE_RES_THUMB_WIDTH;
	public $smallImageHeight = IMAGE_RES_THUMB_HEIGHT;

	public $largeImageTemplate = "{tbl_obj_id}-foto{l_image}";
	public $mediImageTemplate = "{tbl_obj_id}-foto_medi{m_image}";
	public $smallImageTemplate = "{tbl_obj_id}-foto_thumb{s_image}";


  public function CFileGridDataField()
	{
		$this->CGridDataField();
	  $this->visibleOnList = 0;
    $this->$largeImageTemplate = "{tbl_obj_id}".IMAGE_PATH_SEMISECTOR.IMAGE_FOTO_PREFIX."{}";
	  $this->$mediImageTemplate = "{tbl_obj_id}".IMAGE_PATH_SEMISECTOR.IMAGE_FOTO_PREFIX."_medi{}";
	  $this->$smallImageTemplate = "{tbl_obj_id}".IMAGE_PATH_SEMISECTOR.IMAGE_FOTO_PREFIX."_thumb{}";
	}

  protected function GetValue()
	{
    return null;
	}

  public function RenderHead()
  {
    return null;
  }

  public function RenderItem()
  {
    return null;
  }

	public function PostInit()
	{
		ValidateDir(&$this->uploadLink);
	}

	public function RenderAddItem()
	{
		$ibox = new CTextBox();
		$ibox->name = "$this->parentId[$this->dbField]";
		$ibox->type = "file";
		return CStringFormatter::Format($this->addModeTemplate,
		  array("class"=>$this->BuildClassString($this->addClass),"value"=>$ibox->RenderHTML()));
	}

	public function RenderEditItem()
	{
		$ibox = new CTextBox();
		$ibox->name = "$this->parentId[$this->dbField]";
		$ibox->type = "file";
		return CStringFormatter::Format($this->editModeTemplate,
		  array("class"=>$this->BuildClassString($this->editClass),"value"=>$ibox->RenderHTML()));
	}


	protected function SaveFile()
	{
		$filedata = null;
		if (isset($_FILES[$this->parentId]))
			$filedata = $_FILES[$this->parentId];
		else
			return false;

		if (!(isset($filedata["name"][$this->dbField]) &&
		      $filedata["error"][$this->dbField]==0 &&
					is_file($filedata["tmp_name"][$this->dbField]) ))
			return false;

		$filename = null;

		$fileparams = $this->GetDataSourceData();
    $ext = ".".pathinfo($filedata["name"][$this->dbField], PATHINFO_EXTENSION);
		$fileparams[$this->largeImageField] = $ext;
		$fileparams[$this->mediImageField] = $ext;
		$fileparams[$this->smallImageField] = $ext;
		foreach ($fileparams as &$value)
			$value = PrepareImagePathPart($value);

    $path = RealDir($this->uploadPath);
    if(!is_dir($path))
      return false;

		$lFilename = CStringFormatter::Format($this->largeImageTemplate,$fileparams);
    $mFilename = CStringFormatter::Format($this->mediImageTemplate,$fileparams);
    $sFilename = CStringFormatter::Format($this->smallImageTemplate,$fileparams);

		if (is_file($path.$lFilename))
      unlink($path.$lFilename);
    if (is_file($path.$mFilename))
      unlink($path.$mFilename);
    if (is_file($path.$sFilename))
      unlink($path.$sFilename);

    $res = new ResizeImage($filedata["tmp_name"][$this->dbField]);
    $res->resize($this->largeImageWidth, $this->largeImageHeight, $path.$lFilename, false);
    $res->resize($this->mediImageWidth, $this->mediImageHeight, $path.$mFilename, false);
    $res->resize($this->smallImageWidth, $this->smallImageHeight, $path.$sFilename, false);
    $this->SetKeyValue($this->largeImageField,$lFilename);
    $this->SetKeyValue($this->mediImageField,$mFilename);
    $this->SetKeyValue($this->smallImageField,$sFilename);
    unset($res);
    return true;
	}

	public function PostAdd()
	{
		return $this->SaveFile();
	}

	public function PostEdit()
	{
		return $this->SaveFile();
	}
}
?>
