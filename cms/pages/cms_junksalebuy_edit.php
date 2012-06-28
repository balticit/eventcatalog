<?php
class cms_junksalebuy_edit_php extends CCMSPageCodeHandler
{
	public function cms_junksalebuy_edit_php()
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

		$tableName = "tbl__baraholka_search";
		$table = new CNativeDataTable($tableName);
		$id = GP("id",0);

		$error = "";

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


		$junkRow = $table->SelectUnique(new CEqFilter(&$table->fields["tbl_obj_id"],$id));
		$junksale = $this->GetControl("account");
		if (is_null($junkRow))
		{
			$junksale->key = "notfound";
		}
		else
		{
			$addPars = array();

			if ($this->IsPostBack)
			{
				$props = GP("properties");
				$vName = "buyValidator";

				$junkValidator = $this->GetControl($vName);
				$errorsData = $junkValidator->Validate(&$props);
				$junkRow->FromHashMap($props);
				$cityList->selectedValue = GP(array("properties","city"));
				$sectionList->selectedValue = GP(array("properties","section"));
				if (sizeof($errorsData)>0)
				{
					$error = $errorsData[0];
				}
				else
				{
					$junkRow->date=date("d.m.Y");
					$table->UpdateObject(&$junkRow);
				}
			}

			$addPars['cityList'] = $cityList->RenderHTML();
			$addPars['sectionList'] = $sectionList->RenderHTML();
			$errors = $this->GetControl("errors");
			$errors->dataSource = array("message"=>$error);

			$junksale->key = "buy";
			$junksale->dataSource = array_merge($junkRow->GetData(),$addPars);
		}
	}
}
?>
