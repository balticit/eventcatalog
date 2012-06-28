<?php
class r_cabinet_php extends CPageCodeHandler
{

	public $mailpath;

	public $mailtitle;

	public function r_cabinet_php()
	{
		$this->CPageCodeHandler();
	}

	private function CreateLogo($login,$files,$key="logo_file",$prefix = IMAGE_LOGO_PREFIX)
  {
    if (isset($files["name"][$key]) &&
        $files["error"][$key] === 0 &&
        is_file($files["tmp_name"][$key])){
      $login = PrepareImagePathPart($login);
      $ext = ".".pathinfo($files["name"][$key], PATHINFO_EXTENSION);
      $newfile = $login.IMAGE_PATH_SEMISECTOR.$prefix.$ext;
      $newpath = IMAGES_UPLOAD_DIR.$newfile;
      if (is_file(ROOTDIR.$newpath))
        unlink(ROOTDIR.$newpath);
      $res = new ResizeImage($files["tmp_name"][$key]);
      $res->resize(120,80,ROOTDIR.$newpath,true);
      unset($res);
      return $newfile;
    }
    return null;
  }

	private function CleanLogo(&$file)
	{
		if (is_file(ROOTDIR.$file))
		{
			unlink(ROOTDIR.$file);
		}
		$file = "";
	}

  private function CheckCity($city)
    {
      $cities = SQLProvider::ExecuteQuery("select tbl_obj_id from `tbl__city`
            where `active`=1 and LOWER(`title`) = LOWER('$city')");
      if (sizeof($cities)>0)
        return $cities[0]['tbl_obj_id'];
      else
        return null;
    }
  private function CheckCountry($country)
    {
      $contries= SQLProvider::ExecuteQuery("select tbl_obj_id from `tbl__countries`
            where LOWER(`title`) = LOWER('$country')");
      if (sizeof($contries)>0)
        return $contries[0]['tbl_obj_id'];
      else
        return null;
    }

	private function UpdateHalls(&$userData)
	{
		$halls = GP(array("properties","hall"),array());
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
		$typeTable = new CNativeDataTable("tbl__area2type");
		$typeTable->Delete(new CEqFilter(&$typeTable->fields["area_id"],$userData->tbl_obj_id));
		$areaTypes = GP("area_types",array());
		foreach ($areaTypes as $areaType)
		{
			$Row = $typeTable->CreateNewRow(true);
			$Row->type_id = $areaType;
			$Row->area_id = $userData->tbl_obj_id;
			$typeTable->InsertObject(&$Row,false);

		}
		$subtypeTable = new CNativeDataTable("tbl__area2subtype");
		$subtypeTable->Delete(new CEqFilter(&$subtypeTable->fields["area_id"],$userData->tbl_obj_id));
		$areaSubtypes = GP("area_subtypes",array());
		foreach ($areaSubtypes as $areaSubtype)
		{
			$Row = $subtypeTable->CreateNewRow(true);
			$Row->subtype_id = $areaSubtype;
			$Row->area_id = $userData->tbl_obj_id;
			$subtypeTable->InsertObject(&$Row,false);
		}
		$atypes = SQLProvider::ExecuteQueryReverse("select distinct type_id from tbl__area2type where area_id=$userData->tbl_obj_id");
		$areaTypesList->selectedValue = (isset($atypes["type_id"]))?$atypes["type_id"]:array();
		$asubtypes = SQLProvider::ExecuteQueryReverse("select distinct subtype_id from tbl__area2subtype where area_id=$userData->tbl_obj_id");
		$areaSubTypesList->selectedValue = (isset($asubtypes["subtype_id"]))?$asubtypes["subtype_id"]:array();
	}

	private function SetArtistStyles(&$userData)
	{
		$styles= GP(array("properties","style"),array());
    SQLProvider::ExecuteNonReturnQuery("delete from tbl__artist2style where artist_id=$userData->tbl_obj_id");
    foreach ($styles as $style)
    {
        SQLProvider::ExecuteNonReturnQuery("insert into tbl__artist2style values($userData->tbl_obj_id,$style)");
    }
    $subgroups = GP(array("properties","selected_group"),array());
    SQLProvider::ExecuteNonReturnQuery("delete from tbl__artist2subgroup where artist_id=$userData->tbl_obj_id");
    foreach ($subgroups as $subgroup)
    {
        SQLProvider::ExecuteNonReturnQuery("insert into tbl__artist2subgroup values($userData->tbl_obj_id,$subgroup)");
    }

	}

  private function renderProfile($user)
  {
    $account = $this->GetControl("account");
		$account->key = $user->type;
		$errors = $this->GetControl("errors");
		$errors->dataSource = array("message"=>"");

		switch ($user->type) {
			case "contractor":
			{
				$groups_items = SQLProvider::ExecuteQuery("select * from tbl__activity_type where parent_id = 0 or parent_id is null
                                                             order by priority desc");
        $groups_list = "";
        $subgroups_list = "";
        $sel_groups = array();
        $logo_link = "";

				$table = new CNativeDataTable("tbl__contractor_doc");
				$userData = null;
				$images = array();
				if ($user->authorized)
				{
					$filter = new CAndFilter(new CEqFilter($table->fields["tbl_obj_id"],$user->id),
					new CAndFilter(new CEqFilter($table->fields["active"],1),
					new CEqFilter($table->fields["registration_confirmed"],1)));
					$userData = $table->SelectUnique($filter,false);
				}

				if ($this->IsPostBack)
				{
					$props = GP("properties");
					if (is_array($props))
					{
						$userValidator = $this->GetControl("contractorValidator");
						if ($user->authorized)
						{
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
						}
            $props['city'] = trim($props['city']);
						$errorsData = $userValidator->Validate(&$props);
						$userData->FromHashMap($props);
						if (sizeof($errorsData)>0)
						{
							$errors->dataSource = array("message"=>$errorsData[0]);
						}
						else
						{
							$city_id = $this->CheckCity($props['city']);
              if ($city_id) {
                $props['city'] = $city_id;
                $props['other_city'] = '';
              }
              else {
                $props['other_city'] = $props['city'];
                $props['city'] = 0;
              }
              $userData->city = $props['city'];
              $userData->other_city = $props['other_city'];
              $userData->short_description = $userData->description;

              if (isset($props["password"])) {
                if (!IsNullOrEmpty($props["password"])) {
                  $userData->password = md5($props["password"]);
                }
              }

              $cleanlogo = GP("logo_clean");
              if ($cleanlogo=="clean") {
                $this->CleanLogo($userData->logo_image);
              }
              $flogo = $_FILES["properties"];
              if (is_array($flogo)) {
                $logo = $this->CreateLogo($userData->login,$flogo,"logo");
                if (!is_null($logo)) {
                  $userData->logo_image = $logo;
                }
              }
              $userData->edit_date = time();
              $table->UpdateObject(&$userData);

              $delphotos = GP('delete_photos',array());
              if (sizeof($delphotos)>0)
              {
                $dstr = CStringFormatter::FromArray($delphotos);
                SQLProvider::ExecuteNonReturnQuery("delete from tbl__photo where tbl_obj_id in ($dstr)");
                SQLProvider::ExecuteNonReturnQuery("delete from tbl__contractor_photos where child_id in ($dstr)");
              }

              $user->name = $userData->title;
              $user->Save();
						}
					}
				}


        if (!is_null($userData))	{
          $images = SQLProvider::ExecuteQuery("SELECT
                                  p.*
                                FROM
                                  `tbl__contractor_photos`  ap
                                  inner join `tbl__photo` p
                                  on ap.child_id = p.tbl_obj_id
                                  where parent_id=$userData->tbl_obj_id limit 8");

          if (!IsNullOrEmpty($userData->city)) {
             $userData->other_city = SQLProvider::ExecuteScalar(
               "select title from `tbl__city` where tbl_obj_id = ".$userData->city);
          }

          $ss= SQLProvider::ExecuteQuery(
            "select distinct ca.kind_of_activity from tbl__contractor2activity ca
             join tbl__activity_type at on at.tbl_obj_id = ca.kind_of_activity and at.parent_id > 0
             where ca.tbl_obj_id=$userData->tbl_obj_id");
          $sel_groups = array();
          foreach ($ss as $s)
            array_push($sel_groups, $s['kind_of_activity']);
				}

				$logos = preg_split("/\//",$userData->logo);
				$ls = sizeof($logos);

				$ikeys = array_keys($images);
        foreach ($ikeys as $ikey)
        {
            $images[$ikey]["ptype"] = "contractor";
        }
        $imagesList = $this->GetControl("imagesList");
        $imagesList->dataSource = $images;

        $selected_groups = "";
        if (isset($sel_groups) and is_array($sel_groups)) {
          foreach($sel_groups as $sg) {
            $sg_title = SQLProvider::ExecuteScalar("select title from tbl__activity_type where tbl_obj_id = $sg");
            $selected_groups .= '<div id="selected_id'.$sg.
            '"><input type="hidden" name="properties[selected_group]['.$sg.
          ']" value="'.$sg.'">'.$sg_title.
          ' <a href="" class="reg_del_group" onclick="javascript: SelectedGroupDel('.$sg.
          '); return false;">удалить</a></div>';
          }


        }
        foreach ($groups_items as $group) {
          $groups_list .= '<option value="'.$group['tbl_obj_id'].'">'.$group['title'].'</option>';
          $subgroups_items = SQLProvider::ExecuteQuery("select * from tbl__activity_type where parent_id = ".$group['tbl_obj_id']."
                                                     order by priority desc");
          $subgroups_list .= '<div id="subgroup_id'.$group['tbl_obj_id'].'" style="display: none;">';
          foreach ($subgroups_items as $sgroup) {
            $chk = "";
            if (isset($sel_groups) and is_array($sel_groups) and
                !(array_search($sgroup['tbl_obj_id'],$sel_groups)===false)
                ) {
              $chk = "checked=\"checked\"";
            }

            $subgroups_list .= '<input id="checkbox_id'.$sgroup['tbl_obj_id'].'" type="checkbox" value="'.$sgroup['tbl_obj_id'].'" onclick="SelectSubGroup(this,\''.$sgroup['title'].'\')" '.$chk.'>'.$sgroup['title'].'<br>';
                  }
                  $subgroups_list .= "</div>";
                }


        if (!IsNullOrEmpty($userData->logo_image)) {
          $logo_file = GetFilename($userData->logo_image);
          $logo_link = '<a href="/upload/'.$logo_file.'" target="_blank">'.$logo_file.'</a>';
        }

        $udata = array(
        "submit_text"=>$this->GetMessage($user->authorized?"save":"reg"),
        "imagesList"=>$imagesList->RenderHTML(),
        "groups_list"=>$groups_list,
        "subgroups_list"=>$subgroups_list,
        "selected_groups"=>$selected_groups,
        "st_visible"=>sizeof($sel_groups)>0?"":"display:none;",
        "logo_link"=>$logo_link);

				$userData->logo_image = GetFilename($userData->logo_image);
				$account->dataSource = array_merge($userData->GetData(),$udata);
			}
			break;
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
			case "area":
			{
				$ch = "checked=\"checked\"";
				$newUser = false;
				$table = new CNativeDataTable("tbl__area_doc");
				$userData = null;
				$citySelect = new CSelect();
				$citySelect->dataSource = SQLProvider::ExecuteQuery("select * from `tbl__city` ");
				array_push($citySelect->dataSource,array("tbl_obj_id"=>-1,"title"=>"другой город..."));
				$citySelect->titleName="title";
				$citySelect->valueName="tbl_obj_id";
				$citySelect->name="properties[city]";
				$citySelect->class = "property_select";
				$areaTypesList = new CSelect();
				$areaTypesList->dataSource = SQLProvider::ExecuteQuery("select * from tbl__area_types");
				$areaTypesList->titleName = "title";
				$areaTypesList->valueName = "tbl_obj_id";
				$areaTypesList->multiple = true;
				//$areaTypesList->disabled = disabled;
				$areaTypesList->class = "property_select_multiple";
				//$areaTypesList->class = "property_select";
				$areaTypesList->name = "area_types[]";
				$areaSubTypesList = new CSelect();
				$areaSubTypesList->dataSource = SQLProvider::ExecuteQuery("select * from tbl__area_subtypes");
				$areaSubTypesList->titleName = "title";
				$areaSubTypesList->valueName = "tbl_obj_id";
				$areaSubTypesList->multiple = true;
				//$areaSubTypesList->disabled = disabled;
				$areaSubTypesList->class = "property_select_multiple";
				$regData = "Регистрация не подтверждена";
				//$areaSubTypesList->class = "property_select";
				$areaSubTypesList->name = "area_subtypes[]";
				$dance = false;
				$rent = false;
				$halls = array();
				$hallsJS = "";
				$images = array();
				if ($user->authorized)
				{
					$filter = new CAndFilter(new CEqFilter($table->fields["tbl_obj_id"],$user->id),
					new CAndFilter(new CEqFilter($table->fields["active"],1),
					new CEqFilter($table->fields["registration_confirmed"],1)));
					//CDebugger::DebugArray($filter);
					$userData = $table->SelectUnique($filter,false);
				}
				if (is_null($userData))
				{
					$userData=$table->CreateNewRow(true);
					$newUser = true;
				}
				else
				{
					$dance = $userData->dancing>0;
					$hasPark = $userData->parking>0;
					$rent = $userData->rent>0;
					$hasSE = $userData->service_entrance>0;
					$hasG = $userData->wardrobe>0;
					$hasCar = $userData->car_into>0;
					$hasLight = $userData->light>0;
					$hasSound = $userData->sound>0;
					$hasPanels = $userData->panels>0;
					$hasProj = $userData->projector>0;
					$hasCat = $userData->invite_catering>0;
					$hasSc = $userData->stage>0;
					$hasM = $userData->makeup_rooms>0;
					$atypes = SQLProvider::ExecuteQueryReverse("select distinct type_id from tbl__area2type where area_id=$userData->tbl_obj_id");
					$areaTypesList->selectedValue = (isset($atypes["type_id"]))?$atypes["type_id"]:array();
					$asubtypes = SQLProvider::ExecuteQueryReverse("select distinct subtype_id from tbl__area2subtype where area_id=$userData->tbl_obj_id");
					$areaSubTypesList->selectedValue = (isset($asubtypes["subtype_id"]))?$asubtypes["subtype_id"]:array();
					$citySelect->selectedValue = $userData->city;
					$halls = SQLProvider::ExecuteQuery("select * from tbl__area_halls where area_id = $userData->tbl_obj_id");
					$images = SQLProvider::ExecuteQuery("SELECT
								  p.*
								FROM
								  `tbl__area_photos`  ap
								  inner join `tbl__photo` p
								  on ap.child_id = p.tbl_obj_id
								  where parent_id=$userData->tbl_obj_id limit 8");
					$jlist = $this->GetControl("junksaleSellList");
					$jlist->pid = $user->id;
					$junksaleSellList = $jlist->RenderHTML();
					$jblist = $this->GetControl("junksaleBuyList");
					$jblist->pid = $user->id;
					$junksaleBuyList = $jblist->RenderHTML();
					$jclist = $this->GetControl("personalCVList");
					$jclist->pid = $user->id;
					$personalCVList = $jclist->RenderHTML();
					$jvlist = $this->GetControl("personalVacancyList");
					$jvlist->pid = $user->id;
					$personalVacancyList = $jvlist->RenderHTML();
					$regData = $userData->registration_date;
				}

				$updateHalls = false;
				if ($this->IsPostBack)
				{
					$props = GP("properties");
					CDebugger::DebugArray($props);

					if (is_array($props))
					{
						$userValidator = $this->GetControl("areaValidator");
						if ($user->authorized)
						{
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
						}
						$errorsData = $userValidator->Validate(&$props);
						if ($props["other_city"]!="") {
							mysql_query("insert into tbl__area_city set title='".addslashes($props["other_city"])."', active=1");
							$props["city"] = mysql_insert_id();
							$props["other_city"] = "";
						}
						$userData->FromHashMap($props);
						if (sizeof($errorsData)>0)
						{
							$errors->dataSource = array("message"=>$errorsData[0]);
							CDebugger::DebugArray($errors);
						}
						else
						{
							if ($newUser)
							{
								$login = $table->SelectUnique(new CEqFilter(&$table->fields["login"],$props["login"]),false);
								$r = mysql_query("select * from vw__all_users");
								if (mysql_num_rows($r)==0) {
									$errors->dataSource = array("message"=>"такой логин уже существует");
								}
								elseif (is_null($login))
								{

									$completeData = array();
									$completeData["login"] = $userData->login;
									$completeData["password"] = $userData->password;

									$userData->active=0;
									$userData->registration_confirmed = 0;
									$userData->registration_confirm_code = md5(rand(0,getrandmax()));
									$userData->password = md5($userData->password);
									$flogo = $_FILES["properties"];
									if (is_array($flogo))
									{
										$logo = $this->CreateLogo($userData->login,$flogo,"logo");
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
										$video = $this->CreateLogo($userData->login,$flogo,"video",IMAGE_VIDEO_PREFIX,VIDEO_SIZE_LIMIT);
										if (!is_null($video))
										{
											$userData->video = $video;
										}
									}
									$userData->edit_date = time();
									$table->InsertObject(&$userData);

									$completeData["id"] = $userData->tbl_obj_id;

									$updateHalls = true;
									$this->UpdateHalls($userData);

									$photos = $_FILES["photo_file"];
									$ptitles = GP("photo_title",array());
									if (is_array($ptitles))
									{

                    $midiWidth = IMAGE_AREA_MEDIUM_WIDTH;
										$midiHeight = IMAGE_AREA_MEDIUM_HEIGHT;
										$thumbWidth = IMAGE_AREA_THUMB_WIDTH;
										$thumbHeight = IMAGE_AREA_THUMB_HEIGHT;
										$uploader = new registration_image_php();
										$uploadtable = new CNativeDataTable("tbl__photo");
										for ($j=0;$j<8;$j++) {
											$ptj = GP(array("photo_title",$j));
											if (!IsNullOrEmpty($ptj))
											{
												$imgRow = $uploadtable->CreateNewRow(true);
												$imgRow->title = $ptitles[$j];
												$uploadtable->InsertObject(&$imgRow);
												$lim = $uploader->CreateImage($imgRow->tbl_obj_id,$photos,$j,IMAGE_BASE_PREFIX,IMAGE_LAGRE_SIZE_LIMIT);
												if (!is_null($lim))
												{
													$imgRow->l_image = $lim;
													$mim = preg_replace(IMAGE_PATH_SEMISECTOR.IMAGE_BASE_PREFIX.IMAGE_PATH_SEMISECTOR,IMAGE_PATH_SEMISECTOR.IMAGE_BASE_PREFIX."_medi".IMAGE_PATH_SEMISECTOR,$lim,1);
													$resImg = new ResizeImage(ROOTDIR.IMAGES_UPLOAD_DIR.$lim);
													if ( $resImg->resize($midiWidth,$midiHeight,ROOTDIR.IMAGES_UPLOAD_DIR.$mim,true))
													{
														$imgRow->m_image = $mim;
													}
													else
													{
														print $resImg->error();
													}
													unset($resImg);
													$sim = preg_replace(IMAGE_PATH_SEMISECTOR.IMAGE_BASE_PREFIX.IMAGE_PATH_SEMISECTOR,IMAGE_PATH_SEMISECTOR.IMAGE_BASE_PREFIX."_thumb".IMAGE_PATH_SEMISECTOR,$lim,1);
													$resImg = new ResizeImage(ROOTDIR.IMAGES_UPLOAD_DIR.$lim);
													if ( $resImg->resize($thumbWidth,$thumbHeight,ROOTDIR.IMAGES_UPLOAD_DIR.$sim,true))
													{
														$imgRow->s_image = $sim;
													}
													else
													{
														print $resImg->error();
													}
													unset($resImg);
													$uploadtable->UpdateObject(&$imgRow);
													SQLProvider::ExecuteNonReturnQuery("delete from tbl__area_photos where child_id=$imgRow->tbl_obj_id");
													SQLProvider::ExecuteNonReturnQuery("insert into tbl__area_photos values($imgRow->tbl_obj_id,$userData->tbl_obj_id)");
												}
											}
										}
									}
									$title = iconv($this->encoding,"utf-8",$this->mailtitle);
									$mbody = CStringFormatter::Format(file_get_contents(RealFile($this->mailpath)),array("link"=>"http://".$_SERVER['HTTP_HOST']."/registration/confirm/code/".$userData->registration_confirm_code));
									SendHTMLMail($userData->email,$mbody,$title);
									CURLHandler::Redirect(CURLHandler::$currentPath."type/complete/data/".base64_encode(serialize($completeData)));
									CDebugger::DebugArray($userData);
								}
								else
								{
									$errors->dataSource = array("message"=>"такой логин уже существует");
								}
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
								$cleanlogo = GP("video_clean");
								if ($cleanlogo=="clean")
								{
									$this->CleanLogo($userData->video);
								}
								$flogo = $_FILES["properties"];
								CDebugger::DebugArray($flogo);
								if (is_array($flogo))
								{
									$logo = $this->CreateLogo($userData->login,$flogo,"logo");
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
									$video = $this->CreateLogo($userData->login,$flogo,"video",IMAGE_VIDEO_PREFIX,VIDEO_SIZE_LIMIT);
									if (!is_null($video))
									{
										$userData->video = $video;
									}
								}
								$userData->edit_date = time();
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
								$user->name = $userData->title;
								$user->Save();
							}

							if ($updateHalls)
							{
								$this->UpdateHalls($userData);
								$halls = SQLProvider::ExecuteQuery("select * from tbl__area_halls where area_id = $userData->tbl_obj_id");
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

				if ($newUser)
				{
					$imagesList->itemTemplate = file_get_contents(RealFile("pagecode/settings/registration_files/fileUploadItem.htm"));
					$images = array();
					for ($i=0;$i<8;$i++)
					{
						array_push($images,array("title_name"=>"photo_title[$i]","file_name"=>"photo_file[$i]"));
					}
				}
				$imagesList->dataSource = $images;
				$udata = array("login_readonly"=>$user->authorized?"readonly=\"true\"":"",
				"submit_text"=>$this->GetMessage($user->authorized?"save":"reg"),
				"areaTypesList"=>$areaTypesList->RenderHTML(),
				"areaSubTypesList"=>$areaSubTypesList->RenderHTML(),
				"city_list"=>$citySelect->RenderHTML(),
				"hasRent"=>$rent?$ch:"",
				"hasNoResnt"=>!$rent?$ch:"",
				"hasDance"=>$dance?$ch:"",
				"noDance"=>!$dance?$ch:"",
				"hasSE"=>$hasSE?$ch:"",
				"notSE"=>!$hasSE?$ch:"",
				"hasCat"=>$hasCat?$ch:"",
				"noCat"=>!$hasCat?$ch:"",
				"hasSc"=>$hasSc?$ch:"",
				"noSc"=>!$hasSc?$ch:"",
				"hasM"=>$hasM?$ch:"",
				"noM"=>!$hasM?$ch:"",
				"hasPark"=>$hasPark?$ch:"",
				"noPark"=>!$hasPark?$ch:"",
				"hasG"=>$hasG?$ch:"",
				"noG"=>!$hasG?$ch:"",
				"hasCar"=>$hasCar?$ch:"",
				"noCar"=>!$hasCar?$ch:"",
				"hasL"=>$hasLight?$ch:"",
				"noL"=>!$hasLight?$ch:"",
				"hasS"=>$hasSound?$ch:"",
				"noS"=>!$hasSound?$ch:"",
				"hasP"=>$hasPanels?$ch:"",
				"noP"=>!$hasPanels?$ch:"",
				"hasPr"=>$hasProj?$ch:"",
				"noPr"=>!$hasProj?$ch:"",
				"hallsJS" => $hallsJS,
				"imagesList"=>$imagesList->RenderHTML(),
				"images_visible"=>sizeof($images)>7?"hidden":"visible",
				"junksaleSellList"=>$junksaleSellList,
				"junksaleBuyList"=>$junksaleBuyList,
				"personalCVList"=>$personalCVList,
				"regdate"=>$regData,
				"personalVacancyList"=>$personalVacancyList);
				$userData->logo = GetFilename($userData->logo);
				$account->dataSource = array_merge($udata, $userData->GetData());
			}
			break;
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
			case "artist":
			{
				$newUser = false;
				$table = new CNativeDataTable("tbl__artist_doc");
				$userData = null;

        $groups_items = SQLProvider::ExecuteQuery("select * from tbl__artist_group
                                                             order by priority desc");
        $groups_list = "";
        $subgroups_list = "";
        $sel_groups = array();
        $selstyles = array();

				$images = array();
				$mp3s = array();
				if ($user->authorized)
				{
					$filter = new CAndFilter(new CEqFilter($table->fields["tbl_obj_id"],$user->id),
					new CAndFilter(new CEqFilter($table->fields["active"],1),
					new CEqFilter($table->fields["registration_confirmed"],1)));
					$userData = $table->SelectUnique($filter,false);
				}

				if ($this->IsPostBack)
				{
					$props = GP("properties");
					if (is_array($props))
					{
            $userValidator = $this->GetControl("artistValidator");
						if ($user->authorized)
						{
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
						}
            $props['city'] = trim($props['city']);
            $props['country'] = trim($props['country']);
						$errorsData = $userValidator->Validate(&$props);
            $sel_groups = $props['selected_group'];
            if (!sizeof($sel_groups)) {
              array_push($errorsData,"не указана категория");
            }
            $props['other_city'] = $props['city'];
            $props['other_country'] = $props['country'];

            //Validate youtube.
            if (strlen($props['youtube_video']) > 0 && !(preg_match('/^http:\/\/(www\.){0,1}youtube\.com\/watch\?v=[A-z0-9\-^&]+(.*)$/i',$props['youtube_video']) > 0 )) {
                array_push($errorsData,'Неверно указана ссылка на видео на YouTube');
            }

						$userData->FromHashMap($props);
            $styles= $props['style'];

						if (sizeof($errorsData)>0)
						{
							$errors->dataSource = array("message"=>$errorsData[0]);
							CDebugger::DebugArray($errors);
						}
						else
						{
							$city_id = $this->CheckCity($props['city']);
              if ($city_id) {
                $props['city'] = $city_id;
                $props['other_city'] = '';
              }
              else {
                $props['other_city'] = $props['city'];
                $props['city'] = 0;
              }
              $userData->city = $props['city'];
              $userData->other_city = $props['other_city'];

              $country_id = $this->CheckCountry($props['country']);
              if ($country_id) {
                $props['country'] = $country_id;
                $props['other_country'] = '';
              }
              else {
                $props['other_country'] = $props['country'];
                $props['country'] = 0;
              }
              $userData->country = $props['country'];
              $userData->other_country = $props['other_country'];

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
                $logo = $this->CreateLogo($userData->login,$flogo,"logo");
                if (!is_null($logo))
                {
                  $userData->logo = $logo;
                }
              }
              $userData->edit_date = time();

              foreach($sel_groups as $sg) {
                $userData->group = SQLProvider::ExecuteScalar("select parent_id from tbl__artist_subgroup where tbl_obj_id = $sg");
                break;
              }
              if ($userData->country)
                $userData->region = SQLProvider::ExecuteScalar("select region from tbl__countries where tbl_obj_id = ".$userData->country);
              else
                $userData->region = 31;

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
              $delphotos = GP('delete_mp3',array());
              if (sizeof($delphotos)>0)
              {
                $dstr = CStringFormatter::FromArray($delphotos);
                SQLProvider::ExecuteNonReturnQuery("delete from tbl__upload where tbl_obj_id in ($dstr)");
                SQLProvider::ExecuteNonReturnQuery("delete from tbl__artist2mp3file where file_id in ($dstr)");
                $images = SQLProvider::ExecuteQuery( "SELECT
                                      p.*
                                    FROM
                                      `tbl__artist2mp3file`  ap
                                      inner join `tbl__upload` p
                                      on ap.file_id = p.tbl_obj_id
                                      where artist_id=$userData->tbl_obj_id limit 5");
              }

              $user->name = $userData->title;
              $user->Save();

              $this->SetArtistStyles($userData);

						}
					}
				}

        if (!is_null($userData))
				{
					$subgroupsRaw = SQLProvider::ExecuteQueryReverse("select subgroup_id from tbl__artist2subgroup where artist_id=$userData->tbl_obj_id");
					$sel_groups = isset($subgroupsRaw["subgroup_id"])?$subgroupsRaw["subgroup_id"]:array();

					$selstyles  = SQLProvider::ExecuteQueryReverse("select style_id from tbl__artist2style where artist_id=$userData->tbl_obj_id");

          if (sizeof($selstyles))
            $selstyles = $selstyles['style_id'];

          if (!IsNullOrEmpty($userData->city)) {
             $userData->other_city = SQLProvider::ExecuteScalar(
               "select title from `tbl__city` where tbl_obj_id = ".$userData->city);
          }

          if (!IsNullOrEmpty($userData->country)) {
             $userData->other_country = SQLProvider::ExecuteScalar(
               "select title from `tbl__countries` where tbl_obj_id = ".$userData->country);
          }

					$images = SQLProvider::ExecuteQuery("SELECT
								  p.*
								FROM
								  `tbl__artist2photos`  ap
								  inner join `tbl__photo` p
								  on ap.child_id = p.tbl_obj_id
								  where parent_id=$userData->tbl_obj_id limit 8");
					$mp3s = SQLProvider::ExecuteQuery("SELECT
								  p.*
								FROM
								  `tbl__artist2mp3file`  ap
								  inner join `tbl__upload` p
								  on ap.file_id = p.tbl_obj_id
								  where artist_id=$userData->tbl_obj_id limit 5");

				}


				$logos = preg_split("/\//",$userData->logo);
				$ls = sizeof($logos);

				$imagesList = $this->GetControl("imagesList");
				$ikeys = array_keys($images);
				foreach ($ikeys as $ikey)
				{
					$images[$ikey]["ptype"] = "artist";
				}
				$imagesList->dataSource = $images;

				$mp3List = $this->GetControl("mp3List");
				$ikeys = array_keys($mp3s);
				foreach ($ikeys as $ikey)
				{
					$mp3s[$ikey]["ptype"] = "artist";
				}
				$mp3List->dataSource = $mp3s;

        $selected_groups = "";
        if (isset($sel_groups) and is_array($sel_groups)) {
          foreach($sel_groups as $sg) {
            $sg_title = SQLProvider::ExecuteScalar("select title from tbl__artist_subgroup where tbl_obj_id = $sg");
            $selected_groups .= '<div id="selected_id'.$sg.
            '"><input type="hidden" name="properties[selected_group]['.$sg.
          ']" value="'.$sg.'">'.$sg_title.
          ' <a href="" class="reg_del_group" onclick="javascript: SelectedGroupDel('.$sg.
          '); return false;">удалить</a></div>';
          }


        }
        foreach ($groups_items as $group) {
          $groups_list .= '<option value="'.$group['tbl_obj_id'].'">'.$group['title'].'</option>';
          $subgroups_items = SQLProvider::ExecuteQuery("select * from tbl__artist_subgroup where parent_id = ".$group['tbl_obj_id']."
                                                       order by priority desc");
          $subgroups_list .= '<div id="subgroup_id'.$group['tbl_obj_id'].'" style="display: none;">';
          foreach ($subgroups_items as $sgroup) {
            $chk = "";
            if (isset($sel_groups) and is_array($sel_groups) and
                !(array_search($sgroup['tbl_obj_id'],$sel_groups)===false)
                ) {
              $chk = "checked=\"checked\"";
            }

            $subgroups_list .= '<input id="checkbox_id'.$sgroup['tbl_obj_id'].'" type="checkbox" value="'.$sgroup['tbl_obj_id'].'" onclick="SelectSubGroup(this,\''.$sgroup['title'].'\')" '.$chk.'>'.$sgroup['title'].'<br>';
          }
          $subgroups_list .= "</div>";
        }

        $logo_link = "";
        if (!IsNullOrEmpty($userData->logo)) {
          $logo_file = GetFilename($userData->logo);
          $logo_link = '<a href="/upload/'.$logo_file.'" target="_blank">'.$logo_file.'</a>';
        }

        $artist_mus_ids = SQLProvider::ExecuteQuery("
          SELECT tbl_obj_id, style_group from tbl__artist_group
          where style_group > 0");
        $stls_q = SQLProvider::ExecuteQuery("
          SELECT * FROM `tbl__styles` where style_group>0
          order by title");
        $stls = array();
        foreach($stls_q as $stl) {
          if (!isset($stls[$stl['style_group']]))
            $stls[$stl['style_group']] = array();
          array_push($stls[$stl['style_group']],$stl);
        }
        $stl_gs = SQLProvider::ExecuteQuery("
          SELECT * FROM tbl__styles_groups order by title");

        $s_artist_mus_ids = "";
        $s_artist_stl_groups = "";
        foreach ($artist_mus_ids as $gr_id) {
          if (!IsNullOrEmpty($s_artist_mus_ids))
            $s_artist_mus_ids .= ",";
          $s_artist_mus_ids .= $gr_id['tbl_obj_id'];
          $s_artist_stl_groups .= "\n artist_stl_groups[".$gr_id['tbl_obj_id']."] = ".$gr_id['style_group'].";";
        }
        $s_artist_stl_group_ttl = "";
        $stylesArray = "";
        foreach ($stl_gs as $stl_g) {
          $s_artist_stl_group_ttl .="\n artist_stl_group_ttl[".$stl_g['tbl_obj_id']."] = '".$stl_g['title']."';";
          $stl_arr = "";
          if (isset($stls[$stl_g['tbl_obj_id']]))
            foreach ($stls[$stl_g['tbl_obj_id']] as $stl) {
              if (!IsNullOrEmpty($stl_arr))
              $stl_arr .= ",";
              $stl_arr .= "{id:".$stl['tbl_obj_id'].", title:'".$stl['title']."'}";
            }
          $stylesArray .= "\n artist_styles[".$stl_g['tbl_obj_id']."] = new Array($stl_arr);";
        }

        $s_selstyles = "";
        foreach($selstyles as $ss) {
          if (!IsNullOrEmpty($s_selstyles))
            $s_selstyles .= ",";
          $s_selstyles .= $ss;
        }

        $udata = array(
        "submit_text"=>$this->GetMessage($user->authorized?"save":"reg"),
        "imagesList"=>$imagesList->RenderHTML(),
        "mp3List"=>$mp3List->RenderHTML(),
        "images_visible"=>sizeof($images)>=4?"hidden":"visible",
        "mp3s_visible"=>sizeof($mp3s)>=5?"hidden":"visible",
        "groups_list"=>$groups_list,
        "subgroups_list"=>$subgroups_list,
        "selected_groups"=>$selected_groups,
        "st_visible"=>sizeof($sel_groups)>0?"":"display:none;",
        "logo_link"=>$logo_link,
        "artist_mus_ids"=>"[$s_artist_mus_ids]",
        "artist_stl_groups"=>$s_artist_stl_groups,
        "artist_stl_group_ttl"=>$s_artist_stl_group_ttl,
        "checked_styles" => "[$s_selstyles]",
        "stylesArray"=>$stylesArray);
        $userData->logo =GetFilename($userData->logo);
        $account->dataSource = array_merge($udata, $userData->GetData());
			}
			break;
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
			case "agency":
			{
				$newUser = false;
				$table = new CNativeDataTable("tbl__agency_doc");
				$userData = null;

        $userData = null;
        $activityList = new CCheckBoxList();
        $activityList->dataSource  = SQLProvider::ExecuteQuery("select * from tbl__agency_type ");
        $activityList->valueName = "tbl_obj_id";
        $activityList->titleName = "title";
        $activityList->baseName = "properties[kind_of_activity]";
        $activityList->class = "";
        $activityList->htmlEvents = array("onclick"=>"javascript: CheckCountAct(this);");
        $activityList->checkedValue = "";


				if ($user->authorized)
				{
					$filter = new CAndFilter(new CEqFilter($table->fields["tbl_obj_id"],$user->id),
					new CAndFilter(new CEqFilter($table->fields["active"],1),
					new CEqFilter($table->fields["registration_confirmed"],1)));
					$userData = $table->SelectUnique($filter,false);
				}
				if ($this->IsPostBack)
				{
					$props = GP("properties");
					if (is_array($props))
					{
						$userValidator = $this->GetControl("agencyValidator");
						$activityList->selectedValue = $props['kind_of_activity'];
						$cityList->selectedValue = $props['city'];
						if ($user->authorized)
						{
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
						}
						$errorsData = $userValidator->Validate(&$props);
            $kindList = $props['kind_of_activity'];
            if (!sizeof($kindList)) {
              array_push($errorsData,"не указан тип деятельности");
            }
            $props['other_city'] = $props['city'];
						$userData->FromHashMap($props);
						if (sizeof($errorsData)>0)
						{
							$errors->dataSource = array("message"=>$errorsData[0]);
							CDebugger::DebugArray($errors);
						}
						else
						{
							$city_id = $this->CheckCity($props['city']);
              if ($city_id) {
                $props['city'] = $city_id;
                $props['other_city'] = '';
              }
              else {
                $props['other_city'] = $props['city'];
                $props['city'] = 0;
              }
              $userData->city = $props['city'];
              $userData->other_city = $props['other_city'];

              if (isset($props["password"]))
              {
                if (!IsNullOrEmpty($props["password"]))
                {
                  $userData->password = md5($props["password"]);
                }
              }

              $cleanlogo = GP("logo_image_clean");
              if ($cleanlogo=="clean")
              {
                $this->CleanLogo($userData->logo_image);
              }
              $flogo = $_FILES["properties"];
              if (is_array($flogo))
              {
                $logo = $this->CreateLogo($userData->login,$flogo,"logo_image");
                if (!is_null($logo))
                {
                  $userData->logo_image = $logo;
                }
              }
              $userData->edit_date = time();
              $userData->kind_of_activity = 0;
              $table->UpdateObject(&$userData);
              $user->name = $userData->title;
              $user->Save();

              $delphotos = GP('delete_photos',array());

              if (sizeof($delphotos)>0)
              {
                $dstr = CStringFormatter::FromArray($delphotos);
                SQLProvider::ExecuteNonReturnQuery("delete from tbl__photo where tbl_obj_id in ($dstr)");
                SQLProvider::ExecuteNonReturnQuery("delete from tbl__agency_photos where child_id in ($dstr)");
              }

              SQLProvider::ExecuteNonReturnQuery("delete from tbl__agency2activity where tbl_obj_id=$userData->tbl_obj_id");
              foreach ($kindList as $kind)
              {
                  SQLProvider::ExecuteNonReturnQuery("insert into tbl__agency2activity (tbl_obj_id,kind_of_activity) values ($userData->tbl_obj_id,$kind)");
              }
						}
					}
				}


        if (!is_null($userData))
				{
          $kinds  = SQLProvider::ExecuteQueryReverse("select kind_of_activity from tbl__agency2activity where tbl_obj_id=$userData->tbl_obj_id");
					if (isset($kinds['kind_of_activity']))
					{
            $activityList->checkedValue = $kinds['kind_of_activity'];
					}

          if (!IsNullOrEmpty($userData->city)) {
             $userData->other_city = SQLProvider::ExecuteScalar(
               "select title from `tbl__city` where tbl_obj_id = ".$userData->city);
          }

					$images = SQLProvider::ExecuteQuery("SELECT
								  p.*
								FROM
								  `tbl__agency_photos`  ap
								  inner join `tbl__photo` p
								  on ap.child_id = p.tbl_obj_id
								  where parent_id=$userData->tbl_obj_id limit 8");

				}

				$logos = preg_split("/\//",$userData->logo_image);
				$ls = sizeof($logos);

        $imagesList = $this->GetControl("imagesList");
				$ikeys = array_keys($images);
				foreach ($ikeys as $ikey)
				{
					$images[$ikey]["ptype"] = "agency";
				}
				$imagesList->dataSource = $images;

        $logo_link = "";
        if (!IsNullOrEmpty($userData->logo_image)) {
          $logo_file = GetFilename($userData->logo_image);
          $logo_link = '<a href="/upload/'.$logo_file.'" target="_blank">'.$logo_file.'</a>';
        }

				$udata = array(
				"submit_text"=>$this->GetMessage($user->authorized?"save":"reg"),
				"activityList"=>$activityList->RenderHTML(),
        "imagesList"=>$imagesList->RenderHTML(),
        "logo_link"=>$logo_link);
				$userData->logo_image = GetFilename($userData->logo_image);
				$account->dataSource = array_merge($udata, $userData->GetData());
			}
			break;
		}

		return ($errors->renderHTML().$account->renderHTML());
  }

//--------------  --------------  --------------  --------------  --------------  --------------  --------------

	public function PreRender() {
		$user = new CSessionUser(null);
		CAuthorizer::AuthentificateUserFromCookie(&$user);
		CAuthorizer::RestoreUserFromSession(&$user);

    if (!$user->authorized) CURLHandler::Redirect("/authorizationrequired?target=".$_SERVER['REQUEST_URI']);

    if ($user->type == 'user')
      CURLHandler::Redirect ("/u_cabinet".CURLHandler::$query);

		$type = GP("data","personal");

		$cab = array();
		$u_cab_menu = $this->GetControl("r_cab_menu");
		$u_cab_menu->activeItem = "/r_cabinet/data/$type";
		$uid = $user->type.$user->id;
		$new_count = SQLProvider::ExecuteScalar("select count(*) as quan from tbl__messages m
												left join tbl__black_list bl on (reciever_id=bl.user_id and sender_id=blocked_id) and bl.user_id='$uid'
												where blocked_id is null and `status`='sent' and reciever_id='$uid'");
		if ($new_count > 0)
			$u_cab_menu->dataSource["/r_cabinet/data/my_messages"]["title"] .= "&nbsp;($new_count)";

    $u_link = "";
		$u_links = SQLProvider::ExecuteQuery("select ru.tbl_obj_id, IF(ru.nikname is NULL or ru.nikname = '',ru.title,ru.nikname) title from tbl__registered_user_link_resident rl left join tbl__registered_user ru on ru.tbl_obj_id = rl.user_id
		                                      where rl.resident_type = '".$user->type."' and rl.resident_id = ".$user->id);
		if (sizeof($u_links)>0)
		{
			$u_link = "<div style=\"padding-bottom: 5px;\">Привязанные пользователи:<br />";
			foreach ($u_links as $num=>$link)
			{
				$u_link .= "<a style=\"padding: 0 0 0 10px;\" href=\"/u_profile/type/user/id/".$link['tbl_obj_id']."\">".$link["title"]."</a><br>";
			}
			$u_link .= "</div>";
		}

		$user_tbl = $user->GetTable();
		$user_info = SQLProvider::ExecuteQuery("select * from $user_tbl where tbl_obj_id = $user->id");
		if (sizeof($user_info)>0) $user_info = $user_info[0];
		if (!IsNullOrEmpty($user_info["city"]))
      $user_info["city"] = SQLProvider::ExecuteScalar("select title from tbl__city where tbl_obj_id = ".$user_info["city"]);
    else
      $user_info["city"] = $user_info["other_city"];

		$cab["user_name"] = $user_info["title"];
		switch ($user->type) {
			case "contractor":
      case "agency":
        if (IsNullOrEmpty($user_info["logo_image"]))
          $cab["logo"] = "/images/nologo.png";
        else
          $cab["logo"] = "/upload/".GetFileName($user_info["logo_image"]);
        break;
      case "area":
      case "artist":
        if (IsNullOrEmpty($user_info["logo"]))
          $cab["logo"] = "/images/nologo.png";
        else
          $cab["logo"] = "/upload/".GetFileName($user_info["logo"]);
        break;
        break;
    }
		$cab["edit_link_href"] = "/r_cabinet/data/personal/action/edit";
		$cab["edit_link_title"] = "редактировать";

		$en_month = array("Jan","Feb","Mar","Apr","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
		$ru_month = array("января","февраля","марта","апреля","июня","июля","августа","сентября","октября","ноября","декабря");
		$cab["show_info"] = "none";
		$left_menu = array();
    $cab["user_class"] = $user->type;
    $link_page = "Перейти на страницу ";
    switch ($user->type) {
			case "contractor":
        $cab["user_type"] = "Подрядчик";
        $link_page .= "компании";
        break;

      case "area":
        $cab["user_type"] = "Площадка";
        $link_page .= "площадки";
        break;

      case "artist":
        if (!IsNullOrEmpty($user_info["country"]))
          $user_info["country"] = SQLProvider::ExecuteScalar("select title from tbl__countries where tbl_obj_id = ".$user_info["country"]);
        else
          $user_info["country"] = $user_info["other_country"];
        if (!IsNullOrEmpty($user_info["city"]))
          $user_info["city"] = $user_info["country"]." / ".$user_info["city"];
        else
          $user_info["city"] = $user_info["country"];

        $cab["user_type"] = "Артист";
        $link_page .= "артиста";
        break;

      case "agency":
        $cab["user_type"] = "Агентство";
        $link_page .= "агентства";
        break;
    }


		$left_menu = array();
		switch ($type) {
			case "personal" :
			$p_action = GP("action","");
			if ($p_action == "edit")
			{
				$cab["edit_link_href"] = GP("target","/r_cabinet/data/personal");
				$cab["edit_link_title"] = "вернуться назад";
				$cab["main_area"] = $this->renderProfile($user);
			}
			else
			{
				$cab["main_area"] = "<span style=\"font-size:13px; color:#000;\">";
				$cab["main_area"] .= "<br>";
				$cab["main_area"] .= "<span class=\"city_name\">".$user_info["city"]."</span><br>";
        $cab["main_area"] .= $u_link;
				$cab["main_area"] .= "<a class=\"black\" href=\"/".$user->type."/details/id/".$user->id."\">$link_page</a>";
				$cab["main_area"] .= "</span>";
			}
			break;
			case "my_favorite" :
				$f_type = GP("type","all");
				$left_menu = array(
					"my_favorite/type/contractor" => array(
						"type" => "link",
						"text" => "Подрядчики",
						"color" => "#ff5100",
						"selected" => false),
					"my_favorite/type/area" => array(
						"type" => "link",
						"text" => "Площадки",
						"color" => "#1198dc",
						"selected" => false),
					"my_favorite/type/artist" => array(
						"type" => "link",
						"text" => "Артисты",
						"color" => "#db117d",
						"selected" => false),
					"my_favorite/type/agency" => array(
						"type" => "link",
						"text" => "Агентства",
						"color" => "#9ecd1f",
						"selected" => false));
				if ($f_type != "all")
					$left_menu["my_favorite/type/$f_type"]["selected"] = true;

				if (GP("fav_del_sel",0) == 1)
				{
					$fav_del = GP("fav_del",array());
					foreach ($fav_del as $key=>$value)
					{
						foreach ($value as $key2=>$value2)
						{
							SQLProvider::ExecuteNonReturnQuery("delete from tbl__user_favorites
							where tbl__user_favorites.user_type='".$user->type."' and tbl__user_favorites.user_id=".$user->id." and
							tbl__user_favorites.resident_type='".$key."' and tbl__user_favorites.resident_id=".$value2);
						}
					}
					header("location: /r_cabinet/data/my_favorite/type/$f_type");
				}

				switch($f_type) {
					case "contractor" :
						$favorites = SQLProvider::ExecuteQuery("select distinct '#f05620' as typecolor, 'contractor' linktype, tbl__contractor_doc.tbl_obj_id,tbl__contractor_doc.title, tbl__user_favorites.date_add
							from tbl__user_favorites, tbl__contractor_doc
							where tbl__user_favorites.user_type='".$user->type."' and tbl__user_favorites.user_id=".$user->id." and
								tbl__user_favorites.resident_type='contractor' and tbl__contractor_doc.tbl_obj_id = tbl__user_favorites.resident_id
							order by tbl__contractor_doc.title
							");
					break;
					case "area" :
						$favorites = SQLProvider::ExecuteQuery("select distinct '#3399ff' as typecolor, 'area' linktype, tbl__area_doc.tbl_obj_id,tbl__area_doc.title, tbl__user_favorites.date_add
							from tbl__user_favorites, tbl__area_doc
							where tbl__user_favorites.user_type='".$user->type."' and tbl__user_favorites.user_id=".$user->id." and
								tbl__user_favorites.resident_type='area' and tbl__area_doc.tbl_obj_id = tbl__user_favorites.resident_id
							order by tbl__area_doc.title
							");
					break;
					case "artist" :
						$favorites = SQLProvider::ExecuteQuery("select distinct '#ff0066' as typecolor, 'artist' linktype, tbl__artist_doc.tbl_obj_id,tbl__artist_doc.title, tbl__user_favorites.date_add
							from tbl__user_favorites, tbl__artist_doc
							where tbl__user_favorites.user_type='".$user->type."' and tbl__user_favorites.user_id=".$user->id." and
								tbl__user_favorites.resident_type='artist' and tbl__artist_doc.tbl_obj_id = tbl__user_favorites.resident_id
							order by tbl__artist_doc.title
							");
					break;
					case "agency" :
						$favorites = SQLProvider::ExecuteQuery("select distinct '#99cc00' as typecolor, 'agency' linktype, tbl__agency_doc.tbl_obj_id,tbl__agency_doc.title, tbl__user_favorites.date_add
						from tbl__user_favorites, tbl__agency_doc
						where tbl__user_favorites.user_type='".$user->type."' and tbl__user_favorites.user_id=".$user->id." and
							tbl__user_favorites.resident_type='agency' and tbl__agency_doc.tbl_obj_id = tbl__user_favorites.resident_id
							order by tbl__agency_doc.title
						");
					break;
					case "all":
						$favorites = SQLProvider::ExecuteQuery("(select distinct '#f05620' as typecolor, 'contractor' linktype, tbl__contractor_doc.tbl_obj_id,tbl__contractor_doc.title, tbl__user_favorites.date_add
							from tbl__user_favorites, tbl__contractor_doc
							where tbl__user_favorites.user_type='".$user->type."' and tbl__user_favorites.user_id=".$user->id." and
								tbl__user_favorites.resident_type='contractor' and tbl__contractor_doc.tbl_obj_id = tbl__user_favorites.resident_id
							order by tbl__contractor_doc.title)
							union all
							(select distinct '#3399ff' as typecolor, 'area' linktype, tbl__area_doc.tbl_obj_id,tbl__area_doc.title, tbl__user_favorites.date_add
							from tbl__user_favorites, tbl__area_doc
							where tbl__user_favorites.user_type='".$user->type."' and tbl__user_favorites.user_id=".$user->id." and
								tbl__user_favorites.resident_type='area' and tbl__area_doc.tbl_obj_id = tbl__user_favorites.resident_id
							order by tbl__area_doc.title)
							union all
							(select distinct '#ff0066' as typecolor, 'artist' linktype, tbl__artist_doc.tbl_obj_id,tbl__artist_doc.title, tbl__user_favorites.date_add
							from tbl__user_favorites, tbl__artist_doc
							where tbl__user_favorites.user_type='".$user->type."' and tbl__user_favorites.user_id=".$user->id." and
								tbl__user_favorites.resident_type='artist' and tbl__artist_doc.tbl_obj_id = tbl__user_favorites.resident_id
							order by tbl__artist_doc.title)
							union all
							(select distinct '#99cc00' as typecolor, 'agency' linktype, tbl__agency_doc.tbl_obj_id,tbl__agency_doc.title, tbl__user_favorites.date_add
							from tbl__user_favorites, tbl__agency_doc
							where tbl__user_favorites.user_type='".$user->type."' and tbl__user_favorites.user_id=".$user->id." and
							tbl__user_favorites.resident_type='agency' and tbl__agency_doc.tbl_obj_id = tbl__user_favorites.resident_id
							order by tbl__agency_doc.title)
							");
					break;
				}
				for ($i=0; $i < sizeof($favorites); $i++)
				{
					$favorites[$i]["del_link"] = "/r_cabinet/data/my_favorite/type/$f_type/del/".$favorites[$i]["tbl_obj_id"];
					if ($favorites[$i]["date_add"] != '')
						$favorites[$i]["date_add"] = str_ireplace($en_month,$ru_month,date("d M Y H:i",strtotime($favorites[$i]["date_add"])));
				}
				$favorite_block = $this->GetControl("favorite");
				$favorite_block->dataSource = $favorites;

				$cab["main_area"] = "<form id='fav_del_form' action=\"\"><table cellspacing=\"0\" cellpadding=\"10\" width=\"600\">
				                     <tr style=\"color: #999999; font-weight: bold;\">
									 <td style=\"border-bottom: 1px solid #DCDCDC;\" nowrap=\"nowrap\">Название резидента</td>
									 <td style=\"border-bottom: 1px solid #DCDCDC;\" nowrap=\"nowrap\" width=\"150\">Дата добавления в избранное</td>
									 <td style=\"border-bottom: 1px solid #DCDCDC;\" nowrap=\"nowrap\" width=\"70\">[<a href=\"\" id=\"btn_fav_del\">Удалить все</a>]</td></tr>
									 ".$favorite_block->renderHTML()."
									 <tr><td></td><td></td>
									 <td>
										<input type=\"hidden\" name=\"fav_del_sel\" value=\"1\">
										<input type=\"hidden\" name=\"data\" value=\"my_favorite\">
									</td>
									 </tr></table></form><script type=\"text/javascript\">
										var del_selected = false;
										\$('#btn_fav_del').click(function(){
											if(confirm('Вы уверены что хотите удалить все из избранного?')) {
												\$('.fav_del').attr('checked','checked');
												\$('#fav_del_form').submit();
											}
										return false;});
										\$('.fav_del_cross').click( function() {
											if(confirm('Вы уверены что хотите удалить '+\$(this).parents('tr').find('a').html()+' из избранного?')) {
												\$(this).next().attr('checked','checked');
												\$('#fav_del_form').submit();
											}
										});
									</script>";
			break;

			case "my_marks" :
				$m_type = GP("type","all");
                $delete_like = GP("delete_like",array());
				foreach ($delete_like as $value)
				{
                    SQLProvider::ExecuteNonReturnQuery(
                        "delete from tbl__userlike
                        where from_resident_type='user' and from_resident_id=".$user->id."
                        and CONCAT(to_resident_type,to_resident_id) = '$value'");
                }

				$left_menu = array(
					"my_marks/type/contractor" => array(
						"type" => "link",
						"text" => "Подрядчики",
						"color" => "#ff5100",
						"selected" => false),
					"my_marks/type/area" => array(
						"type" => "link",
						"text" => "Площадки",
						"color" => "#1198dc",
						"selected" => false),
					"my_marks/type/artist" => array(
						"type" => "link",
						"text" => "Артисты",
						"color" => "#db117d",
						"selected" => false),
					"my_marks/type/agency" => array(
						"type" => "link",
						"text" => "Агентства",
						"color" => "#9ecd1f",
						"selected" => false));
				$left_menu["my_marks/type/$m_type"]["selected"] = true;
				$marks = array();
				switch($m_type)
				{
					case "contractor" :
						$marks = SQLProvider::ExecuteQuery("
							select
								tbl__contractor_doc.tbl_obj_id,
								tbl__contractor_doc.title,
								'#f05620' as typecolor,
								'contractor' as linktype,
								tbl__userlike.date,
								UNIX_TIMESTAMP(tbl__userlike.date) as elapsed
							from
								tbl__contractor_doc, tbl__userlike
							where
								tbl__userlike.to_resident_type='contractor' and
								tbl__userlike.to_resident_id=tbl__contractor_doc.tbl_obj_id and
								tbl__userlike.from_resident_type='".$user->type."' and
								tbl__userlike.from_resident_id=".$user->id." order by 2");
					break;
					case "area" :
						$marks = SQLProvider::ExecuteQuery("
							select
								tbl__area_doc.tbl_obj_id,
								tbl__area_doc.title,
								'#3399ff' as typecolor,
								'area' as linktype,
								tbl__userlike.date,
								UNIX_TIMESTAMP(tbl__userlike.date) as elapsed
							from
								tbl__area_doc, tbl__userlike
							where
								tbl__userlike.to_resident_type='area' and
								tbl__userlike.to_resident_id=tbl__area_doc.tbl_obj_id and
								tbl__userlike.from_resident_type='".$user->type."' and
								tbl__userlike.from_resident_id=".$user->id." order by 2");
					break;
					case "artist" :
						$marks = SQLProvider::ExecuteQuery("
							select
								tbl__artist_doc.tbl_obj_id,
								tbl__artist_doc.title,
								'#ff0066' as typecolor,
								'artist' as linktype,
								tbl__userlike.date,
								UNIX_TIMESTAMP(tbl__userlike.date) as elapsed
							from
								tbl__artist_doc, tbl__userlike
							where
								tbl__userlike.to_resident_type='artist' and
								tbl__userlike.to_resident_id=tbl__artist_doc.tbl_obj_id and
								tbl__userlike.from_resident_type='".$user->type."' and
								tbl__userlike.from_resident_id=".$user->id." order by 2");
					break;
					case "agency" :
						$marks = SQLProvider::ExecuteQuery("
							select
								tbl__agency_doc.tbl_obj_id,
								tbl__agency_doc.title,
								'#99cc00' as typecolor,
								'agency' as linktype,
								tbl__userlike.date,
								UNIX_TIMESTAMP(tbl__userlike.date) as elapsed
							from
								tbl__agency_doc, tbl__userlike
							where
								tbl__userlike.to_resident_type='agency' and
								tbl__userlike.to_resident_id=tbl__agency_doc.tbl_obj_id and
								tbl__userlike.from_resident_type='".$user->type."' and
								tbl__userlike.from_resident_id=".$user->id." order by 2");
					break;
					case "all" :
						$marks = SQLProvider::ExecuteQuery("
						(
						select
								tbl__contractor_doc.tbl_obj_id,
								tbl__contractor_doc.title,
								'#f05620' as typecolor,
								'contractor' as linktype,
								tbl__userlike.date,
								UNIX_TIMESTAMP(tbl__userlike.date) as elapsed
							from
								tbl__contractor_doc, tbl__userlike
							where
								tbl__userlike.to_resident_type='contractor' and
								tbl__userlike.to_resident_id=tbl__contractor_doc.tbl_obj_id and
								tbl__userlike.from_resident_type='".$user->type."' and
								tbl__userlike.from_resident_id=".$user->id." order by 2
						)
						union all
						(
						select
								tbl__area_doc.tbl_obj_id,
								tbl__area_doc.title,
								'#3399ff' as typecolor,
								'area' as linktype,
								tbl__userlike.date,
								UNIX_TIMESTAMP(tbl__userlike.date) as elapsed
							from
								tbl__area_doc, tbl__userlike
							where
								tbl__userlike.to_resident_type='area' and
								tbl__userlike.to_resident_id=tbl__area_doc.tbl_obj_id and
								tbl__userlike.from_resident_type='".$user->type."' and
								tbl__userlike.from_resident_id=".$user->id." order by 2
						)
						union all
						(
						select
								tbl__artist_doc.tbl_obj_id,
								tbl__artist_doc.title,
								'#ff0066' as typecolor,
								'artist' as linktype,
								tbl__userlike.date,
								UNIX_TIMESTAMP(tbl__userlike.date) as elapsed
							from
								tbl__artist_doc, tbl__userlike
							where
								tbl__userlike.to_resident_type='artist' and
								tbl__userlike.to_resident_id=tbl__artist_doc.tbl_obj_id and
								tbl__userlike.from_resident_type='".$user->type."' and
								tbl__userlike.from_resident_id=".$user->id." order by 2
						)
						union all
						(
						select
								tbl__agency_doc.tbl_obj_id,
								tbl__agency_doc.title,
								'#99cc00' as typecolor,
								'agency' as linktype,
								tbl__userlike.date,
								UNIX_TIMESTAMP(tbl__userlike.date) as elapsed
							from
								tbl__agency_doc, tbl__userlike
							where
								tbl__userlike.to_resident_type='agency' and
								tbl__userlike.to_resident_id=tbl__agency_doc.tbl_obj_id and
								tbl__userlike.from_resident_type='".$user->type."' and
								tbl__userlike.from_resident_id=".$user->id." order by 2
						)");
					break;
				}

			foreach ($marks as $key=>$mark)
			{
				if ($marks[$key]["date"] != '')
						$marks[$key]["date"] = str_ireplace($en_month,$ru_month,date("d M Y H:i",strtotime($marks[$key]["date"])));
			}

			$marks_block = $this->GetControl("marks");
			$marks_block->dataSource = $marks;

			$cab["main_area"] = "<form method=\"post\"><table cellspacing=\"0\" cellpadding=\"10\" width=\"600\">
				                     <tr style=\"color: #999999; font-weight: bold;\">
									 <td style=\"border-bottom: 1px solid #DCDCDC;\" nowrap=\"nowrap\">Название резидента</td>
									 <td style=\"border-bottom: 1px solid #DCDCDC;\" nowrap=\"nowrap\" width=\"150\">Дата оценки</td>
									 <td style=\"border-bottom: 1px solid #DCDCDC;\" nowrap=\"nowrap\" width=\"70\">Больше не нравится</td>
									 </tr>
									 ".$marks_block->renderHTML()."
                                     <tr><td style=\"border-bottom: 1px solid #DCDCDC;\" colspan=\"2\">&nbsp;</td>
                                         <td style=\"border-bottom: 1px solid #DCDCDC; text-align: center;\" ><input type=\"submit\" value=\"Удалить\"></td></tr></table></form>";

			break;
//================================================================================================================================================================
			case "my_messages":
			$thread = GPT("thread");

			$pageSize = 20;
			$page = GP("page",1);

			$m_action = GP("action","inbox");

			$rewriteParams = array("data"=>"my_messages","action"=>$m_action);

			$uid = $user->type.$user->id;
			$left_menu = array(
				"my_messages/action/compose" => array(
					"type" => "link",
					"text" => "Написать<br /> сообщение",
					"color" => "",
					"selected" => false),
				" " => array(
					"type" => "link",
					"text" => "",
					"color" => "#000",
					"selected" => false),
				"my_messages/action/inbox" => array(
					"type" => "link",
					"text" => "Входящие",
					"color" => "#000",
					"selected" => false),
				"my_messages/action/outbox" => array(
					"type" => "link",
					"text" => "Отправленные",
					"color" => "#000",
					"selected" => false),
				"my_messages/action/blacklist" => array(
					"type" => "link",
					"text" => "Черный список",
					"color" => "#000",
					"selected" => false)
				);
			$new_count = SQLProvider::ExecuteScalar("select count(*) as quan from tbl__messages m
																left join tbl__black_list bl on (reciever_id=bl.user_id and sender_id=blocked_id) and bl.user_id='$uid'
															where blocked_id is null and `status`='sent' and reciever_id='$uid'");
			if ($new_count>0)
			{
				$left_menu["my_messages/action/inbox"]["text"] .= "&nbsp;($new_count)";
			}

			$left_menu["my_messages/action/$m_action"]["selected"] = true;
			if (GP("delete_multiple")==1&&($m_action=="inbox"||$m_action=="outbox"))
			{
				$del_mess = GP("delete_mess",array());
				foreach ($del_mess as $key=>$value)
				{
					if ($m_action=="inbox")
					{
						SQLProvider::ExecuteNonReturnQuery("update tbl__messages set status='deleted' where tbl_obj_id=$value and reciever_id='$uid'");
					}
					elseif($m_action=="outbox")
					{
						SQLProvider::ExecuteNonReturnQuery("update tbl__messages set sender_deleted=1 where tbl_obj_id=$value and sender_id='$uid'");
					}
				}
			}
			$cab["main_area"] ='<br /><br /><div id="comments_body">';

			$is_sent = false;
			$reply_mess = null;
			if ($m_action=="compose"||$m_action=="reply"||$m_action=="select"||$m_action=="delete"||$m_action=="sent"||$m_action=="view"||$m_action=="block"||$m_action=="unblock"||$m_action == "error_sent")
			{
				$reciever_id=0;
				$reciever_type = null;
				$reciever_data = null;
				if ($m_action=="compose"||$m_action=="block"||$m_action=="unblock")
				{
					$reciever_id = GP("id",0);
					$reciever_type = GP("type");
					if ($m_action=="block")
					{
						SQLProvider::ExecuteNonReturnQuery("replace into tbl__black_list values('$uid','$reciever_type$reciever_id')");
						CURLHandler::Redirect("/r_cabinet/data/my_messages/");
					}
					elseif ($m_action=="unblock")
					{
						SQLProvider::ExecuteNonReturnQuery("delete from tbl__black_list where user_id='$uid' and blocked_id='$reciever_type$reciever_id'");
						CURLHandler::Redirect("/r_cabinet/data/my_messages/");
					}
				}
				elseif($m_action=="reply"||$m_action=="delete"||$m_action=="view")
				{
					$reply_id = GPT("rid");
					$r_mess = SQLProvider::ExecuteQuery("select * from tbl__messages where tbl_obj_id=$reply_id");
					if (sizeof($r_mess)==0)
					{
						CURLHandler::Redirect("/r_cabinet/data/my_messages/");
					}
					if ($r_mess[0]["status"]!="read"&&$m_action!="view")
					{
						SQLProvider::ExecuteNonReturnQuery("update tbl__messages set status='read', time_read = NOW() where tbl_obj_id=$reply_id");
					}
					$sid = $r_mess[0][$m_action=="view"?"reciever_id":"sender_id"];
					$matches = array();
					preg_match("/([a-z]+)(\d+)/i",$sid,&$matches);
					$reciever_id = $matches[2];
					$reciever_type = $matches[1];
					$reply_mess = $r_mess[0];
					if ($m_action=="delete")
					{
						if ($reply_mess["reciever_id"]==$uid)
						{
							SQLProvider::ExecuteNonReturnQuery("update tbl__messages set status='deleted' where tbl_obj_id=$reply_id");
						}
						elseif($reply_mess["sender_id"]==$uid)
						{
							SQLProvider::ExecuteNonReturnQuery("update tbl__messages set sender_deleted=1 where tbl_obj_id=$reply_id");
						}
						CURLHandler::Redirect("/r_cabinet/data/my_messages/");
					}

				}
				elseif ($m_action=="sent" || $m_action=="error_sent")
				{
					$is_sent = true;
				}

				if (!$is_sent &&$m_action!="select")
				{
					$reciever_data = SQLProvider::ExecuteQuery("select * from vw__all_users_full where user_id=$reciever_id and `type`='$reciever_type'");
					if (sizeof($reciever_data)==0)
					{
						CURLHandler::Redirect("/r_cabinet/data/my_messages/action/select/");
					}
					$reciever_data = $reciever_data[0];
					//Проверка количества пользователей
					$sentUserCnt = SQLProvider::ExecuteQuery("
						SELECT count(distinct reciever_id) cnt FROM `tbl__messages`
						WHERE date(time) = current_date()
							and sender_id = '".mysql_real_escape_string($uid)."'
							and reciever_id <> '".mysql_real_escape_string($reciever_data["user_key"])."'");
					if (sizeof($sentUserCnt)>0)
						$sentUserCnt = $sentUserCnt[0]['cnt'];
					else
						$sentUserCnt = 0;
					if ($sentUserCnt >= 10)
						CURLHandler::Redirect("/u_cabinet/data/my_messages/action/error_sent/");

					if ($this->IsPostBack&&$m_action!="view")
					{
						$table = new CNativeDataTable("tbl__messages");

						$newMessage = $table->CreateNewRow(true);
						$newMessage->text = GP("message_text");
						$newMessage->time = date("Y-m-d H:i:s");
						$newMessage->reciever_id = $reciever_data["user_key"];
						$newMessage->sender_id = $uid;
						if ($m_action=="reply")
						{
							$newMessage->reply_to_id = $reply_mess["tbl_obj_id"];
							$newMessage->thread_id = $reply_mess["thread_id"];
						}
						$table->InsertObject(&$newMessage);
						if ($this->action=="reply")
						{
							SQLProvider::ExecuteNonReturnQuery("update tbl__messages set status='replied' where tbl_obj_id=$reply_id");
						}
						else
						{
							$newMessage->thread_id = $newMessage->tbl_obj_id;
							$table->UpdateObject($newMessage);
						}
						SQLProvider::ExecuteNonReturnQuery("replace into tbl__message_users_list values('$uid','".$reciever_data["user_key"]."')");
						SQLProvider::ExecuteNonReturnQuery("replace into tbl__message_users_list values('".$reciever_data["user_key"]."','$uid')");
						$sender_name = $user->name;
						$res_info = SQLProvider::ExecuteQuery("select title,email from vw__all_users where CONCAT(login_type,tbl_obj_id) = '".$newMessage->reciever_id."'");
						$res_info = $res_info[0];
						$mailpath = "/pagecode/settings/r_cabinet/msg_mail.htm";
						$mailtitle = $sender_name." написал Вам сообщение на портале eventcatalog.ru";
            $app = CApplicationContext::GetInstance();
						$title = iconv($app->appEncoding,"utf-8",$mailtitle);
						$mailData["to_name"] = iconv($app->appEncoding,"utf-8",$res_info["title"]);
						$mailData["from_name"] = iconv($app->appEncoding,"utf-8",$sender_name);
						$mailData["link"] = "http://".$_SERVER['HTTP_HOST']."/r_cabinet/data/my_messages/action/reply/rid/".$newMessage->tbl_obj_id;
						$mbody = CStringFormatter::Format(file_get_contents(RealFile($mailpath)),$mailData);
						SendHTMLMail($res_info["email"],$mbody,$title,"noreply@eventcatalog.ru","noreply@eventcatalog.ru");

						CURLHandler::Redirect("/r_cabinet/data/my_messages/action/sent/");
					}
				}

				if ($is_sent){
				  if ($m_action == "error_sent")
					  $cab["main_area"] .='<h1>Вы не можете отсылать сообщения более чем десяти пользователям в день.</h1>';
					else
					  $cab["main_area"] .='<h1>Сообщение отправлено</h1>';
				}
				else
				{
					$cab["main_area"] .='<div style="width:100%;">
											<p style="font-size:16px; font-weight:bold;">'.($m_action=="compose"||$m_action=="select"?"Новое сообщение":"").'</p>';

					if ($m_action=="select")
					{
						$message_user_list = SQLProvider::ExecuteQuery("select * from vw__all_users_full");
						//where user_key in(select distinct ml.sender_id from tbl__message_users_list 
						//ml left join tbl__black_list bl on 
						//bl.user_id=ml.user_id and ml.sender_id=bl.blocked_id 
					    //where ml.user_id='$uid' and blocked_id is null)
						if (sizeof($message_user_list)==0)
						{
							$cab["main_area"].='<p>Ваш список возможных адресатов пуст</p>';
						}
						else
						{
							array_unshift(&$message_user_list,array("user_id"=>0,"type"=>0,"title"=>"-- Выберите пользователя"));
							$options= "";
							for ($i=0;$i<sizeof($message_user_list);$i++)
							{
								$options.='<option value="'.$message_user_list[$i]["type"].'|'.$message_user_list[$i]["user_id"].'">'.$message_user_list[$i]["title"].'</option>';
							}

							$cab["main_area"].='
									<script type="text/javascript" language="javascript">
										function SenderChanged()
										{
											var senders_list = document.getElementById("senders_list");
											var sender_uid = senders_list.options[senders_list.selectedIndex].value;
											var sen_data = sender_uid.split("|");
											document.getElementById("select_sender").disabled = sen_data[0]=="0";
											document.getElementById("compose_form").action = "/r_cabinet/data/my_messages/action/compose/type/"+sen_data[0]+"/id/"+sen_data[1]+"/";
										}
									</script><form id="compose_form">
									<table border="0">
										<tr>
											<td valign="middle">Получатель сообщения:</td>
											<td valign="middle"><select id="senders_list" onChange="javascript:SenderChanged();">'.$options.'</select></td>
											<td valign="middle"><input id="select_sender" type="submit" value="Продолжить &rarr;" disabled="disabled"></td>
										</tr>
									</table></form>
								';
						}
						$cab["main_area"].='</div>';
					}
					else
					{

						$cab["main_area"].='<table border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td valign="middle" >'.($m_action=="reply"?"От":"Кому").':</td>
												<td style="width:80px;height:40px;" align="center"><div style="width:60px; height:40px; border: 1px solid #D5D5D5;"><img border="0" height="40" width="60" src="/upload/'.($reciever_data["logo"]==''?"images/nologo.png":$reciever_data["logo"]).'"/></div></td>
												<td valign="middle">
													<a style="font-size:16px; color:#0063AF; font-weight:bold;" href="/u_profile/type/'.$reciever_type.'/id/'.$reciever_id.'">'.$reciever_data["title"].'</a>
													'.($m_action=="compose"?"":'&nbsp;&nbsp;<a onClick="return confirm(\'Вы действительно хотите добавить данного адресата в черный список?\');" href="/r_cabinet/data/my_messages/action/block/type/'.$reciever_type.'/id/'.$reciever_id.'" style="color:#888888; font-size:10px; text-decoration:underline;">в черный список</a>
													<br/>
													<span style="color:#999999; font-size:11px;">'.str_ireplace($en_month,$ru_month,date("d M Y H:i",strtotime($reply_mess["time"]))).'</span>').'

												</td>
											</tr>
											<tr><td>&nbsp;</td></tr>
										</table>';

						if ($m_action=="reply"||$m_action=="view")
						{
							$cab["main_area"].='<div style="color:#333333; background-color:#EEEEEE; padding:8px; margin:0 0 12px; -moz-border-radius: 6px 6px 6px 6px;">'.ProcessMessage($reply_mess["text"])."</div>";
							if ($m_action=="reply")
							{
								$cab["main_area"].='<p>Ваш ответ:</p>';
							}

						}
						if ($m_action=="reply"||$m_action=="compose")
						{
							$cab["main_area"].='<form method="post">
										<textarea name="message_text" style="width:100%; height:100px; border:1px solid #999999; font-size:12px; -moz-border-radius: 6px 6px 6px 6px;"></textarea><br/><br/>
										<input type="submit" value="Отправить"/><br/><br/><br/>
										</form>';
						}
						elseif ($m_action=="view")
						{
							$cab["main_area"].='<form method="get" action="/r_cabinet/data/my_messages/action/delete/rid/'.$reply_mess["tbl_obj_id"].'/">
										<input type="submit" value="Удалить" onClick="javascript:return confirm(\'Удалить данное сообщение\');"/><br/><br/><br/>
										</form>';
						}
						$cab["main_area"].='</div>';
					}
				}
			}
			else
			{
				$cab["main_area"] .= '
		    <script type="text/javascript" language="javascript">
		    	function SelectMultiple()
		    	{
		    		var all_selected = true;
		    		var i=0;
		    		for (i=0;i<arguments.length;i++)
		    		{
		    			if (!document.getElementById("delete_mess_"+arguments[i]).checked)
		    			{
		    				all_selected = false;
		    			}
		    		}
		    		for (i=0;i<arguments.length;i++)
		    		{
						document.getElementById("delete_mess_"+arguments[i]).checked=!all_selected;
		    		}
		    		return false;
		    	}
		    </script>';

				$pages=0;
				if ($m_action=="inbox"||$m_action=="outbox")
				{
					$count = SQLProvider::ExecuteQuery("select count(*) as quan from tbl__messages m
																inner join vw__all_users_full u on ".($m_action=="inbox"?"sender_id":"reciever_id")."=user_key
																left join tbl__black_list bl on (reciever_id=bl.user_id and sender_id=blocked_id or sender_id=bl.user_id and reciever_id=blocked_id) and bl.user_id='$uid'
															where blocked_id is null and  ".($m_action=="inbox"?"`status`!='deleted' and reciever_id='$uid'":"ifnull(sender_deleted,0)<>1 and sender_id='$uid'"));

					$count = $count[0]["quan"];
					$pages = floor($count/$pageSize)+(($count%$pageSize==0)?0:1);

					if (($page>$pages)&&($pages>0))
					{
						$page = $pages;
						$rewriteParams["page"] = $page;
						CURLHandler::Redirect(CURLHandler::$currentPath.CURLHandler::BuildRewriteParams($rewriteParams));
					}

					$messages = SQLProvider::ExecuteQuery("select m.*,u.*, bl.blocked_id from tbl__messages m
																inner join vw__all_users_full u on ".($m_action=="inbox"?"sender_id":"reciever_id")."=user_key
																left join tbl__black_list bl on (reciever_id=bl.user_id and sender_id=blocked_id or sender_id=bl.user_id and reciever_id=blocked_id) and bl.user_id='$uid'
                                                            where blocked_id is null and  ".($m_action=="inbox"?"`status`!='deleted' and reciever_id='$uid'":"ifnull(sender_deleted,0)<>1 and sender_id='$uid'")."
															order by `time` desc
															limit ".(($page-1)*$pageSize).",$pageSize");

					if (sizeof($messages)>0)
					{
						$mids = array();
						for ($i=0;$i<sizeof($messages);$i++) {
                            $messages[$i]["display_delete"] = (($messages[$i]["sender_id"]==$uid)&&($messages[$i]["status"]=="sent"))?"":"display:none;";
							$messages[$i]["date"] = date("d M Y H:i",strtotime($messages[$i]["time"]));
							$messages[$i]["date"] = str_ireplace($en_month,$ru_month,$messages[$i]["date"]);
							$messages[$i]["action"] = $m_action=="inbox"?"reply":"view";
                            if ($m_action=="outbox") {
                                if ($messages[$i]["time_read"]) {
                                    $messages[$i]["date_read"] = date("d M Y H:i",strtotime($messages[$i]["time_read"]));
                                    $messages[$i]["date_read"] = str_ireplace($en_month,$ru_month,$messages[$i]["date_read"]);
                                }
                                else
                                    $messages[$i]["date_read"] = "&nbsp";
                                $messages[$i]["date_read"] = "<td>".$messages[$i]["date_read"]."</td>";
                            }
                            else
                                $messages[$i]["date_read"] = "";
							$mids[]="'".$messages[$i]["tbl_obj_id"]."'";
						}

						$messagesList = $this->GetControl("messagesList");
						$messagesList->dataSource = $messages;



						$cab["main_area"] .='<form method="post"><table cellspacing=0 cellpadding=0 class="message_list">
										<tr>
										   <th>&nbsp;</th>
											<th>'.($m_action=="inbox"?"От":"Кому").'</th>
											<th>Тема</th>
											<th>Дата</th>'.($m_action=="outbox"?"<th>Прочитано</th>":"").'
											<th align="center">'.($m_action=="blacklist"?"&nbsp;": '[ <a id="selall" href="#" style="color:#0063AF;" onClick="javascript:return SelectMultiple('.implode(",",$mids).');">Выбрать все</a> ]').'</th>
										</tr>
										'.$messagesList->renderHTML().'
										'.($m_action=="blacklist"?"":
										'<tr>
											<td colspan="'.($m_action=="inbox"?"4":"5").'">&nbsp;</td>
											<td align="center"><input type="hidden" name="delete_multiple" value="1"><input type="submit" value="Удалить"/></td>
										</tr>').'
										</table></form>';

					}
					else
					{
						$cab["main_area"] .='<div style="padding: 10px 5px;">У Вас нет '.($m_action=="inbox"?"входящих":"отправленных").' сообщений</div>';
					}



				}
				elseif ($m_action=="blacklist")
				{
					$count = SQLProvider::ExecuteQuery("select count(*) as quan from vw__all_users_full u
															inner join tbl__black_list bl on bl.user_id='$uid' and user_key=bl.blocked_id");

					$count = $count[0]["quan"];
					$pages = floor($count/$pageSize)+(($count%$pageSize==0)?0:1);

					if (($page>$pages)&&($pages>0))
					{
						$page = $pages;
						$rewriteParams["page"] = $page;
						CURLHandler::Redirect(CURLHandler::$currentPath.CURLHandler::BuildRewriteParams($rewriteParams));
					}

					$black_users =  SQLProvider::ExecuteQuery("select u.* from vw__all_users_full u
															inner join tbl__black_list bl on bl.user_id='$uid' and user_key=bl.blocked_id
															order by `title` asc
															limit ".(($page-1)*$pageSize).",$pageSize");
					if (sizeof($black_users)>0)
					{

						$blackList = $this->GetControl("blackList");
						$blackList->dataSource = $black_users;

						$cab["main_area"] .='<table border="0">
												'.$blackList->renderHTML().'
											 </table>';
					}
					else
					{
						$cab["main_area"] .='<div style="padding: 10px 5px;">Поздравляю, у вас нет никого в черном списке</div>';
					}
				}
				$pager = $this->GetControl("pager");
				$pager->currentPage = $page;
				$pager->totalPages = $pages;
				$pager->rewriteParams = $rewriteParams;

				if ($pages>1)
				{
					$cab["main_area"] .= "<div>".$pager->renderHTML()."</div>";
				}
				$cab["main_area"] .= "</div>";

			}
			break;

			case "my_comments":

			$pageSize = 20;
			$page = GP("page",1);
			$rewriteParams = array("data"=>"my_comments");

			$left_menu = array();

			$uid = $user->type.$user->id;

			$comments = SQLProvider::ExecuteQuery("select * from tbl__comments c
						left join
						vw__comment_targets  u
						on c.target_id=u.user_key
						 where c.sender_id='$uid' order by c.time desc
						 limit ".(($page-1)*$pageSize).",$pageSize");

			for ($i=0;$i<sizeof($comments);$i++)
			{
				$comments[$i]["date"] = date("d.m.Y H:i",strtotime($comments[$i]["time"]));
				$matches = array();
				preg_match("/([A-Za-z_]+)(\d+)/",$comments[$i]["user_key"],&$matches);
				if (sizeof($matches)==3)
				{
					$com_type = $matches[1];
					$com_id =  $matches[2];

					if ($com_type=="event"||$com_type=="magazine")
					{
						$com_type .= "s";
					}
					if ($com_type=="show_technology")
					{
						$com_type .= "show_technologies";
					}
					switch ($com_type)
					{
						case "artist":
						case "area":
						case "contractor":
						case "agency":
						case "news":
						case "events":
						case "magazines":
						case "book":
						case "show_technologies":
						case "opened_area":
						{
							$comments[$i]["url"] = "$com_type/details/id/$com_id";
						} break;
						case "resident_news":
						{
							$comments[$i]["url"] = "$com_type/news/id/$com_id";
						} break;

					}
				}
			}

			$commentsList = $this->GetControl("commentsList");
			$commentsList->dataSource = $comments;

			$cab["main_area"] = "<div class='comments_body'><table cellspacing=0 cellpadding=4 width=\"100%\"><tr class=\"underline_comment\"><td colspan=\"2\">&nbsp;</td></tr>".$commentsList->renderHTML()."</table>";

			$count = SQLProvider::ExecuteQuery("select count(*) as quan from tbl__comments c where c.sender_id='$uid'");
			$count = $count[0]["quan"];
			$pages = floor($count/$pageSize)+(($count%$pageSize==0)?0:1);

			$pager = $this->GetControl("pager");
			$pager->currentPage = $page;
			$pager->totalPages = $pages;
			$pager->rewriteParams = $rewriteParams;

			if ($pages>1)
			{
				$cab["main_area"] .= "<div>".$pager->renderHTML()."</div></div>";
			}

			break;

        case "my_news" :
        $delete_news = GP("delete_news",array());
				foreach ($delete_news as $value)
				{
                    SQLProvider::ExecuteNonReturnQuery(
                        "delete from tbl__resident_news where tbl_obj_id = $value");
                }

        $news = SQLProvider::ExecuteQuery("select tbl_obj_id,date,title,active from tbl__resident_news where resident_type='".$user->type."' and resident_id=".$user->id);

		foreach ($news as &$item){
            $item['user_class'] = $user->type;
            $dt = strtotime($item["date"]);
            switch ($item['active']) {
            case 0 :
              $item['status'] = 'Заявка на рассмотрении';
              break;
            case 1 :
              $item['status'] = 'Новость размещена<br><a href="/resident_news/news/id/'.$item['tbl_obj_id'].'">Посмотреть</a>';
              break;
            case -1 :
              $item['status'] = 'Заявка отклонена';
              break;
            }

            if ($item["date"] != '')
                $item["date"] = str_ireplace($en_month,$ru_month,date("d M Y",strtotime($item["date"])));
        }

        $newsblock = $this->GetControl("news");
		$newsblock->dataSource = $news;

		$cab["main_area"] = "
			<a href='/add_res_news' style='color: black;'>Добавить новость</a><br><br>
            <form method='post'>
			<table cellspacing='0' cellpadding='10' width='900'>
			<tr style=\"color: #999999; font-weight: bold;\">
				<td style=\"border-bottom: 1px solid #DCDCDC;\" nowrap=\"nowrap\">Заголовок</td>
				<td style=\"border-bottom: 1px solid #DCDCDC;\" nowrap=\"nowrap\" width=\"150\">Дата отправки запроса на размещение</td>
				<td style=\"border-bottom: 1px solid #DCDCDC;\" nowrap=\"nowrap\" width=\"150\">Статус размещения</td>
				<td style=\"border-bottom: 1px solid #DCDCDC;\" nowrap=\"nowrap\" width=\"70\">Удалить</td>
			</tr>".$newsblock->renderHTML()."<tr><td style=\"border-bottom: 1px solid #DCDCDC;\" colspan=\"3\">&nbsp;</td>
            <td style=\"border-bottom: 1px solid #DCDCDC; text-align: center;\" ><input type=\"submit\" value=\"Удалить\"></td></tr></table></form>";
		break;
//=======================================================
// PRO account
//=======================================================
      case "pro":
        $pt = GetParam("pro_type","p");
        if ($this->IsPostBack && is_numeric($pt) && $pt>0 && $pt<=8) {
          $pro_type = 1;
          if ($pt%2 == 0)
            $pro_type = 2;
          $cost = 0;
          $period = 1;
          $period_str = "";
          switch(round($pt/2)){
            case 1: $period = 1; $cost = 750; $period_str = "1 месяц"; break;
            case 2: $period = 3; $cost = 1950; $period_str = "3 месяца"; break;
            case 3: $period = 6; $cost = 3450; $period_str = "6 месяцев"; break;
            case 4: $period = 12; $cost = 6000; $period_str = "1 год"; break;
          }
          $cost *= $pro_type;
          $max_date = SQLProvider::ExecuteQuery(
            "select max(date_end) m_date from tbl__pro_accounts
            where to_resident_id = $user->id and to_resident_type = '$user->type' and payed=1");
          if (is_array($max_date) && sizeof($max_date) && !IsNullOrEmpty($max_date[0]['m_date']))
            $max_date = "'".$max_date[0]['m_date']."'";
          else
            $max_date = "CURRENT_DATE()";
          SQLProvider::ExecuteNonReturnQuery(
            "delete from tbl__pro_accounts
            where to_resident_id = $user->id and to_resident_type = '$user->type' and payed=0");
          $invId = SQLProvider::ExecuteIdentityInsert(
            "insert into tbl__pro_accounts(to_resident_id, to_resident_type, pro_type, date_end, cost, period)
            values($user->id,'$user->type',$pro_type,DATE_ADD($max_date, INTERVAL $period MONTH),$cost,$period)");
          $signatureValue = md5(ROBOX_LOGIN.":$cost:$invId:".ROBOX_PASS1);
          $desc = $pro_type==2 ? "Расширенное платное" : "Платное";
          $url_params =
            "?MrchLogin=".ROBOX_LOGIN.
            "&OutSum=$cost".
            "&InvId=$invId".
            "&Desc=".urlencode("$desc размещение на $period_str").
            "&SignatureValue=$signatureValue";
          CURLHandler::Redirect(ROBOX_PAY_URL.$url_params);
        }
        else {
          $list = SQLProvider::ExecuteQuery(
            "select * from tbl__pro_accounts
            where to_resident_id = $user->id and to_resident_type = '$user->type' and payed=1
            order by date_end");
          $list_html = "";
          if (is_array($list) && sizeof($list)){
            foreach($list as &$li){
              $li['caption'] = "";
              switch($li['pro_type']){
                case 1:
                  $li['caption'] = "Платное";
                  $li['color'] = '#bce247';
                  break;
                case 2:
                  $li['caption'] = "Расширенное платное";
                  $li['color'] = '#febf01';
                  break;
              }
              $li['caption'] .= " размещение на ";
              switch($li['period']){
                case 1: $li['caption'] .= "1 месяц"; break;
                case 3: $li['caption'] .= "3 месяца"; break;
                case 6: $li['caption'] .= "6 месяцев"; break;
                case 12: $li['caption'] .= "1 год"; break;
              }
              $li['date_end'] = date("d.m.Y",strtotime($li['date_end']));
            }
            $pro_list = $this->GetControl("pro_list");
            $pro_list->dataSource = $list;
            $list_html = $pro_list->Render();  
          }
          $pro_account = $this->GetControl("pro_account");
          $pro_account->dataSource = array("list"=>$list_html, "pro_logo"=>getProLogo());
          $cab["main_area"] = "";//$pro_account->Render();
        }
        break;
    }

		$menu_render = "";

		foreach ($left_menu as $key=>$menu) {
			if (isset($menu["type"]))
			{
				if ($menu["type"] == "label")
				$menu_render .= $menu["text"]."<br>";
				else {
					$menu_render .= "<a href=/r_cabinet/data/".$key." style=\"color: ".$menu["color"].";\">";
					if ($menu["selected"])
						$menu_render .= "<span style=\"font-weight: bold; text-decoration: underline;\">".$menu["text"]."</span>";
					else
						$menu_render .= $menu["text"];
					$menu_render .= "</a><br>";
				}
			}
		}

		$cab["menu_area"] = $menu_render;

		$cabinet = $this->GetControl("cabinet");
		$cabinet->dataSource = $cab;



	}

}
?>
