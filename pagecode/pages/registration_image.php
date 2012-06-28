<?php
class registration_image_php extends CPageCodeHandler
{
	public function registration_image_php()
	{
		$this->CPageCodeHandler();
	}

	private function GetPID($ptype)
	{
		$su = new CSessionUser($ptype=="junksale"?"user":$ptype);
		if ($ptype=="junksale")
			CAuthorizer::RestoreUserFromSession(&$su);

    if ($su->authorized){
			if ($ptype=="junksale"){
				$pid = GP("pid",-1);
				$pidList = SQLProvider::ExecuteQuery("select owner_id from tbl__baraholka_sell where tbl_obj_id=$pid");
				if (sizeof($pidList)==0)
					return null;
				return $pidList[0]["owner_id"]==$su->id?$pid:null;
			}
			return $su->id;
		}
		return null;
	}

	public function GetExistingPID($id)
	{
		$imp = SQLProvider::ExecuteQuery("select parent_id from tbl__area_photos where child_id=$id");
		if (sizeof($imp)>0)
			return $imp[0]["parent_id"];

    $imp = SQLProvider::ExecuteQuery("select parent_id from tbl__contractor_photos where child_id=$id");
		if (sizeof($imp)>0)
			return $imp[0]["parent_id"];

    $imp = SQLProvider::ExecuteQuery("select parent_id from tbl__artist2photos where child_id=$id");
		if (sizeof($imp)>0)
			return $imp[0]["parent_id"];

    $imp = SQLProvider::ExecuteQuery("select parent_id from tbl__agency_photos where child_id=$id");
		if (sizeof($imp)>0)
			return $imp[0]["parent_id"];

    $imp = SQLProvider::ExecuteQuery("select parent_id from tbl__photo2baraholka where child_id=$id");
		if (sizeof($imp)>0)
			return $imp[0]["parent_id"];

    return null;
	}

	private  function CreateImageName($id,$files,$key,$prefix = IMAGE_BASE_PREFIX,$sizeLimit =IMAGE_SIZE_LIMIT)
	{
		if (isset($files["name"][$key]) &&
		    $files["error"][$key] === 0 &&
        is_file($files["tmp_name"][$key]) &&
        $files["size"][$key]<=$sizeLimit){
      $ext = ".".pathinfo($files["name"][$key], PATHINFO_EXTENSION);
      $newfile = "$id".IMAGE_PATH_SEMISECTOR.$prefix.$ext;
      $newpath = IMAGES_UPLOAD_DIR.$newfile;
      if (is_file(ROOTDIR.$newpath))
        unlink(ROOTDIR.$newpath);
      return $newfile;
		}
    return null;
	}

	private function CleanImage(&$file)
	{
		if (is_file(ROOTDIR.IMAGES_UPLOAD_DIR.$file))
			unlink(ROOTDIR.IMAGES_UPLOAD_DIR.$file);
		//$file = "";
	}

	private function UpdateParent($id,$ptype)
	{
		$su = new CSessionUser($ptype);
		if ($ptype=="junksale")
			CAuthorizer::RestoreUserFromSession(&$su);
		if ($su->authorized){
			switch ($ptype) {
				case "area":
				SQLProvider::ExecuteNonReturnQuery("delete from tbl__area_photos where child_id=$id");
				SQLProvider::ExecuteNonReturnQuery("insert into tbl__area_photos values($id,$su->id)");
				break;
				case "contractor":
				SQLProvider::ExecuteNonReturnQuery("delete from tbl__contractor_photos where child_id=$id");
				SQLProvider::ExecuteNonReturnQuery("insert into tbl__contractor_photos values($id,$su->id)");
				break;
				case "artist":
				SQLProvider::ExecuteNonReturnQuery("delete from tbl__artist2photos where child_id=$id");
				SQLProvider::ExecuteNonReturnQuery("insert into tbl__artist2photos values($id,$su->id)");
				break;
        case "agency":
				SQLProvider::ExecuteNonReturnQuery("delete from tbl__agency_photos where child_id=$id");
				SQLProvider::ExecuteNonReturnQuery("insert into tbl__agency_photos values($id,$su->id)");
				break;
				case "junksale":
				$pid = GP("pid");
				if (is_numeric($pid)){
					SQLProvider::ExecuteNonReturnQuery("delete from tbl__photo2baraholka where child_id=$id");
					SQLProvider::ExecuteNonReturnQuery("insert into tbl__photo2baraholka values($id,$pid)");
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
			CURLHandler::Redirect("/authorizationrequired?target=".$_SERVER['REQUEST_URI']);

    $table = new CNativeDataTable("tbl__photo");
		$photoID = GP("id");
		$ptype = GP("ptype");
		$pid = $this->GetPID($ptype);
		$imageRow =null;
		$error = "";

		if (is_null($photoID))
			$imageRow = $table->CreateNewRow(true);
		else{
			$imageRow = $table->SelectUnique(new CEqFilter(&$table->fields["tbl_obj_id"],$photoID));
			if (is_null($imageRow)){
				$imageRow = $table->CreateNewRow(true);
				$photoID = null;
			}
			else{
				$exPid =  $this->GetExistingPID($imageRow->tbl_obj_id);
				if (($pid!=$exPid)&&(is_numeric($exPid)))
					$error = $this->GetMessage("noright");
			}
		}

		if ((!is_numeric($pid))||(!IsNullOrEmpty($error))){
			if (IsNullOrEmpty($error))
				$error= $this->GetMessage("nopid");
		}
		else{
			if ($this->IsPostBack){
				$props = GP("properties");
				$imageValidator = $this->GetControl("imageValidator");
				$errorsData = $imageValidator->Validate(&$props);
				$imageRow->FromHashMap($props);
				if (sizeof($errorsData)>0)
				{
					$error = $errorsData[0];
				}
				else
				{
					if (is_null($photoID)){
						$table->InsertObject(&$imageRow);
					}
					if (GP("l_image_clean")=="clean"){
						$this->CleanImage($imageRow->l_image);
						$this->CleanImage($imageRow->m_image);
						$this->CleanImage($imageRow->s_image);
					}
					$images = $_FILES["properties"];
					if (is_array($images))
					{
						$lFilename = $this->CreateImageName($imageRow->tbl_obj_id,$images,"l_image",IMAGE_FOTO_PREFIX,IMAGE_LAGRE_SIZE_LIMIT);
            if (!is_null($lFilename)){
              $mFilename = $this->CreateImageName($imageRow->tbl_obj_id,$images,"l_image",IMAGE_FOTO_PREFIX."_medi",IMAGE_LAGRE_SIZE_LIMIT);
              $sFilename = $this->CreateImageName($imageRow->tbl_obj_id,$images,"l_image",IMAGE_FOTO_PREFIX."_thumb",IMAGE_LAGRE_SIZE_LIMIT);

              $res = new ResizeImage($images["tmp_name"]["l_image"]);
              $res->resize(IMAGE_RES_MAX_WIDTH, IMAGE_RES_MAX_HEIGHT,
                           ROOTDIR.IMAGES_UPLOAD_DIR.$lFilename, false);
              $res->resize(IMAGE_RES_MEDIUM_WIDTH, IMAGE_RES_MEDIUM_HEIGHT,
                           ROOTDIR.IMAGES_UPLOAD_DIR.$mFilename, false);
              $res->resize(IMAGE_RES_THUMB_WIDTH, IMAGE_RES_THUMB_HEIGHT,
                           ROOTDIR.IMAGES_UPLOAD_DIR.$sFilename, false);
              unset($res);
              $imageRow->l_image = $lFilename;
              $imageRow->m_image = $mFilename;
              $imageRow->s_image = $sFilename;
            }
					}
					$table->UpdateObject(&$imageRow);
					$this->UpdateParent($imageRow->tbl_obj_id,$ptype);
					if (is_null($photoID))
					{
						$rewriteParams = $_GET;
						$rewriteParams["id"] = $imageRow->tbl_obj_id;
						$url = CURLHandler::$currentPath.CURLHandler::BuildQueryParams($rewriteParams);
						CURLHandler::Redirect($url);
					}
				}
			}
		}
		$errors = $this->GetControl("errors");
		$errors->dataSource = array("message"=>$error);
		$imageRow->s_image = IsNullOrEmpty($imageRow->s_image)?"front/nofotogal.gif":$imageRow->s_image;
		$imageRow->m_image = IsNullOrEmpty($imageRow->m_image)?"front/nofotogal.gif":$imageRow->m_image;
		$imageRow->l_image = IsNullOrEmpty($imageRow->l_image)?"front/nofotogal.gif":$imageRow->l_image;
		$image = $this->GetControl("image");
		$image->dataSource = $imageRow;

	}
}
?>
