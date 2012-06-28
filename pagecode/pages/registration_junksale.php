<?php
class registration_junksale_php extends CPageCodeHandler
{
	public function registration_junksale_php()
	{
		$this->CPageCodeHandler();
	}

	private function CreateImage($id,$files,$key,$prefix = IMAGE_BASE_PREFIX,$sizeLimit =IMAGE_SIZE_LIMIT,$overwrite = true)
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

	public function PreRender()
	{
		$su = new CSessionUser("user");
		CAuthorizer::RestoreUserFromSession(&$su);
		if (!$su->authorized)
		{
			CURLHandler::Redirect("/authorizationrequired?target=".$_SERVER['REQUEST_URI']);
		}
		$type = GP("type","sale");
		$tableName = "tbl__baraholka_sell";
		if ($type=="buy")
		{
			$tableName = "tbl__baraholka_search";
		}
		$table = new CNativeDataTable($tableName);
		$id = GP("id");

		$junkRow =null;
		$error = "";
		$junkRow = $table->CreateNewRow(true);

		$cityList = new CSelect();
		$cityList->dataSource  = SQLProvider::ExecuteQuery("select tbl_obj_id, title from  tbl__city ");
		$cityList->valueName = "tbl_obj_id";
		$cityList->titleName = "title";
		$cityList->size=1;
		$cityList->name = "properties[city]";
		$cityList->class = "property_select";

		$sectionList = new CSelect();
		$sectionList->dataSource  = SQLProvider::ExecuteQuery("select * from `tbl__baraholka_section` ");
		$sectionList->valueName = "tbl_obj_id";
		$sectionList->titleName = "title";
		$sectionList->size=1;
		$sectionList->name = "properties[section]";
		$sectionList->class = "property_select";

		$images = array();
		
		if (!$su->authorized)
		{
			$error = $this->GetMessage("noright");
		}
		if ($id>0 && IsNullOrEmpty($error))
		{
			$_Row = $table->SelectUnique(new CEqFilter(&$table->fields["tbl_obj_id"],$id));
			if (!is_null($_Row))
			{
				if ($su->id==$_Row->owner_id)
				{
					$junkRow = $_Row;
					$cityList->selectedValue = $junkRow->city;
					$sectionList->selectedValue = $junkRow->section;
					if ($type=="sale")
					{
						$images = SQLProvider::ExecuteQuery( "select * from `vw__baraholka_photo` where junk_id =$junkRow->tbl_obj_id limit 3 ");
					}
				}
				else
				{
					$error = $this->GetMessage("noright");
				}
			}
			else
			{
				$id = null;
			}
		}
		$addPars = array();
		if (IsNullOrEmpty($error))
		{
			if ($this->IsPostBack)
			{
				$props = GP("properties");
				$vName = "saleValidator";
				if ($type=="buy")
				{
					$vName = "buyValidator";
				}
				$junkValidator = $this->GetControl($vName);
				$errorsData = $junkValidator->Validate(&$props);
				$junkRow->FromHashMap($props);
				$cityList->selectedValue = GP(array("properties","city"));
				if (($type=="sale")&&(sizeof($errorsData)==0)&&
				((IsNullOrEmpty($junkRow->foto))||(GP("foto_clean")=="clean"))
				&&($_FILES["properties"]["error"]["foto"]!=0))
				{
					$errorsData[0] = "Не загружен логотип.";
				}
				if (sizeof($errorsData)>0)
				{
					$error = $errorsData[0];
				}
				else
				{
					$junkRow->date=date("d.m.Y");
					if (is_null($id))
					{
						$junkRow->active=1;
						$junkRow->owner_id = $su->id;
						$table->InsertObject(&$junkRow);
					}
					if ($type=="sale")
					{
						if (GP("foto_clean")=="clean")
						{
							$this->CleanImage($junkRow->foto);
						}
						$_images = $_FILES["properties"];
						if (is_array($_images))
						{
							$sim = $this->CreateImage($junkRow->tbl_obj_id,$_images,"foto",IMAGE_FOTO_PREFIX,IMAGE_LOGO_SIZE_LIMIT);
							if (!is_null($sim))
							{
								$junkRow->foto = $sim;
							}
						}
						$delphotos = GP('delete_photos',array());
						if (sizeof($delphotos)>0)
						{
							$dstr = CStringFormatter::FromArray($delphotos);
							SQLProvider::ExecuteNonReturnQuery("delete from tbl__photo where tbl_obj_id in ($dstr)");
							SQLProvider::ExecuteNonReturnQuery("delete from tbl__photo2baraholka where child_id in ($dstr)");
							$images = SQLProvider::ExecuteQuery( "select * from `vw__baraholka_photo` where junk_id =$junkRow->tbl_obj_id limit 3 ");
						}
					}
					$table->UpdateObject(&$junkRow);
					if (is_null($id))
					{
						/*$rewriteParams = CURLHandler::$rewriteParams;
						$rewriteParams["id"] = $junkRow->tbl_obj_id;
						$url = CURLHandler::$currentPath.CURLHandler::BuildRewriteParams($rewriteParams);
						CURLHandler::Redirect($url);*/
						if ($type=="sale")
						{
							$type="sale_success";
						}
						if ($type=="buy")
						{
							$type="buy_success";
						}
					}
				}
			}
		}
		/*if ($type=="sale")
		{
			$logos = preg_split("/\//",$junkRow->foto);
			$ls = sizeof($logos);
			$junkRow->foto = GetFilename($junkRow->foto);
			$imagesList = $this->GetControl("imagesList");
			$imagesList->dataSource = $images;
			$addPars["imagesList"]=$imagesList->RenderHTML();
			$addPars["images_visible"]=sizeof($images)>2?"hidden":"visible";
		}*/
		$addPars['cityList'] = $cityList->RenderHTML();
		//$addPars['sectionList'] = $sectionList->RenderHTML();
		$errors = $this->GetControl("errors");
		$errors->dataSource = array("message"=>$error);
		$junksale = $this->GetControl("junksale");
		$junksale->key = $type;
		$junksale->dataSource = array_merge($junkRow->GetData(),$addPars);
	}
}
?>
