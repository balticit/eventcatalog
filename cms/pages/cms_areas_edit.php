<?php
class cms_areas_edit_php extends CCMSPageCodeHandler
{

	private function CreateLogo($login,$files,$key="logo",$prefix = IMAGE_LOGO_PREFIX,$sizeLimit =IMAGE_LOGO_SIZE_LIMIT,$overwrite = true)
	{
		if (isset($files["name"][$key]))
		{
			if (($files["error"][$key]==0)&&(is_file($files["tmp_name"][$key]))&&($files["size"][$key]<=$sizeLimit) )
			{
				$login = preg_replace("/[^A-Za-z0-9_\.]/","_",str_replace(IMAGE_PATH_SEMISECTOR,"_",$login));
				$fn =preg_replace("/[^A-Za-z0-9_\.]/","_",str_replace(IMAGE_PATH_SEMISECTOR,"_",$files["name"][$key]));
				$newpath = IMAGES_UPLOAD_DIR.$login.IMAGE_PATH_SEMISECTOR.$prefix.IMAGE_PATH_SEMISECTOR.$fn;
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

	private function CleanLogo(&$file)
	{
		if (is_file(ROOTDIR.$file))
		{
			unlink(ROOTDIR.$file);
		}
		$file = "";
	}

	public function cms_areas_edit_php()
	{
		$this->CCMSPageCodeHandler();
	}

	public function PreRender()
	{
		$userId = GP("userid",0);
		$account = $this->GetControl("account");
		$account->key = "area";
		$errors = $this->GetControl("errors");
		$errors->dataSource = array("message"=>"");
		$junksaleSellList = "";
		$junksaleBuyList = "";
		$personalCVList = "";
		$personalVacancyList = "";

		$ch = "checked=\"checked\"";
		$table = new CNativeDataTable("tbl__area_doc");
		$openAreasList = new CSelect();
		$openAreasList->id = "openAreasList";
		$openAreasList->dataSource = SQLProvider::ExecuteQuery("SELECT
							  `tbl_obj_id`,
							  `title`
							FROM
							  `tbl__area_subtypes` st
							  inner join  `tbl__area_types2subtypes` t2s 
							  on st.tbl_obj_id = t2s.child_id
							  where t2s.parent_id=19");
		$openAreasList->titleName = "title";
		$openAreasList->valueName = "tbl_obj_id";
		$openAreasList->class="property_select";
		$coveredAreasList = new CSelect();
		$coveredAreasList->id = "coveredAreasList";
		$coveredAreasList->dataSource = SQLProvider::ExecuteQuery("SELECT
							  `tbl_obj_id`,
							  `title`
							FROM
							  `tbl__area_subtypes` st
							  inner join  `tbl__area_types2subtypes` t2s 
							  on st.tbl_obj_id = t2s.child_id
							  where t2s.parent_id=20");
		$coveredAreasList->titleName = "title";
		$coveredAreasList->valueName = "tbl_obj_id";
		$coveredAreasList->class = "property_select";
		$locationsList = new CSelect();
		$locationsList->id = "locationsList";
		$locationsList->dataSource = SQLProvider::ExecuteQuery("select * from vw__city_location");
		$locationsList->titleName = "title";
		$locationsList->valueName = "tbl_obj_id";
		$locationsList->class = "property_select";
		$dance = false;
		$rent = false;
		$halls = array();
		$hallsJS = "";
		$images = array();


		$userData = $table->SelectUnique(new CEqFilter($table->fields["tbl_obj_id"],$userId),false);

		if (is_null($userData))
		{
			$account->key = "notfound";
		}
		else
		{
			$dance = $userData->dancing>0;
			$rent = $userData->rent>0;
			$locationsList->selectedValue = $userData->city_location;
			$coveredAreasList->selectedValue = $userData->area_subtype;
			$openAreasList->selectedValue = $userData->area_subtype;
			$halls = SQLProvider::ExecuteQuery("select * from tbl__area_halls where area_id = $userData->tbl_obj_id");
			$images = SQLProvider::ExecuteQuery("SELECT
								  p.*
								FROM 
								  `tbl__area_photos`  ap
								  inner join `tbl__photo` p 
								  on ap.child_id = p.tbl_obj_id 
								  where parent_id=$userData->tbl_obj_id limit 8");
			$jlist = $this->GetControl("junksaleSellList");
			$jlist->pid = $userId;
			$junksaleSellList = $jlist->RenderHTML();
			$jblist = $this->GetControl("junksaleBuyList");
			$jblist->pid = $userId;
			$junksaleBuyList = $jblist->RenderHTML();
			$jclist = $this->GetControl("personalCVList");
			$jclist->pid = $userId;
			$personalCVList = $jclist->RenderHTML();
			$jvlist = $this->GetControl("personalVacancyList");
			$jvlist->pid = $userId;
			$personalVacancyList = $jvlist->RenderHTML();


			$updateHalls = false;
			if ($this->IsPostBack)
			{
				$props = GP("properties");
				$halls = GP(array("properties","hall"),array());
				if (is_array($props))
				{
					$userValidator = $this->GetControl("areaValidator");

					unset($props["login"]);
					unset($userValidator->validationItems["login"]);
					if (IsNullOrEmpty($props["password"])&&IsNullOrEmpty($props["password_confirm"]))
					{
						unset($userValidator->validationItems["password"]);
						unset($userValidator->validationItems["password_confirm"]);
						unset($userValidator->validationItems["password_req"]);
						unset($props["password"]);
						unset($props["password_confirm"]);
					}

					$errorsData = $userValidator->Validate(&$props);
					$userData->FromHashMap($props);
					if (sizeof($errorsData)>0)
					{
						$errors->dataSource = array("message"=>$errorsData[0]);
					}
					else
					{

						if (isset($props["password"]))
						{
							if (!IsNullOrEmpty($props["password"]))
							{
								$userData->password = md5($props["password"]);
							}
						}

						$cleanlogo = GP("logo_clean");
						if ($cleanlogo=="clean")
						{
							$this->CleanLogo($userData->logo);
						}
						$cleanlogo = GP("location_scheme_clean");
						if ($cleanlogo=="clean")
						{
							$this->CleanLogo($userData->location_scheme);
						}
						$cleanlogo = GP("menu_clean");
						if ($cleanlogo=="clean")
						{
							$this->CleanLogo($userData->menu);
						}
						$flogo = $_FILES["properties"];
						if (is_array($flogo))
						{
							$logo = $this->CreateLogo($userData->login,$flogo);
							if (!is_null($logo))
							{
								$userData->logo = $logo;
							}
							$menu = $this->CreateLogo($userData->login,$flogo,"menu",IMAGE_MENU_PREFIX,IMAGE_SIZE_LIMIT);
							if (!is_null($menu))
							{
								$userData->menu = $menu;
							}
							$map = $this->CreateLogo($userData->login,$flogo,"location_scheme",IMAGE_MAP_PREFIX,IMAGE_SIZE_LIMIT);
							if (!is_null($map))
							{
								$userData->location_scheme = $map;
							}
						}
						$table->UpdateObject(&$userData);
						$updateHalls = true;
						$delphotos = GP('delete_photos',array());
						if (sizeof($delphotos)>0)
						{
							$dstr = CStringFormatter::FromArray($delphotos);
							SQLProvider::ExecuteNonReturnQuery("delete from tbl__photo where tbl_obj_id in ($dstr)");
							SQLProvider::ExecuteNonReturnQuery("delete from tbl__area_photos where child_id in ($dstr)");
							$images = SQLProvider::ExecuteQuery( "SELECT
																			  p.*
																			FROM 
																			  `tbl__area_photos`  ap
																			  inner join `tbl__photo` p 
																			  on ap.child_id = p.tbl_obj_id 
																			  where parent_id=$userData->tbl_obj_id limit 8");
						}
					}

					if ($updateHalls)
					{
						$hallTable = new CNativeDataTable("tbl__area_halls");
						$hallTable->Delete(new CEqFilter(&$hallTable->fields["area_id"],$userData->tbl_obj_id));
						if (sizeof($halls)>0)
						{
							foreach ($halls as $hall)
							{
								$hall["title"] = isset($hall["title"])?VType($hall["title"],"string",""):"";
								$hRow = $hallTable->CreateNewRow(true);
								$hRow->FromHashMap($hall);
								$hRow->area_id = $userData->tbl_obj_id;
								$hallTable->InsertObject(&$hRow,false);
							}
						}
					}
				}
			}
			if (sizeof($halls)>0)
			{
				$th = array();
				foreach ($halls as $hall)
				{
					$hall["title"] = isset($hall["title"])?VType($hall["title"],"string",""):"";
					$hall["max_places_banquet"] = isset($hall["max_places_banquet"])?VType($hall["max_places_banquet"],"int",0):0;
					$hall["max_places_official_buffet"] = isset($hall["max_places_official_buffet"])?VType($hall["max_places_official_buffet"],"int",0):0;
					$tr = CStringFormatter::Format('{"title":"{title}", "max_places_banquet":{max_places_banquet},"max_places_official_buffet":{max_places_official_buffet}}',$hall);
					array_push($th,$tr);
				}
				$hallsJS = CStringFormatter::FromArray($th);
			}
			$logos = preg_split("/\//",$userData->logo);
			$ls = sizeof($logos);
			$ikeys = array_keys($images);
			foreach ($ikeys as $ikey)
			{
				$images[$ikey]["ptype"] = "area";
			}
			$imagesList = $this->GetControl("imagesList");
			$imagesList->dataSource = $images;
			$udata = array("login_readonly"=>"readonly=\"true\"",
			"submit_text"=>$this->GetMessage("save"),
			"openAreasList"=>$openAreasList->RenderHTML(),
			"coveredAreasList"=>$coveredAreasList->RenderHTML(),
			"hasRent"=>$rent?$ch:"",
			"hasNoResnt"=>!$rent?$ch:"",
			"hasDance"=>$dance?$ch:"",
			"noDance"=>!$dance?$ch:"",
			"locationsList"=>$locationsList->RenderHTML(),
			"hallsJS" => $hallsJS,
			"imagesList"=>$imagesList->RenderHTML(),
			"images_visible"=>"hidden",
			"junksaleSellList"=>$junksaleSellList,
			"junksaleBuyList"=>$junksaleBuyList,
			"personalCVList"=>$personalCVList,
			"personalVacancyList"=>$personalVacancyList);
			$userData->logo = ($ls==0)?null:$logos[$ls-1];
			$account->dataSource = array_merge($udata, $userData->GetData());
		}
	}
}
?>
