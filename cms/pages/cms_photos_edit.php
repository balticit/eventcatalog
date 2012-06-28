<?php
class cms_photos_edit_php extends CCMSPageCodeHandler
{
	public function cms_photos_edit_php()
	{
		$this->CCMSPageCodeHandler();
	}

	private function CreateImage($id,$files,$key,$prefix = IMAGE_BASE_PREFIX,$sizeLimit =IMAGE_SIZE_LIMIT,$overwrite = true)
	{
		if (isset($files["name"][$key]))
		{
			if (($files["error"][$key]==0)&&(is_file($files["tmp_name"][$key]))&&($files["size"][$key]<=$sizeLimit) )
			{
				$fn =preg_replace("/[^A-Za-z0-9_\.]/","_",str_replace(IMAGE_PATH_SEMISECTOR,"_",$files["name"][$key]));
				$newpath = IMAGES_UPLOAD_DIR."$id".IMAGE_PATH_SEMISECTOR.$prefix.IMAGE_PATH_SEMISECTOR.$fn;
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
				if ($key!='s_image') {
					$im = imagecreatefromjpeg($files["tmp_name"][$key]);
					$mark = imagecreatefromjpeg(ROOTDIR."images/ss.gif");
					imagecopyresampled($im,$mark,imagesx($im)-imagesx($mark),imagesy($im)-imagesy($mark),0,0,50,19,imagesx($mark),imagesy($mark));
					imagejpeg($im,ROOTDIR.$newpath,8);
				}
				else
					return  (move_uploaded_file($files["tmp_name"][$key],ROOTDIR.$newpath))?$newpath:null;
			}
		}
	}

	private function CleanImage(&$file)
	{
		if (is_file(ROOTDIR.$file))
		{
			unlink(ROOTDIR.$file);
		}
		$file = "";
	}


	public function PreRender()
	{
		$table = new CNativeDataTable("tbl__photo");
		$photoID = GP("id",0);
		$error = "";
		$image = $this->GetControl("account");
		$imageRow = $table->SelectUnique(new CEqFilter(&$table->fields["tbl_obj_id"],$photoID));
		if (is_null($imageRow))
		{
			$errors = $this->GetControl("errors");
			$errors->dataSource = array("message"=>"Запись не найдена");
			$image->template = "";
		}
		else
		{

			if ($this->IsPostBack)
			{
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
					if (GP("s_image_clean")=="clean")
					{
						$this->CleanImage($imageRow->s_image);
					}
					if (GP("m_image_clean")=="clean")
					{
						$this->CleanImage($imageRow->m_image);
					}
					if (GP("l_image_clean")=="clean")
					{
						$this->CleanImage($imageRow->l_image);
					}
					$images = $_FILES["properties"];
					if (is_array($images))
					{

						$sim = $this->CreateImage($imageRow->tbl_obj_id,$images,"s_image",IMAGE_BASE_PREFIX,IMAGE_LOGO_SIZE_LIMIT);
						if (!is_null($sim))
						{
							$imageRow->s_image = $sim;
						}
						$mim = $this->CreateImage($imageRow->tbl_obj_id,$images,"m_image");
						if (!is_null($mim))
						{
							$imageRow->m_image = $mim;
						}
						$lim = $this->CreateImage($imageRow->tbl_obj_id,$images,"l_image",IMAGE_BASE_PREFIX,IMAGE_LAGRE_SIZE_LIMIT);
						if (!is_null($lim))
						{
							$imageRow->l_image = $lim;
							mysql_query("update tbl_photo set width='800', height='600' where tbl_obj_id=$photoID");
							//$imageRow->width = 800;
							//$imageRow->height = 600;
						}
					}
					$table->UpdateObject(&$imageRow);
				}
			}

			$errors = $this->GetControl("errors");
			$errors->dataSource = array("message"=>$error);
			$imageRow->s_image = IsNullOrEmpty($imageRow->s_image)?"front/nofotogal.gif":$imageRow->s_image;
			$imageRow->m_image = IsNullOrEmpty($imageRow->m_image)?"front/nofotogal.gif":$imageRow->m_image;
			$imageRow->l_image = IsNullOrEmpty($imageRow->l_image)?"front/nofotogal.gif":$imageRow->l_image;
			
			$image->dataSource = $imageRow;
		}
	}
}
?>
