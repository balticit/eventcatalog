<?php
class registration_upload_php extends CPageCodeHandler
{
	public function registration_upload_php()
	{
		$this->CPageCodeHandler();
	}

	private function GetPID($ptype)
	{
		$su = new CSessionUser($ptype);
		if ($su->authorized)
		{
			return $su->id;
		}
		return null;
	}

	public function GetExistingPID($id)
	{
		$imp = SQLProvider::ExecuteQuery("select artist_id from tbl__artist2mp3file where file_id=$id");
		if (sizeof($imp)>0)
		{
			return $imp[0]["artist_id"];
		}
		$imp = SQLProvider::ExecuteQuery("select artist_id from tbl__artist2video where file_id=$id");
		if (sizeof($imp)>0)
		{
			return $imp[0]["artist_id"];
		}
		return null;
	}

	public  function CreateImage($id,$files,$key,$prefix = UPLOAD_BASE_PREFIX,$sizeLimit =MAX_UPLOAD_FILE_SIZE,$overwrite = true)
	{
		if (isset($files["name"][$key]))
		{
			if (($files["error"][$key]==0)&&(is_file($files["tmp_name"][$key]))&&($files["size"][$key]<=$sizeLimit) )
			{
				$fn =preg_replace("/[^A-Za-z0-9_\.]/","_",str_replace(IMAGE_PATH_SEMISECTOR,"_",$files["name"][$key]));
				$newfile = "$id".IMAGE_PATH_SEMISECTOR.$prefix.IMAGE_PATH_SEMISECTOR.$fn;
				$newpath = IMAGES_UPLOAD_DIR.$newfile;
				if (is_file(ROOTDIR.$newpath))
				{
					if ($overwrite)
					{
						unlink(ROOTDIR.$newpath);
					}
					else
					{
						return null;
					}
				}
				return  (move_uploaded_file($files["tmp_name"][$key],ROOTDIR.$newpath))?$newfile:null;
			}
		}
	}

	private function CleanImage(&$file)
	{
		if (is_file(ROOTDIR.IMAGES_UPLOAD_DIR.$file))
		{
			unlink(ROOTDIR.IMAGES_UPLOAD_DIR.$file);
		}
		$file = "";
	}

	private function UpdateParent($id,$ptype,$type)
	{
		$su = new CSessionUser($ptype);
		if ($su->authorized)
		{
			switch ($ptype) {
				case "artist":
				{
					switch ($type) {
						case "mp3":
							SQLProvider::ExecuteNonReturnQuery("delete from tbl__artist2mp3file where file_id=$id");
							SQLProvider::ExecuteNonReturnQuery("insert into tbl__artist2mp3file(file_id,artist_id) values($id,$su->id)");
							break;
						case "video":
								SQLProvider::ExecuteNonReturnQuery("delete from tbl__artist2video where file_id=$id");
								SQLProvider::ExecuteNonReturnQuery("insert into tbl__artist2video(file_id,artist_id) values($id,$su->id)");
								break;
						default:
							break;
					}
					
				}
				break;
			}
		}
	}

	public function PreRender()
	{
		$su = new CSessionUser("user");
		CAuthorizer::RestoreUserFromSession(&$su);
		if (!$su->authorized)
		{
			CURLHandler::Redirect("/authorizationrequired?target=".$_SERVER['REQUEST_URI']);
		}
		$table = new CNativeDataTable("tbl__upload");
		$fileID = GP("id");
		$ptype = GP("ptype");
		$type = GP("type");
		$pid = $this->GetPID($ptype);
		$fileRow =null;
		$error = "";
		if (is_null($fileID))
		{
			$fileRow = $table->CreateNewRow(true);
		}
		else
		{
			$fileRow = $table->SelectUnique(new CEqFilter(&$table->fields["tbl_obj_id"],$fileID));
			if (is_null($fileRow))
			{
				$fileRow = $table->CreateNewRow(true);
				$fileID = null;
			}
			else
			{
				$exPid =  $this->GetExistingPID($fileRow->tbl_obj_id);
				if (($pid!=$exPid)&&(is_numeric($exPid)))
				{
					$error = $this->GetMessage("noright");
				}
			}
		}
		if ((!is_numeric($pid))||(!IsNullOrEmpty($error)))
		{
			if (IsNullOrEmpty($error))
			{
				$error= $this->GetMessage("nopid");
			}
		}
		else
		{
			if ($this->IsPostBack)
			{
				$props = GP("properties");
				$imageValidator = $this->GetControl("uploadValidator");
				$errorsData = $imageValidator->Validate(&$props);
				$fileRow->FromHashMap($props);
				if (sizeof($errorsData)>0)
				{
					$error = $errorsData[0];
				}
				else
				{
					if (is_null($fileID))
					{
						$table->InsertObject(&$fileRow);
					}
					if (GP("file_clean")=="clean")
					{
						$this->CleanImage($fileRow->file);
					}
					$images = $_FILES["properties"];
					if (is_array($images))
					{
						$mim = $this->CreateImage($fileRow->tbl_obj_id,$images,"file");
						if (!is_null($mim))
						{
							$fileRow->file = $mim;
						}
					}
					$table->UpdateObject(&$fileRow);
					$this->UpdateParent($fileRow->tbl_obj_id,$ptype,$type);
					if (is_null($fileID))
					{
						$rewriteParams = CURLHandler::$rewriteParams;
						$rewriteParams["id"] = $fileRow->tbl_obj_id;
						$url = CURLHandler::$currentPath.CURLHandler::BuildRewriteParams($rewriteParams);
						CURLHandler::Redirect($url);
					}
				}
			}
		}
		$errors = $this->GetControl("errors");
		$errors->dataSource = array("message"=>$error);
    $upl = $this->GetControl("upl");
		$upl->dataSource = $fileRow;
	}
}
?>
