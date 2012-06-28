<?php
class CFileGridDataField extends CGridDataField
{
	public $uploadLink;

	public $clearText;

	public $clearPostfix = "_delete";

	public $uploadPath;

	public $uploadTemplate;

	public $saveFormat;

	public $splitValue = true;

	public function CFileGridDataField()
	{
		$this->CGridDataField();
	}

	public function PostInit()
	{
		ValidateDir(&$this->uploadLink);
	}

	public function RenderItem()
	{
		$value = $this->GetValue();
		$files = preg_split("/[\\/]/",$value);
		$filename = $files[sizeof($files)-1];
		$file = new CLinkLabel();
		$file->href = $this->uploadLink.($this->splitValue?$filename:$value);
		$file->innerHTML = $filename;
		$file->target = "_blank";
		return CStringFormatter::Format($this->itemTemplate,
		array("class"=>$this->BuildItemClassString(),"value"=>$file->RenderHTML()));
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
		$value = $this->GetValue();
		$ibox = new CTextBox();
		$ibox->name = "$this->parentId[$this->dbField]";
		$ibox->type = "file";
		$files = preg_split("/[\\/]/",$value);
		$filename = $files[sizeof($files)-1];
		$file = new CLinkLabel();
		$file->href = $this->uploadLink.($this->splitValue?$filename:$value);
		$file->innerHTML = $filename;
		$file->target = "_blank";
		$cbox = new CTextBox();
		$cbox->name = "$this->parentId[$this->dbField$this->clearPostfix]";
		$cbox->type = "checkbox";
		$cbox->value = "clear";
		return CStringFormatter::Format($this->editModeTemplate,
		array("class"=>$this->BuildClassString($this->editClass),"value"=>$file->RenderHTML()."<br/>".$cbox->RenderHTML().$this->clearText."<br/>".$ibox->RenderHTML()));
	}

	protected function ClearFile($filename)
	{
		$path = RealDir($this->uploadPath);
		if (is_file($path.$filename))
		{
			unlink($path.$filename);
		}
	}

	protected function SaveFile($overwrite = true)
	{
		$filedata = null;
		if (isset($_FILES[$this->parentId]))
		{
			$filedata = $_FILES[$this->parentId];
		}
		else
		{
			return false;
		}
		if (!isset($filedata["name"][$this->dbField]))
		{
			return false;
		}
		if (!($filedata["error"][$this->dbField]==0)&&(is_file($filedata["tmp_name"][$this->dbField])))
		{
			return false;
		}
		$filename = null;
		if (IsNullOrEmpty($this->uploadTemplate))
		{
			$filename = $filedata["name"][$this->dbField];
		}
		else
		{
			$fileparams = $this->GetDataSourceData();
			$fileparams[$this->dbField] = $filedata["name"][$this->dbField];
			$fkeys = array_keys($fileparams);
			foreach ($fkeys as $fkey) {
				$fileparams[$fkey] = PrepareImagePathPart($fileparams[$fkey]);
			}
			$filename = CStringFormatter::Format($this->uploadTemplate,$fileparams);
		}
		$path = RealDir($this->uploadPath);
		if (is_dir($path))
		{
			if (is_file($path.$filename))
			{
				if ($overwrite)
				{
					unlink($path.$filename);
				}
				else
				{
					return false;
				}
			}
			if (move_uploaded_file($filedata["tmp_name"][$this->dbField],$path.$filename))
			{
				$this->SetValue($filename);
				return true;
			}
			else
			{
				return false;
			}
		}
		return false;
	}

	public function PostAdd()
	{
		
		$result = false;
		if (GetParam(array($this->parentId,$this->dbField.$this->clearPostfix))=="clear")
		{
			$value = $this->GetValue();
			if ($this->splitValue)
			{
				$files = preg_split("/[\\/]/",$value);
				$this->ClearFile( $files[sizeof($files)-1]);
				$this->SetValue("");
				$result = true;
			}
			else
			{
				$this->ClearFile( $value);
				$this->SetValue("");
				$result = true;
			}

		}
		$result = $result||$this->SaveFile();
		return $result;
	}
	
	public function PostEdit()
	{
		$result = false;
		if (GetParam(array($this->parentId,$this->dbField.$this->clearPostfix))=="clear")
		{
			$value = $this->GetValue();
			if ($this->splitValue)
			{
				$files = preg_split("/[\\/]/",$value);
				$this->ClearFile( $files[sizeof($files)-1]);
				$this->SetValue("");
				$result = true;
			}
			else
			{
				$this->ClearFile( $value);
				$this->SetValue("");
				$result = true;
			}

		}
		$result = $result||$this->SaveFile();
		return $result;
	}
}
?>