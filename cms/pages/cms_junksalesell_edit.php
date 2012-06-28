<?php
class cms_junksalesell_edit_php extends CCMSPageCodeHandler
{
	public function cms_junksalesell_edit_php()
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

		$tableName = "tbl__baraholka_sell";
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

		$images = array();


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
				$vName = "saleValidator";

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

					$table->UpdateObject(&$junkRow);
				}
			}
			$images = SQLProvider::ExecuteQuery( "select * from `vw__baraholka_photo` where junk_id =$junkRow->tbl_obj_id limit 3 ");
			$logos = preg_split("/\//",$junkRow->foto);
			$ls = sizeof($logos);
			$junkRow->foto = ($ls==0)?null:$logos[$ls-1];
			$imagesList = $this->GetControl("imagesList");
			$imagesList->dataSource = $images;
			$addPars["imagesList"]=$imagesList->RenderHTML();
			$addPars["images_visible"]="hidden";

			$addPars['cityList'] = $cityList->RenderHTML();
			$addPars['sectionList'] = $sectionList->RenderHTML();
			$errors = $this->GetControl("errors");
			$errors->dataSource = array("message"=>$error);

			$junksale->key = "sale";
			$junksale->dataSource = array_merge($junkRow->GetData(),$addPars);
		}
	}
}
?>
