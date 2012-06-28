<?php
class registration_personal_php extends CPageCodeHandler
{
	public function registration_personal_php()
	{
		$this->CPageCodeHandler();
	}
	
	public  function CreateImage($id,$files,$key,$prefix = IMAGE_BASE_PREFIX,$sizeLimit =IMAGE_SIZE_LIMIT,$overwrite = true)
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
				if  (move_uploaded_file($files["tmp_name"][$key],ROOTDIR.$newpath))
				{
					if (GP("type")=="cv")
					{
						$res = new ResizeImage(ROOTDIR.$newpath);
						if (($res->imgWidth>MAX_CV_IMAGE_WIDTH)||($res->imgHeight>MAX_CV_IMAGE_HEIGHT))
							$res->resize(MAX_CV_IMAGE_WIDTH,MAX_CV_IMAGE_HEIGHT,ROOTDIR.$newpath,true);
					}
					return  $newfile;
				}
				return null;
			}
		}
	}

	public function CleanImage(&$file)
	{
		if (is_file(ROOTDIR.IMAGES_UPLOAD_DIR.$file))
		{
			unlink(ROOTDIR.IMAGES_UPLOAD_DIR.$file);
		}
		//$file = "";
	}


	public function PreRender()
	{
		$su = new CSessionUser("user");
		CAuthorizer::RestoreUserFromSession(&$su);
		if (!$su->authorized)
		{
			CURLHandler::Redirect("/authorizationrequired?target=".$_SERVER['REQUEST_URI']);
		}
		$type = GP("type","cv");
		$personal_type_list = new CSelect();
		$personal_type_list->dataSource = SQLProvider::ExecuteQuery("select * from vw__personal_types_ex order by if(tbl_obj_id>0,0,-1) desc,title");
		$personal_type_list->valueName = "tbl_obj_id";
		$personal_type_list->titleName = "title";
		$personal_type_list->name = "properties[personal_type]";
		$personal_type_list->id = "properties_personal_type";
		$personal_type_list->htmlEvents["onchange"]= "javascript:ShowOther();";
		$citySelect = new CSelect();
		$citySelect->dataSource = SQLProvider::ExecuteQuery("select * from `tbl__city` where `all`=1");
		$citySelect->titleName="title";
		$citySelect->valueName="tbl_obj_id";
		$citySelect->name="properties[city]";
		$citySelect->class = "property_select";
		$tableName = "tbl__personal_organizator_cv_doc";
		if ($type=="vacancy")
		{
			$tableName = "tbl__personal_organizator_vacancy_doc";
		}
		$table = new CNativeDataTable($tableName);
		$id = GP("id");
		$persRow =null;
		$error = "";
		$persRow = $table->CreateNewRow(true);
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
					$persRow = $_Row;
					$personal_type_list->selectedValue = $persRow->personal_type;
					if ($type=="vacancy")
					{
						$citySelect->selectedValue = $persRow->city;
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

		if (IsNullOrEmpty($error))
		{
			if ($this->IsPostBack)
			{
				$props = GP("properties");
				$vName = "cvValidator";
				if ($type=="vacancy")
				{
					$vName = "vacancyValidator";
				}
				$persValidator = $this->GetControl($vName);
				$errorsData = $persValidator->Validate(&$props);
				$persRow->FromHashMap($props);
				$personal_type_list->selectedValue = $persRow->personal_type;
				$citySelect->selectedValue = $persRow->city;
				if (($props["personal_type"]==-1)&&(IsNullOrEmpty($props["other_personal_type"])))
				{
					$errorsData[0] = "Не введено название должности";
				}
				if (($type=="cv")&&(sizeof($errorsData)==0)&&
				((IsNullOrEmpty($junkRow->logo))||(GP("logo_clean")=="clean"))
				&&($_FILES["properties"]["error"]["logo"]!=0))
				{
					$errorsData[0] = "Не загружен логотип.";
				}
				if (sizeof($errorsData)>0)
				{
					$error = $errorsData[0];
				}
				else
				{
					$persRow->owner_id=$su->id;
					if (is_null($id))
					{
						$table->InsertObject(&$persRow);
					}
					$persRow->registration_date = date("Y-m-d");
					if (GP("logo_clean")=="clean")
					{
						$this->CleanImage($persRow->logo);
					}
					$images = $_FILES["properties"];
					if (is_array($images))
					{
						$lim = $this->CreateImage($persRow->tbl_obj_id,$images,"logo",IMAGE_BASE_PREFIX,IMAGE_SIZE_LIMIT);
						if (!is_null($lim))
						{
							$persRow->logo = $lim;
						}
					}
					$table->UpdateObject(&$persRow);
					if (is_null($id))
					{
						if ($type=="vacancy")
						{
							$type="vacancy_success";
						}
						if ($type=="cv")
						{
							$type="cv_success";
						}
					}
				}
			}
		}
		$errors = $this->GetControl("errors");
		$errors->dataSource = array("message"=>$error);
		$personal = $this->GetControl("personal");
		$personal->key = $type;
		$personal->dataSource = array_merge($persRow->GetData(),
		array("personal_type_select"=>$personal_type_list->RenderHTML(),
		"citySelect"=>$citySelect->RenderHTML()));
	}
}
?>
