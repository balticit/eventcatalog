<?php
class cms_artists_edit_php extends CCMSPageCodeHandler
{
	public function cms_artists_edit_php()
	{
		$this->CCMSPageCodeHandler();
	}

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

	public function PreRender()
	{
		$userId = GP("userid",0);
		$account = $this->GetControl("account");
		$account->key = "artist";
		$errors = $this->GetControl("errors");
		$errors->dataSource = array("message"=>"");
		$junksaleSellList = "";
		$junksaleBuyList = "";
		$personalCVList = "";
		$personalVacancyList = "";

		$table = new CNativeDataTable("tbl__artist_doc");
		$groupList = new CSelect();
		$groupList->dataSource = SQLProvider::ExecuteQuery("select * from  `vw__artist_group_hierarcy` where parent_id=0");
		$groupList->titleName = "title";
		$groupList->valueName = "child_id";
		$groupList->id = "properties_group";
		$groupList->name = "properties[group]";
		$groupList->class = "property_select";
		$groupList->size=1;
		$groupList->htmlEvents["onchange"]="javascript:SetSubgroup();";
		$groups = SQLProvider::ExecuteQueryReverse("select child_id from  `vw__artist_group_hierarcy` where parent_id=0");
		$groupsJS = CStringFormatter::FromArray($groups["child_id"]);
		$subgroupLists = array();
		foreach ($groups["child_id"] as $group)
		{
			$subgroupList = new CSelect();
			$subgroupList->dataSource = SQLProvider::ExecuteQuery("select * from  `vw__artist_group_hierarcy` where parent_id=$group");
			$subgroupList->titleName = "title";
			$subgroupList->valueName = "child_id";
			$subgroupList->class = "property_select";
			$subgroupList->id = "properties_subgroup$group";
			$subgroupList->size = 1;
			array_push($subgroupLists,$subgroupList);
		}
		$stylesList = new CSelect();
		$stylesList->dataSource=SQLProvider::ExecuteQuery("select * from  tbl__styles");
		$stylesList->titleName = "title";
		$stylesList->valueName = "tbl_obj_id";
		$stylesList->multiple = true;
		$stylesList->size = 10;
		$stylesList->class = "property_select";
		$stylesList->name = "properties[style][]";
		$stylesList->style["height"] = "80px";
		$countriesList = new CSelect();
		$countriesList->dataSource = SQLProvider::ExecuteQuery("select * from  tbl__countries");
		$countriesList->class = "property_select";
		$countriesList->name = "properties[country]";
		$countriesList->valueName = "tbl_obj_id";
		$countriesList->titleName = "title";
		$countriesList->size = 1;
		$mp3Table = new CNativeDataTable("tbl__mp3");
		$mp3 = $mp3Table->CreateNewRow(true);
		$images = array();

		$userData = $table->SelectUnique(new CEqFilter($table->fields["tbl_obj_id"],$userId),false);

		if (is_null($userData))
		{
			$account->key = "notfound";
		}
		else
		{
			$groupList->selectedValue = $userData->group;
			$skeys = array_keys($subgroupLists);
			foreach ($skeys as $skey)
			{
				$subgroupLists[$skey]->selectedValue =  $userData->subgroup;
			}
			$styles  = SQLProvider::ExecuteQueryReverse("select style_id from tbl__artist2style where artist_id=$userData->tbl_obj_id");
			if (isset($styles['style_id']))
			{
				$stylesList->selectedValue = $styles['style_id'];
			}
			$countriesList->selectedValue = $userData->country;
			$mp3Data = SQLProvider::ExecuteQuery("select m.tbl_obj_id,m.title,m.file from tbl__mp3 m inner join tbl__artist2mp3 a2m on m.tbl_obj_id=a2m.child_id where a2m.parent_id=$userData->tbl_obj_id");
			if (sizeof($mp3Data))
			{
				$mp3->FromHashMap($mp3Data[0]);
			}
			$images = SQLProvider::ExecuteQuery("SELECT
								  p.*
								FROM 
								  `tbl__artist2photos`  ap
								  inner join `tbl__photo` p 
								  on ap.child_id = p.tbl_obj_id 
								  where parent_id=$userData->tbl_obj_id limit 3");
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

			if ($this->IsPostBack)
			{
				$props = GP("properties");
				$newMP3 = GP("mp3",array());
				$updateMP3 = false;
				$mp3->FromHashMap($newMP3);
				if (is_array($props))
				{
					$userValidator = $this->GetControl("artistValidator");

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
					$styles= GP(array("properties","style"),array());
					$stylesList->selectedValue = $styles;
					$setStyles = false;
					$groupList->selectedValue = $userData->group;
					$skeys = array_keys($subgroupLists);
					foreach ($skeys as $skey)
					{
						$subgroupLists[$skey]->selectedValue =  $userData->subgroup;
					}
					$countriesList->selectedValue = $userData->country;
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
						$flogo = $_FILES["properties"];
						if (is_array($flogo))
						{
							$logo = $this->CreateLogo($userData->login,$flogo);
							if (!is_null($logo))
							{
								$userData->logo = $logo;
							}
						}
						$table->UpdateObject(&$userData);
						$delphotos = GP('delete_photos',array());
						if (sizeof($delphotos)>0)
						{
							$dstr = CStringFormatter::FromArray($delphotos);
							SQLProvider::ExecuteNonReturnQuery("delete from tbl__photo where tbl_obj_id in ($dstr)");
							SQLProvider::ExecuteNonReturnQuery("delete from tbl__artist2photos where child_id in ($dstr)");
							$images = SQLProvider::ExecuteQuery( "SELECT
																			  p.*
																			FROM 
																			  `tbl__artist2photos`  ap
																			  inner join `tbl__photo` p 
																			  on ap.child_id = p.tbl_obj_id 
																			  where parent_id=$userData->tbl_obj_id limit 3");
						}
						$setStyles = true;
						$updateMP3 = true;
						if ($setStyles)
						{
							SQLProvider::ExecuteNonReturnQuery("delete from tbl__artist2style where artist_id=$userData->tbl_obj_id");
							foreach ($styles as $style)
							{
								SQLProvider::ExecuteNonReturnQuery("insert into tbl__artist2style values($userData->tbl_obj_id,$style)");
							}
						}
						if ($updateMP3)
						{
							if (GP("mp3_clean")=="clean")
							{
								$this->CleanLogo($mp3->file);
								SQLProvider::ExecuteNonReturnQuery("delete from tbl__mp3 where tbl_obj_id in (select child_id from tbl__artist2mp3 where parent_id=$userData->tbl_obj_id)");
								SQLProvider::ExecuteNonReturnQuery("delete from tbl__artist2mp3 where parent_id=$userData->tbl_obj_id");
								$mp3->tbl_obj_id=0;
							}
							$mp3File = $_FILES["mp3"];
							if (is_array($mp3File))
							{
								$mpf = $this->CreateLogo($userData->login,$mp3File,"mp3_file",IMAGE_MP3_PREFIX,IMAGE_LAGRE_SIZE_LIMIT);
								if (!is_null($mpf))
								{
									$mp3->file = $mpf;
								}
							}
							if ($mp3->tbl_obj_id>0)
							{
								$mp3Table->UpdateObject(&$mp3);
							}
							else
							{
								$mp3Table->InsertObject(&$mp3);
								SQLProvider::ExecuteNonReturnQuery("delete from tbl__artist2mp3 where parent_id=$userData->tbl_obj_id");
								SQLProvider::ExecuteNonReturnQuery("insert into tbl__artist2mp3 values($mp3->tbl_obj_id,$userData->tbl_obj_id)");
							}
						}
					}
				}
			}
			$logos = preg_split("/\//",$userData->logo);
			$ls = sizeof($logos);
			$mp3s = preg_split("/\//",$mp3->file);
			$ms = sizeof($mp3s);
			$_subgroupLists = "";
			foreach ($subgroupLists as $subgroupList) {
				$_subgroupLists.=$subgroupList->RenderHTML()."<br/>";
			}
			$imagesList = $this->GetControl("imagesList");
			$ikeys = array_keys($images);
			foreach ($ikeys as $ikey)
			{
				$images[$ikey]["ptype"] = "artist";
			}
			$imagesList->dataSource = $images;
			$udata = array("login_readonly"=>"readonly=\"true\"",
			"submit_text"=>$this->GetMessage("save"),
			"groupList"=>$groupList->RenderHTML(),
			"groupsJS"=>$groupsJS,
			"subgroupsList"=>$_subgroupLists,
			"stylesList"=>$stylesList->RenderHTML(),
			"countriesList"=>$countriesList->RenderHTML(),
			"mp3_title"=>$mp3->title,
			"mp3_file"=>($ms==0)?null:$mp3s[$ms-1],
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
