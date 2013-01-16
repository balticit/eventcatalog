<?php
class u_cabinet_php extends CPageCodeHandler
{

	public $mailpath;

	public $mailtitle;

	public function u_cabinet_php()
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
		if (is_file(ROOTDIR.IMAGES_UPLOAD_DIR.$file))
		{
			unlink(ROOTDIR.IMAGES_UPLOAD_DIR.$file);
		}
		$file = "";
	}

	public function renderEditProfile()
	{
		$type = GP("type","choose");
		$user = new CSessionUser($type);
		CAuthorizer::AuthentificateUserFromCookie(&$user);
		CAuthorizer::RestoreUserFromSession(&$user);

		$user_tbl = $user->GetTable();
		$user_info = SQLProvider::ExecuteQuery("select * from $user_tbl where tbl_obj_id = $user->id");

		$account = $this->GetControl("account");
		if (IsNullOrEmpty($user_info[0]["new_user"]) || ($user_info[0]["new_user"] == 0))
			$account->key = "user_old";
		else
			$account->key = "user";
		$errors = $this->GetControl("errors");
		$errors->dataSource = array("message"=>"");
		switch ($user->type) {
			case "user":
			{
				$table = new CNativeDataTable("tbl__registered_user");
				$userData = null;

				$citySelect = new CSelect();
				$citySelect->dataSource = SQLProvider::ExecuteQuery("select * from `tbl__city` ");
				array_push($citySelect->dataSource,array("tbl_obj_id"=>-1,"title"=>"другой город..."));
				$citySelect->titleName="title";
				$citySelect->valueName="tbl_obj_id";
				$citySelect->name="properties[city]";
				$citySelect->style=array("width"=>"280px");
				$regData = "Регистрация не подтверждена";
				$utdata = array(
				"user_type_1"=>"false",
				"user_type_2"=>"false",
				"user_type_3"=>"false",
				"user_type_4"=>"false",
				"user_type_5"=>"false",
				"user_type_6"=>"false",
				"user_type_7"=>"false",
				"user_typeID_3"=>"",
				"user_typeID_4"=>"",
				"user_typeID_5"=>"",
				"user_typeID_6"=>"",
				"ut_other"=>"");

        $filter = new CAndFilter(new CEqFilter($table->fields["tbl_obj_id"],$user->id),
        new CAndFilter(new CEqFilter($table->fields["active"],1),
        new CEqFilter($table->fields["registration_confirmed"],1)));
        $userData = $table->SelectUnique($filter,false);
        $regData = $userData->registration_date;
        
        /* BALTIC IT */
        $utdata["subs2"] = '';
        if($userData->subscribe2 == '1') {$utdata["subs2"] = 'checked';}
        
        
        $show = explode('|',$userData->display_type);
        $utdata["show_title_0"] = '';
        $utdata["show_title_1"] = '';
        $utdata["show_title_2"] = '';
        if($show[0] == '0') { $utdata["show_title_0"] = "selected";}
        if($show[0] == '1') { $utdata["show_title_1"] = "selected";}
        if($show[0] == '2') { $utdata["show_title_2"] = "selected";}
        
        $utdata["show_nikname_0"] = '';
        $utdata["show_nikname_1"] = '';
        $utdata["show_nikname_2"] = '';
        if($show[1] == '0') { $utdata["show_nikname_0"] = "selected";}
        if($show[1] == '1') { $utdata["show_nikname_1"] = "selected";}
        if($show[1] == '2') { $utdata["show_nikname_2"] = "selected";}
        
        $utdata["show_skype_0"] = '';
        $utdata["show_skype_1"] = '';
        $utdata["show_skype_2"] = '';
        if($show[2] == '0') { $utdata["show_skype_0"] = "selected";}
        if($show[2] == '1') { $utdata["show_skype_1"] = "selected";}
        if($show[2] == '2') { $utdata["show_skype_2"] = "selected";}
        
        $utdata["show_icq_0"] = '';
        $utdata["show_icq_1"] = '';
        $utdata["show_icq_2"] = '';
        if($show[3] == '0') { $utdata["show_icq_0"] = "selected";}
        if($show[3] == '1') { $utdata["show_icq_1"] = "selected";}
        if($show[3] == '2') { $utdata["show_icq_2"] = "selected";}
        
        $utdata["show_birthday_0"] = '';
        $utdata["show_birthday_1"] = '';
        $utdata["show_birthday_2"] = '';
        if($show[4] == '0') { $utdata["show_birthday_0"] = "selected";}
        if($show[4] == '1') { $utdata["show_birthday_1"] = "selected";}
        if($show[4] == '2') { $utdata["show_birthday_2"] = "selected";}
        
        $utdata["show_sex_0"] = '';
        $utdata["show_sex_1"] = '';
        $utdata["show_sex_2"] = '';
        if($show[5] == '0') { $utdata["show_sex_0"] = "selected";}
        if($show[5] == '1') { $utdata["show_sex_1"] = "selected";}
        if($show[5] == '2') { $utdata["show_sex_2"] = "selected";}
        
        $utdata["show_company_0"] = '';
        $utdata["show_company_1"] = '';
        $utdata["show_company_2"] = '';
        if($show[6] == '0') { $utdata["show_company_0"] = "selected";}
        if($show[6] == '1') { $utdata["show_company_1"] = "selected";}
        if($show[6] == '2') { $utdata["show_company_2"] = "selected";}
        
        $utdata["show_position_0"] = '';
        $utdata["show_position_1"] = '';
        $utdata["show_position_2"] = '';
        if($show[7] == '0') { $utdata["show_position_0"] = "selected";}
        if($show[7] == '1') { $utdata["show_position_1"] = "selected";}
        if($show[7] == '2') { $utdata["show_position_2"] = "selected";}
        
        $utdata["show_site_0"] = '';
        $utdata["show_site_1"] = '';
        $utdata["show_site_2"] = '';
        if($show[8] == '0') { $utdata["show_site_0"] = "selected";}
        if($show[8] == '1') { $utdata["show_site_1"] = "selected";}
        if($show[8] == '2') { $utdata["show_site_2"] = "selected";}
        
        $utdata["show_address_0"] = '';
        $utdata["show_address_1"] = '';
        $utdata["show_address_2"] = '';
        if($show[9] == '0') { $utdata["show_address_0"] = "selected";}
        if($show[9] == '1') { $utdata["show_address_1"] = "selected";}
        if($show[9] == '2') { $utdata["show_address_2"] = "selected";}
        
        $utdata["show_contact_phone_0"] = '';
        $utdata["show_contact_phone_1"] = '';
        $utdata["show_contact_phone_2"] = '';
        if($show[10] == '0') { $utdata["show_contact_phone_0"] = "selected";}
        if($show[10] == '1') { $utdata["show_contact_phone_1"] = "selected";}
        if($show[10] == '2') { $utdata["show_contact_phone_2"] = "selected";}
        
        $utdata["show_company_phone_0"] = '';
        $utdata["show_company_phone_1"] = '';
        $utdata["show_company_phone_2"] = '';
        if($show[11] == '0') { $utdata["show_company_phone_0"] = "selected";}
        if($show[11] == '1') { $utdata["show_company_phone_1"] = "selected";}
        if($show[11] == '2') { $utdata["show_company_phone_2"] = "selected";}
        
        /* END BALTIC IT */

        $user_types = SQLProvider::ExecuteQuery("select * from tbl__registered_user_types where user_id = ".$user->id);
        $utdata["user_type_3_list"] = "";
        $utdata["user_type_4_list"] = "";
        $utdata["user_type_5_list"] = "";
        $utdata["user_type_6_list"] = "";
        
        foreach($user_types as $key => $ut)
        {
          switch($ut["user_type"])
          {
            case "заказчик мероприятий" :
              $utdata["user_type_1"] = true;
            break;
            case "организатор мероприятий" :
              $utdata["user_type_2"] = true;
            break;
            case "представитель подрядчика" :
              $utdata["user_type_3"] = true;
              $r_id = SQLProvider::ExecuteQuery("select GROUP_CONCAT(resident_id SEPARATOR ', ') rids from tbl__registered_user_link_resident where user_id = ".$user->id." and resident_type = 'contractor'");
              $utdata["user_typeID_3"] = $r_id[0]["rids"];

                  $ids = preg_split("/[\s,]+/", $utdata["user_typeID_3"]);
                  foreach ($ids as $num => $rid) {
                      if (!IsNullOrEmpty($rid) && !is_numeric($rid))
                          $err_ut = "не верно задан ID";
                      else {
                          $tbl = "contractor";
                          
                          $title = SQLProvider::ExecuteScalar("select title from tbl__" . $tbl . "_doc where tbl_obj_id = $rid");
                          $utdata["user_type_3_list"] .= '<div id="sl_' . $rid . '"><a href="" class="' . $tbl . '" title="Удалить" onclick="DeleteSelected(this,\'' . $tbl . '\'); return false;">' . $title . '</a></div>';
                      }
                  }
              
            break;
            case "представитель площадки" :
              $utdata["user_type_4"] = true;
              $r_id = SQLProvider::ExecuteQuery("select GROUP_CONCAT(resident_id SEPARATOR ', ') rids from tbl__registered_user_link_resident where user_id = ".$user->id." and resident_type = 'area'");
              $utdata["user_typeID_4"] = $r_id[0]["rids"];

                  $ids = preg_split("/[\s,]+/", $utdata["user_typeID_4"]);
                  foreach ($ids as $num => $rid) {
                      if (!IsNullOrEmpty($rid) && !is_numeric($rid))
                          $err_ut = "не верно задан ID";
                      else {
                          $tbl = "area";
                          
                          $title = SQLProvider::ExecuteScalar("select title from tbl__" . $tbl . "_doc where tbl_obj_id = $rid");
                          $utdata["user_type_4_list"] .= '<div id="sl_' . $rid . '"><a href="" class="' . $tbl . '" title="Удалить" onclick="DeleteSelected(this,\'' . $tbl . '\'); return false;">' . $title . '</a></div>';
                      }
                  }
              
            break;
            case "представитель артиста" :
              $utdata["user_type_5"] = true;
              $r_id = SQLProvider::ExecuteQuery("select GROUP_CONCAT(resident_id SEPARATOR ', ') rids from tbl__registered_user_link_resident where user_id = ".$user->id." and resident_type = 'artist'");
              $utdata["user_typeID_5"] = $r_id[0]["rids"];
          
                  $ids = preg_split("/[\s,]+/", $utdata["user_typeID_5"]);
                  foreach ($ids as $num => $rid) {
                      if (!IsNullOrEmpty($rid) && !is_numeric($rid))
                          $err_ut = "не верно задан ID";
                      else {
                          $tbl = "artist";
                          
                          $title = SQLProvider::ExecuteScalar("select title from tbl__" . $tbl . "_doc where tbl_obj_id = $rid");
                          $utdata["user_type_5_list"] .= '<div id="sl_' . $rid . '"><a href="" class="' . $tbl . '" title="Удалить" onclick="DeleteSelected(this,\'' . $tbl . '\'); return false;">' . $title . '</a></div>';
                      }
                  }
              
            break;
            case "представитель агентства" :
              $utdata["user_type_6"] = true;
              $r_id = SQLProvider::ExecuteQuery("select GROUP_CONCAT(resident_id SEPARATOR ', ') rids from tbl__registered_user_link_resident where user_id = ".$user->id." and resident_type = 'agency'");
              $utdata["user_typeID_6"] = $r_id[0]["rids"];

                  $ids = preg_split("/[\s,]+/", $utdata["user_typeID_6"]);
                  foreach ($ids as $num => $rid) {
                      if (!IsNullOrEmpty($rid) && !is_numeric($rid))
                          $err_ut = "не верно задан ID";
                      else {
                          $tbl = "agency";
                          
                          $title = SQLProvider::ExecuteScalar("select title from tbl__" . $tbl . "_doc where tbl_obj_id = $rid");
                          $utdata["user_type_6_list"] .= '<div id="sl_' . $rid . '"><a href="" class="' . $tbl . '" title="Удалить" onclick="DeleteSelected(this,\'' . $tbl . '\'); return false;">' . $title . '</a></div>';
                      }
                  }
              
            break;
            default :
              $utdata["user_type_7"] = true;
              $utdata["ut_other"] = $ut["user_type"];
          }
        }

				if ($this->IsPostBack)
				{
					$utdata = array(
								"user_type_1"=>"false",
								"user_type_2"=>"false",
								"user_type_3"=>"false",
								"user_type_4"=>"false",
								"user_type_5"=>"false",
								"user_type_6"=>"false",
								"user_type_7"=>"false",
								"user_typeID_3"=>"",
								"user_typeID_4"=>"",
								"user_typeID_5"=>"",
								"user_typeID_6"=>"",
								"ut_other"=>"");

					$props = GP("properties");
					if (is_array($props))
					{
						if (IsNullOrEmpty(@$props["subscribe"])) {$props["subscribe"] = 0;}
						if (IsNullOrEmpty(@$props["subscribe2"])) {$props["subscribe2"] = 0;}
						$userValidator = $this->GetControl("userValidator");
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

						$citySelect->selectedValue = $props["city"];

						$user_types = GP("user_type");
						$user_typesIDs = GP("user_typeID");
						
						
						foreach($user_types as $key=>$val)
						{
							$err_ut = "";
							$utdata["user_type_".$key] = true;
							if ($key > 2 && $key < 7)
							{
								$utdata["user_typeID_".$key] = $user_typesIDs[$key];
								
							}
							if ($key == 7)
							{
								$utdata["ut_other"] = $user_typesIDs[$key];
							}

							if ($key > 2 && $key < 7)
							{
								if (IsNullOrEmpty($user_typesIDs[$key]))
								{
									$err_ut = "не задан ID ";
								}
								else
								{
									$ids = preg_split("/[\s,]+/",$user_typesIDs[$key]);
									foreach($ids as $num => $rid)
									{
									   if (!IsNullOrEmpty($rid) && !is_numeric($rid)) $err_ut = "не верно задан ID";
									}
								}
								if (!IsNullOrEmpty($err_ut))
								{
									switch($key)
									{
										case 3 : $err_ut .= " подрядчика"; break;
										case 4 : $err_ut .= " площадки"; break;
										case 5 : $err_ut .= " артиста"; break;
										case 6 : $err_ut .= " агентства"; break;
									}
								}
							}
							elseif ($key == 7 && IsNullOrEmpty($user_typesIDs[$key]))
							{
								$err_ut = "не задан другой тип пользователя";
							}
							if (!IsNullOrEmpty($err_ut))
								array_push($errorsData,$err_ut);
						}
						

						
						
						
						$userData->FromHashMap($props);
						if (IsNullOrEmpty($userData->nikname)) $userData->nikname=null;
						$userData->forum_name = IsNullOrEmpty($userData->nikname)?$userData->login:$userData->nikname;

            $flogo = $_FILES["properties"];
						if (is_array($flogo)){
							$logo = $this->CreateLogo($userData->login,$flogo,"logo");
							if (!is_null($logo))
								$userData->logo = $logo;
						}

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

              if (isset($props["logo_clean"]) && $props["logo_clean"]=="clean")
              {
                $this->CleanLogo($userData->logo);
                $userData->logo = "";
              }
              $userData->edit_date = time();
              

						
						  $userData->subscribe = $props["subscribe"];
						  $userData->subscribe2 = $props["subscribe2"];
               
              $userData->display_type = $props["display_type"][0].
              '|'.$props["display_type"][1].
              '|'.$props["display_type"][2].
              '|'.$props["display_type"][3].
              '|'.$props["display_type"][4].
              '|'.$props["display_type"][5].
              '|'.$props["display_type"][6].
              '|'.$props["display_type"][7].
              '|'.$props["display_type"][8].
              '|'.$props["display_type"][9].
              '|'.$props["display_type"][10].
              '|'.$props["display_type"][11];
              
              //echo $userData->display_type;
              //die();
              
              $table->UpdateObject(&$userData);
              $user->name = $userData->title;
              $user->Save();

              SQLProvider::ExecuteNonReturnQuery("delete from tbl__registered_user_types where user_id = ".$user->id);
              SQLProvider::ExecuteNonReturnQuery("delete from tbl__registered_user_link_resident where user_id = ".$user->id);
              foreach($user_types as $key)
              {
                $resident_type = "";
                switch($key)
                {
                  case 3 : $resident_type = "contractor"; break;
                  case 4 : $resident_type = "area"; break;
                  case 5 : $resident_type = "artist"; break;
                  case 6 : $resident_type = "agency"; break;
                }
                if (!IsNullOrEmpty($resident_type))
                {
                  $ids = preg_split("/[\s,]+/",$user_typesIDs[$key]);
                  foreach ($ids as $num => $r_id)
                  {
                    if (is_numeric($r_id))
                    SQLProvider::ExecuteNonReturnQuery("insert into tbl__registered_user_link_resident(user_id,resident_type,resident_id)
                                    values (".$user->id.", '".$resident_type."', ".intval($r_id).")");
                  }

                }
                $ut = "";
                switch ($key)
                {
                  case 1 : $ut = "заказчик мероприятий"; break;
                  case 2 : $ut = "организатор мероприятий"; break;
                  case 3 : $ut = "представитель подрядчика"; break;
                  case 4 : $ut = "представитель площадки"; break;
                  case 5 : $ut = "представитель артиста"; break;
                  case 6 : $ut = "представитель агентства"; break;
                  case 7 : $ut = $user_typesIDs[$key]; break;
                }
                SQLProvider::ExecuteNonReturnQuery("insert into tbl__registered_user_types(user_id,user_type)
                                    values (".$user->id.", '".$ut."')");
              }
              CURLHandler::Redirect(GP("target","/u_cabinet"));
						}
					}
				}
				$logos = preg_split("/\//",$userData->logo);
				$ls = sizeof($logos);

				$c_id = md5(uniqid(rand(), true));
				$udata = array("login_readonly"=>$user->authorized?"readonly=\"true\"":"",
				"submit_text"=>$this->GetMessage($user->authorized?"save":"reg"),
				"city_list"=>$citySelect->RenderHTML(),
				"regdate"=>$regData);


				$userData->logo = GetFilename($userData->logo);
				$account->dataSource = array_merge($udata, $userData->GetData(),$utdata);
			}
			break;
		}

		return ($errors->renderHTML().$account->renderHTML());
	}

	public function PreRender() {


		$user = new CSessionUser('user');
		CAuthorizer::AuthentificateUserFromCookie(&$user);
		CAuthorizer::RestoreUserFromSession(&$user);
		if (!$user->authorized) CURLHandler::Redirect("/authorizationrequired?target=".$_SERVER['REQUEST_URI']);

    if($user->type !== 'user'){
      CURLHandler::Redirect("/r_cabinet".CURLHandler::BuildQueryParams($_GET));
    }

		$type = GP("data","personal");

		$cab = array();
		$u_cab_menu = $this->GetControl("u_cab_menu");
		$u_cab_menu->activeItem = "/u_cabinet/data/$type";
		$uid = $user->type.$user->id;
		$new_count = SQLProvider::ExecuteScalar("select count(*) as quan from tbl__messages m
												left join tbl__black_list bl on (reciever_id=bl.user_id and sender_id=blocked_id) and bl.user_id='$uid'
												where blocked_id is null and `status`='sent' and reciever_id='$uid'");
		if ($new_count > 0)
			$u_cab_menu->dataSource["/u_cabinet/data/my_messages"]["title"] .= "&nbsp;($new_count)";


		$user_tbl = $user->GetTable();
		$user_info = SQLProvider::ExecuteQuery("select * from $user_tbl where tbl_obj_id = $user->id");
		if (sizeof($user_info)>0) $user_info = $user_info[0];
		if (!IsNullOrEmpty($user_info["city"])) $user_info["sity"] = SQLProvider::ExecuteScalar("select title from tbl__city where tbl_obj_id = ".$user_info["city"]);
		$user_types = SQLProvider::ExecuteQuery( "select * from `tbl__registered_user_types` where user_id = ".$user->id);
		$cab["user_types"] = "";
		foreach ($user_types as $key=>$user_type)
		{
			$cab["user_types"] .= $user_type["user_type"];
			$user_links = array();
			switch($user_type["user_type"])
			{
				case "представитель подрядчика" :
					$user_links = SQLProvider::ExecuteQuery("select '#f05620' as typecolor, tr.resident_type, t.* from
					                                         `tbl__registered_user_link_resident` tr
															 left join tbl__contractor_doc t on t.tbl_obj_id = tr.resident_id
															 where tr.user_id = ".$user->id." and tr.resident_type = 'contractor'");
				break;
				case "представитель площадки" :
					$user_links = SQLProvider::ExecuteQuery("select '#3399ff' as typecolor, tr.resident_type, t.* from
					                                         `tbl__registered_user_link_resident` tr
															 left join tbl__area_doc t on t.tbl_obj_id = tr.resident_id
															 where tr.user_id = ".$user->id." and tr.resident_type = 'area'");
				break;
				case "представитель артиста" :
					$user_links = SQLProvider::ExecuteQuery("select '#ff0066' as typecolor, tr.resident_type, t.* from
					                                         `tbl__registered_user_link_resident` tr
															 left join tbl__artist_doc t on t.tbl_obj_id = tr.resident_id
															 where tr.user_id = ".$user->id." and tr.resident_type = 'artist'");
				break;
				case "представитель агентства" :
					$user_links = SQLProvider::ExecuteQuery("select '#99cc00' as typecolor, tr.resident_type, t.* from
					                                         `tbl__registered_user_link_resident` tr
															 left join tbl__agency_doc t on t.tbl_obj_id = tr.resident_id
															 where tr.user_id = ".$user->id." and tr.resident_type = 'agency'");
				break;
			}
			foreach ($user_links as $key=>$user_link)
			{
				$cab["user_types"] .="&nbsp;&nbsp;<a href=\"/".$user_link["resident_type"]."/details/id/".$user_link["tbl_obj_id"]."\"
							  style=\"color:".$user_link["typecolor"]."\">".$user_link["title"]."</a>";
			}

			$cab["user_types"] .="<br />";
		}
		$cab["user_name"] = $user_info["title"];
		if (IsNullOrEmpty($user_info["logo"]))
		  $cab["logo"] = "/images/nologo.png";
		else
		  $cab["logo"] = "/upload/".GetFileName($user_info["logo"]);
		
		
		
    $cab["edit_link_href"] = "";
		$cab["edit_link_title"] = "";
    
		$cab["see_my_profile"] = '';
		if(!isset($_GET['see'])) {
		
		$cab["edit_link_href"] = "/u_cabinet/data/personal/action/edit";
		$cab["edit_link_title"] = "редактировать профиль";
		
		$cab["see_my_profile"] = '<span>Как мой профиль видят другие:
      <a href="/u_cabinet?see=all" target="_blank" style="text-decoration:underline;">все пользователи</a>&nbsp;|&nbsp;
      <a href="/u_cabinet?see=auth" target="_blank" style="text-decoration:underline;">авторизованные</a>
      </span>';
		}
		

		$en_month = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
		$ru_month = array("января","февраля","марта","апреля","мая","июня","июля","августа","сентября","октября","ноября","декабря");
		$cab["show_info"] = "none";
		$left_menu = array();
//----------------------------------------------------------------------------
		switch ($type) {
			case "personal" :
			$cab["show_info"] = "table-row";
			$p_action = GP("action","");
			if ($p_action == "edit")
			{
				$cab["edit_link_href"] = GP("target","/u_cabinet/data/personal");
				$cab["edit_link_title"] = "вернуться назад";
				$cab["main_area"] = $this->renderEditProfile();
			}
			else
			{
			
			
			$cab["main_area"] = '<div class="u_info_block">';
			if ($user_info["sity"] != '') $cab["main_area"] .= "<b>Город:</b> ".$user_info["sity"]."<br />";
			
			 if(isset($_GET['see'])) {
			 
          $show = explode('|',$user_info["display_type"]);
            

            
          if($_GET['see'] == 'all') {
          

            if ($show[10]== '0') {
              $cab["main_area"] .= "<b>Мобильный телефон:</b> ".$user_info["contact_phone"]."<br />";
            }
            if ($show[10]== '1') {
              $cab["main_area"] .= '<b>Мобильный телефон:</b> *******<br />';
            }
          
            
            if ($user_info["skype"] != '' && $user_info["skype"] != '0') {
              if ($show[2]== '0') {
                $cab["main_area"] .= "<b>Skype:</b> ".$user_info["skype"]."<br />";
              }
              if ($show[2]== '1') {
                $cab["main_area"] .= '<b>Skype:</b> *******<br />';
              }
            }
            
            if ($user_info["icq"] != '' && $user_info["icq"] != '0') {
              if ($show[3]== '0') {
                $cab["main_area"] .= "<b>ICQ:</b> ".$user_info["icq"]."<br />";
              }
              if ($show[3]== '1') {
                $cab["main_area"] .= '<b>ICQ:</b> *******<br />';
              }
            }
            
            if ($user_info["company_phone"] != '') {
              if ($show[11]== '0') {
                $cab["main_area"] .= "<b>Рабочий телефон:</b> ".$user_info["company_phone"]."<br />";
              }
              if ($show[11]== '1') {
                $cab["main_area"] .= '<b>Рабочий телефон:</b> *******<br />';
              }
            }

            if ($user_info["site"] != '') {
              if ($show[8]== '0') {
                $cab["main_area"] .= "<b>Сайт компании:</b> <a target='_blank' href='http://".$user_info["site"]."'>".$user_info["site"]."</a><br /><br />";
              }
              if ($show[8]== '1') {
                $cab["main_area"] .='<b>Сайт компании:</b> *******<br /><br />';
              }
            }
    
            if ($user_info["registration_date"] != '') {
            $cab["main_area"] .= "<b>На сайте:</b> ".onSiteTime($user_info["registration_date"])."<br />";
            }
            
            if ($user_info["birthday"] != '' && $user_info["birthday"] != '0000-00-00') {
              if ( $show[4]== '0') {
                $cab["main_area"] .= "<b>Возраст:</b> ".UserAge($user_info["birthday"])."<br />";
              }
              if ($show[4]== '1') {
                $cab["main_area"] .= '<b>Возраст:</b> *******<br />';
              }
            }
            
            if ($user_info["sex"] != '') {
              if ($show[5]== '0') {
          			if ($user_info["sex"] != '0') { $sex = "Женский"; } else { $sex = "Мужской";}
                if ($user_info["sex"] != '') $cab["main_area"] .= "<b>Пол:</b> ".$sex."<br />";
              }
              if ($show[5]== '1') {
                  $cab["main_area"] .= '<b>Пол:</b> *******<br />';
              }
            }
    
            if (!IsNullOrEmpty($user_info["company"])) {
              if ($show[6]== '0') {
        				$cab["main_area"] .= "<b>Компания:</b> ".$user_info["company"]."<br />";
        			}
        			if ($show[6]== '1') {
                $cab["main_area"] .= '<b>Компания:</b> *******<br />';
              }
            }
            
            if ($user_info["position"] != '') {
              if ($show[7]== '0') {
                $cab["main_area"] .= "<b>Должность:</b> ".$user_info["position"]."<br />";
              }
              if ($show[7]== '1') {
                $cab["main_area"] .= '<b>Должность:</b> *******<br />';
              }
            }
            
            if ($user_info["address"] != '') {
              if ($show[9]== '0') {
                $cab["main_area"] .= "<b>Адрес:</b> ".$user_info["address"]."<br />";
              }
              if ($show[9]== '1') {
                $cab["main_area"] .= '<b>Адрес:</b> *******<br />';
              }
            }
            
            
            
            
          
          }
          if($_GET['see'] =='auth') {
          
          
          
            if ($show[10]== '0' or $show[10]== '1') {
              $cab["main_area"] .= "<b>Мобильный телефон:</b> ".$user_info["contact_phone"]."<br />";
            }

          
            
            if ($user_info["skype"] != '' && $user_info["skype"] != '0') {
              if ($show[2]== '0' or $show[2]== '1') {
                $cab["main_area"] .= "<b>Skype:</b> ".$user_info["skype"]."<br />";
              }

            }
            
            if ($user_info["icq"] != '' && $user_info["icq"] != '0') {
              if ($show[3]== '0' or $show[3]== '1') {
                $cab["main_area"] .= "<b>ICQ:</b> ".$user_info["icq"]."<br />";
              }

            }
            
            if ($user_info["company_phone"] != '') {
              if ($show[11]== '0' or $show[11]== '1') {
                $cab["main_area"] .= "<b>Рабочий телефон:</b> ".$user_info["company_phone"]."<br />";
              }

            }

            if ($user_info["site"] != '') {
              if ($show[8]== '0' or $show[8]== '1') {
                $cab["main_area"] .= "<b>Сайт компании:</b> <a target='_blank' href='http://".$user_info["site"]."'>".$user_info["site"]."</a><br /><br />";
              }

            }
    
            if ($user_info["registration_date"] != '') {
            $cab["main_area"] .= "<b>На сайте:</b> ".onSiteTime($user_info["registration_date"])."<br />";
            }
            
            if ($user_info["birthday"] != '' && $user_info["birthday"] != '0000-00-00') {
              if ( $show[4]== '0' or $show[4]== '1') {
                $cab["main_area"] .= "<b>Возраст:</b> ".UserAge($user_info["birthday"])."<br />";
              }

            }
            
            if ($user_info["sex"] != '') {
              if ($show[5]== '0' or $show[5]== '1') {
          			if ($user_info["sex"] != '0') { $sex = "Женский"; } else { $sex = "Мужской";}
                if ($user_info["sex"] != '') $cab["main_area"] .= "<b>Пол:</b> ".$sex."<br />";
              }

            }
    
            if (!IsNullOrEmpty($user_info["company"])) {
              if ($show[6]== '0' or $show[6]== '1') {
        				$cab["main_area"] .= "<b>Компания:</b> ".$user_info["company"]."<br />";
        			}

            }
            
            if ($user_info["position"] != '') {
              if ($show[7]== '0' or $show[7]== '1') {
                $cab["main_area"] .= "<b>Должность:</b> ".$user_info["position"]."<br />";
              }

            }
            
            if ($user_info["address"] != '') {
              if ($show[9]== '0' or $show[9]== '1') {
                $cab["main_area"] .= "<b>Адрес:</b> ".$user_info["address"]."<br />";
              }

            }
          
          
          
          
          }
       }
			
			
        
        else {
			  
				if ($user_info["contact_phone"] != '') $cab["main_area"] .= "<b>Мобильный телефон:</b> ".$user_info["contact_phone"]."<br />";
				
				if ($user_info["skype"] != '') $cab["main_area"] .= "<b>Skype:</b> ".$user_info["skype"]."<br />";
				if ($user_info["icq"] != '' && $user_info["icq"] != '0') $cab["main_area"] .= "<b>ICQ:</b> ".$user_info["icq"]."<br />";
				
				
				if ($user_info["company_phone"] != '') $cab["main_area"] .= "<b>Рабочий телефон:</b> ".$user_info["company_phone"]."<br />";
				if ($user_info["email"] != '') $cab["main_area"] .= "<b>Электронная почта:</b> <a href='mailto:".$user_info["email"]."'>".$user_info["email"]."</a><br />";
				if ($user_info["site"] != '') $cab["main_area"] .= "<b>Сайт компании:</b> <a target='_blank' href='http://".$user_info["site"]."'>".$user_info["site"]."</a><br /><br />";


        if ($user_info["registration_date"] != '') $cab["main_area"] .= "<b>На сайте:</b> ".onSiteTime($user_info["registration_date"])."<br />";
        if ($user_info["birthday"] != '') $cab["main_area"] .= "<b>Возраст:</b> ".UserAge($user_info["birthday"])."<br />";
        
     
        if ($user_info["sex"] != '0') { $sex = "Женский"; } else { $sex = "Мужской";}
        if ($user_info["sex"] != '') $cab["main_area"] .= "<b>Пол:</b> ".$sex."<br />";
        
		    if (!IsNullOrEmpty($user_info["company"])) {
					$cab["main_area"] .= "<b>Компания:</b> ".$user_info["company"]."<br />";
				}
				if ($user_info["position"] != '') $cab["main_area"] .= "<b>Должность:</b> ".$user_info["position"]."<br />";
				if ($user_info["address"] != '') $cab["main_area"] .= "<b>Адрес:</b> ".$user_info["address"]."<br />";
				}
				
        $cab["main_area"] .= '</div>';
			}
			break;
			case "my_favorite" :
				$f_type = GP("type","all");
				$left_menu = array(
				  "my_favorite/type/all" => array(
						"type" => "link",
						"text" => "Все",
						"color" => "#ccc",
						"selected" => false),
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
						
						$left_menu["my_favorite/type/$f_type"]["selected"] = true;
				
			//	if ($f_type != "all")
			//		$left_menu["my_favorite/type/$f_type"]["selected"] = true;

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
					header("location: /u_cabinet/data/my_favorite/type/$f_type");
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
					$favorites[$i]["del_link"] = "/u_cabinet/data/my_favorite/type/$f_type/del/".$favorites[$i]["tbl_obj_id"];
					if ($favorites[$i]["date_add"] != '')
						$favorites[$i]["date_add"] = str_ireplace($en_month,$ru_month,date("d M Y",strtotime($favorites[$i]["date_add"])));
				}
				$favorite_block = $this->GetControl("favorite");
				$favorite_block->dataSource = $favorites;

				$cab["main_area"] = "<form id='fav_del_form' action=\"\"><table cellspacing=\"0\" cellpadding=\"10\" width=\"600\">
				                     <tr style=\"color: #999999; font-weight: bold;\">
									 <td style=\"border-bottom: 1px solid #DCDCDC;\" nowrap=\"nowrap\">Название резидента</td>
									 <td style=\"border-bottom: 1px solid #DCDCDC;\" nowrap=\"nowrap\" width=\"150\">Дата добавления</td>
									 "/*<td style=\"border-bottom: 1px solid #DCDCDC;\" nowrap=\"nowrap\" width=\"70\">[<a href=\"\" id=\"btn_fav_del\">Удалить все</a>]</td></tr>*/."
									 <td style=\"border-bottom: 1px solid #DCDCDC; text-align:center\" nowrap=\"nowrap\" width=\"70\">Удалить</td></tr>
									 ".$favorite_block->renderHTML()."
									 <tr><td></td><td></td>
									 <td>
										<input type=\"hidden\" name=\"fav_del_sel\" value=\"1\">
										<input type=\"hidden\" name=\"data\" value=\"my_favorite\">
									</td>
									 </tr></table></form>
									 
<div id=\"dialog-confirm\" title=\"Удалить?\">
  <p></p>
</div>
									 
<script type=\"text/javascript\">
$(function() {

	\$('.fav_del_cross').click( function() {
	   
	   var itemText = 'Вы уверены что хотите удалить '+\$(this).parents('tr').find('a').html()+' из избранного?';
	   var checkItem = \$(this).next();
	   
		\$( '#dialog-confirm' ).dialog({
			resizable: false,
			height:160,
			width:400,
			dialogClass: 'dialog-confirm',
			modal: false,
  		position: 'center',
			open: function(event, ui) { 
          \$(this).find('p').html(itemText);
       },
			buttons: {
				'Да': function() {
				  checkItem.attr('checked','checked');
					\$('#fav_del_form').submit();
					\$( this ).dialog( 'close' );
				},
				'Нет': function() {
					\$( this ).dialog( 'close' );
				}
			}
		});
		
	});
		
});
</script>
                   
                   
                   
                   <script type=\"text/javascript\">
                   /*
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
										*/
									</script>
                  
                  ";
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
				  "my_marks/type/all" => array(
						"type" => "link",
						"text" => "Все",
						"color" => "#cccccc",
						"selected" => false),
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
								tbl__userlike.from_resident_type='user' and
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
								tbl__userlike.from_resident_type='user' and
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
								tbl__userlike.from_resident_type='user' and
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
								tbl__userlike.from_resident_type='user' and
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
								tbl__userlike.from_resident_type='user' and
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
								tbl__userlike.from_resident_type='user' and
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
								tbl__userlike.from_resident_type='user' and
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
								tbl__userlike.from_resident_type='user' and
								tbl__userlike.from_resident_id=".$user->id." order by 2
						)");
					break;
				}

			foreach ($marks as $key=>$mark)
			{
				if ($marks[$key]["date"] != '')
						$marks[$key]["date"] = str_ireplace($en_month,$ru_month,date("d M Y",strtotime($marks[$key]["date"])));
			}

			$marks_block = $this->GetControl("marks");
			$marks_block->dataSource = $marks;

			$cab["main_area"] = "<form id=\"fav_del_form\" method=\"post\"><table cellspacing=\"0\" cellpadding=\"10\" width=\"600\">
				                     <tr style=\"color: #999999; font-weight: bold;\">
									 <td style=\"border-bottom: 1px solid #DCDCDC;\" nowrap=\"nowrap\">Название резидента</td>
									 <td style=\"border-bottom: 1px solid #DCDCDC;\" nowrap=\"nowrap\" width=\"150\">Дата</td>
									 <td style=\"border-bottom: 1px solid #DCDCDC; text-align:center\" nowrap=\"nowrap\" width=\"70\">Удалить</td>
									 </tr>
									 ".$marks_block->renderHTML()."
                   <tr>
                   <td colspan=\"2\">&nbsp;</td>
                   <td><input type=\"hidden\" value=\"Удалить\"></td></tr></table></form>

<div id=\"dialog-confirm\" title=\"Удалить?\">
  <p></p>
</div>
             
<script type=\"text/javascript\">
$(function() {

	\$('.fav_del_cross').click( function() {
	   
	   var itemText = 'Вы уверены что хотите удалить '+\$(this).parents('tr').find('a').html()+' из рекомендаций?';
	   var checkItem = \$(this).next();
	   
		\$( '#dialog-confirm' ).dialog({
			resizable: false,
			height:160,
			width:400,
			dialogClass: 'dialog-confirm',
			modal: false,
  		position: 'center',
			open: function(event, ui) { 
          \$(this).find('p').html(itemText);
       },
			buttons: {
				'Да': function() {
				  checkItem.attr('checked','checked');
					\$('#fav_del_form').submit();
					\$( this ).dialog( 'close' );
				},
				'Нет': function() {
					\$( this ).dialog( 'close' );
				}
			}
		});
		
	});
		
});
</script>
                   
                   <script type=\"text/javascript\">
                   /*
										var del_selected = false;
										\$('#btn_fav_del').click(function(){
											if(confirm('Вы уверены что хотите удалить все из рекомендаций?')) {
												\$('.fav_del').attr('checked','checked');
												\$('#fav_del_form').submit();
											}
										return false;});
										\$('.fav_del_cross').click( function() {
											if(confirm('Вы уверены что хотите удалить '+\$(this).parents('tr').find('a').html()+' из рекомендаций?')) {
												\$(this).next().attr('checked','checked');
												\$('#fav_del_form').submit();
											}
										});
										*/
									</script>
                   ";

			break;
//===================================================================================================================================================
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
					"selected" => false)
			/*	"my_messages/action/blacklist" => array(
					"type" => "link",
					"text" => "Черный список",
					"color" => "#000",
					"selected" => false) */
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
			$reply_mess;
			if ($m_action=="compose"||$m_action=="reply"||$m_action=="select"||$m_action=="delete"||$m_action=="sent"||$m_action=="view"||$m_action=="block"||$m_action=="unblock"||$m_action=="error_sent")
			{
				$reciever_id=0;
				$reciever_type;
				$reciever_data;
				if ($m_action=="compose"||$m_action=="block"||$m_action=="unblock")
				{
					$reciever_id = GP("id",0);
					$reciever_type = GP("type");
					if ($m_action=="block")
					{
						SQLProvider::ExecuteNonReturnQuery("replace into tbl__black_list values('$uid','$reciever_type$reciever_id')");
						CURLHandler::Redirect("/u_cabinet/data/my_messages/");
					}
					elseif ($m_action=="unblock")
					{
						SQLProvider::ExecuteNonReturnQuery("delete from tbl__black_list where user_id='$uid' and blocked_id='$reciever_type$reciever_id'");
						CURLHandler::Redirect("/u_cabinet/data/my_messages/");
					}
				}
				elseif($m_action=="reply"||$m_action=="delete"||$m_action=="view")
				{
					$reply_id = GPT("rid");
					$r_mess = SQLProvider::ExecuteQuery("select * from tbl__messages where tbl_obj_id=$reply_id");
					
					/*BALTIC IT*/
					$h_sender = $r_mess[0]["sender_id"];
					$h_reciever = $r_mess[0]["reciever_id"];

					$h_mess = SQLProvider::ExecuteQuery("select * from tbl__messages where reciever_id = '$h_reciever' AND sender_id = '$h_sender' AND tbl_obj_id<'$reply_id' OR reciever_id = '$h_sender' AND sender_id = '$h_reciever' AND tbl_obj_id<='$reply_id' ORDER BY time DESC LIMIT 10 ");
					/* 
          echo '<pre>';
					var_dump($h_mess);
					echo '</pre>';
					*/
					/*END BALTIC IT*/
					
					if (sizeof($r_mess)==0)
					{
						CURLHandler::Redirect("/u_cabinet/data/my_messages/");
					}
					if ($r_mess[0]["status"]!="read"&&$m_action!="view")
					{
						SQLProvider::ExecuteNonReturnQuery("update tbl__messages set status='read', time_read = NOW() where tbl_obj_id=$reply_id");
					}
					
					//////////////
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
					"selected" => false)
			/*	"my_messages/action/blacklist" => array(
					"type" => "link",
					"text" => "Черный список",
					"color" => "#000",
					"selected" => false) */
				);
					$new_count = SQLProvider::ExecuteScalar("select count(*) as quan from tbl__messages m
																left join tbl__black_list bl on (reciever_id=bl.user_id and sender_id=blocked_id) and bl.user_id='$uid'
															where blocked_id is null and `status`='sent' and reciever_id='$uid'");
    			if ($new_count>0)
    			{
    			  $u_cab_menu->dataSource["/u_cabinet/data/my_messages"]["title"] = "Мои сообщения&nbsp;($new_count)";
    				$left_menu["my_messages/action/inbox"]["text"] .= "&nbsp;($new_count)";
    			}
          else { 
            $u_cab_menu->dataSource["/u_cabinet/data/my_messages"]["title"] = "Мои сообщения&nbsp;";
            $left_menu["my_messages/action/inbox"]["text"] .= "&nbsp;";
          }
    			$left_menu["my_messages/action/$m_action"]["selected"] = true;
					////////////
					
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
						CURLHandler::Redirect("/u_cabinet/data/my_messages/");
					}

				}
				elseif ($m_action=="sent" || $m_action=="error_sent")
				{
					$is_sent = true;
				}

				if (!$is_sent && $m_action!="select")
				{
					$reciever_data = SQLProvider::ExecuteQuery("select * from vw__all_users_full where user_id=$reciever_id and `type`='$reciever_type'");
					if (sizeof($reciever_data)==0)
					{
						CURLHandler::Redirect("/u_cabinet/data/my_messages/action/select/");
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
					if ($this->IsPostBack && $m_action != "view")
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
						$mailpath = "/pagecode/settings/u_cabinet/msg_mail.htm";
						$mailtitle = $sender_name." написал Вам сообщение на портале eventcatalog.ru";
						$app = CApplicationContext::GetInstance();
						$title = iconv($app->appEncoding,"utf-8",$mailtitle);
						$mailData["to_name"] = iconv($app->appEncoding,"utf-8",$res_info["title"]);
						$mailData["from_name"] = iconv($app->appEncoding,"utf-8",$sender_name);
						$mailData["link"] = "http://".$_SERVER['HTTP_HOST']."/u_cabinet/data/my_messages/action/reply/rid/".$newMessage->tbl_obj_id;
						$mbody = CStringFormatter::Format(file_get_contents(RealFile($mailpath)),$mailData);
						SendHTMLMail($res_info["email"],$mbody,$title,"noreply@eventcatalog.ru","noreply@eventcatalog.ru");

						CURLHandler::Redirect("/u_cabinet/data/my_messages/action/sent/");
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
						$message_user_list = SQLProvider::ExecuteQuery("select * from vw__all_users_full where
																			user_key in(
																			select distinct sender_id from tbl__message_users_list ml
																				left join tbl__black_list bl on bl.user_id=ml.user_id and ml.sender_id=bl.blocked_id
																			where ml.user_id='$uid' and blocked_id is null)");
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
											document.getElementById("compose_form").action = "/u_cabinet/data/my_messages/action/compose/type/"+sen_data[0]+"/id/"+sen_data[1]+"/";
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
												<td style="width:80px;height:40px;" align="center"><div style="width:60px; height:40px; border: 1px solid #D5D5D5;"><img border="0" height="40" width="60" src="'.($reciever_data["logo"]==''?"/images/nologo.png":"/upload/".$reciever_data["logo"]).'"/></div></td>
												<td valign="middle">
													<a style="font-size:16px; color:#0063AF; font-weight:bold;" href="/profile/'.$reciever_type.'/'.$reciever_id.'">'.$reciever_data["title"].'</a>
													' /* ($m_action=="compose"?"":'&nbsp;&nbsp;<a onClick="return confirm(\'Вы действительно хотите добавить данного адресата в черный список?\');" href="/u_cabinet/data/my_messages/action/block/type/'.$reciever_type.'/id/'.$reciever_id.'" style="color:#888888; font-size:10px; text-decoration:underline;">в черный список</a>  
													<br/>
													<span style="color:#999999; font-size:11px;">'.str_ireplace($en_month,$ru_month,date("d M Y H:i",strtotime($reply_mess["time"]))).'</span>').'
                          */.'
                          '.($m_action=="compose"?"":'<br/><span style="color:#999999; font-size:11px;">'.str_ireplace($en_month,$ru_month,date("d M Y H:i",strtotime($reply_mess["time"]))).'</span>').'
                          
												</td>
											</tr>
											<tr><td>&nbsp;</td></tr>
										</table>';

						if ($m_action=="reply"||$m_action=="view")
						{
							//$cab["main_area"].='<div style="color:#333333; background-color:#EEEEEE; padding:8px; margin:0 0 12px; -moz-border-radius: 6px 6px 6px 6px;">'.ProcessMessage($reply_mess["text"])."</div>";
							
							
							
							if ($m_action=="reply")
							{
								$cab["main_area"].='<p>Ваш ответ:</p>';
							}

						}
						
						/*
            
          
            
            */
						
						if ($m_action=="reply"||$m_action=="compose")
						{
						
					
						
						
						
						
						  if($m_action=="compose") {
    						$reciever_id = $_GET["type"].GPT("id");
  
      					$r_mess = SQLProvider::ExecuteQuery("select * from tbl__messages where reciever_id='$reciever_id'  ");
  
      					$h_sender = $r_mess[0]["sender_id"];
      					$h_reciever = $r_mess[0]["reciever_id"];
      
      					$h_mess = SQLProvider::ExecuteQuery("select * from tbl__messages where reciever_id = '$h_reciever' AND sender_id = '$h_sender' OR reciever_id = '$h_sender' AND sender_id = '$h_reciever' ORDER BY time DESC LIMIT 10 ");
    					}
						
						
						
							$cab["main_area"].='<form method="post">
										<textarea name="message_text" style="width:100%; height:100px; border:1px solid #999999; font-size:12px; -moz-border-radius: 6px 6px 6px 6px;"></textarea><br/><br/>
										<input type="submit" value="Отправить"/><br/><br/><br/>
										</form>';
										
							$cab["main_area"].='<div class="message_history">';
							$cab["main_area"].='<div class="header_message_history"><b>История сообщений</b></div>';
							
							if($m_action=="reply") {
							$cab["main_area"].='<div class="grey_message message"><span style="float:right">'.$reply_mess["time"].'</span> <span style="margin-right:140px">'.ProcessMessage($reply_mess["text"])." </span></div>";
							}
							
  						foreach($h_mess as $mess) {
  						  if( $h_sender == $mess['sender_id']) { $class="grey_message"; } else { $class="white_message";}
                  $cab["main_area"].='<div class="'.$class.' message"><span style="float:right">'.$reply_mess["time"].'</span>  <span style="margin-right:140px">'.$mess["text"].' </span></div>';
              }
              $cab["main_area"].='</div>';
										
						}
						elseif ($m_action=="view")
						{
						  $cab["main_area"].='<div style="color:#333333; background-color:#EEEEEE; padding:8px; margin:0 0 12px; -moz-border-radius: 6px 6px 6px 6px;"><span style="float:right">'.$reply_mess["time"].'</span><span style="margin-right:140px">'.ProcessMessage($reply_mess["text"])."<span> </div>";
						
							$cab["main_area"].='<form method="get" action="/u_cabinet/data/my_messages/action/delete/rid/'.$reply_mess["tbl_obj_id"].'/">
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
					$messages = SQLProvider::ExecuteQuery("select m.*, u.*,  bl.blocked_id from tbl__messages m
																join vw__all_users_full u on ".($m_action=="inbox"?"sender_id":"reciever_id")."=user_key
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
              
              if($messages[$i]["logo"] == '') { $messages[$i]["logo"] = 'nologo.png';}
              
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



						$cab["main_area"] .='<form id="fav_del_form" method="post"><table cellspacing=0 cellpadding=0 class="message_list">
										<tr>
										  <th>&nbsp;</th>
										  <th>&nbsp;</th>
											<th>'.($m_action=="inbox"?"От":"Кому").'</th>
											<th>Сообщение</th>
											<th width="130">Дата</th>'.($m_action=="outbox"?"<th width='130'>Прочитано</th>":"").'
											'/* <th width="120" align="center">'.($m_action=="blacklist"?"&nbsp;": ' <a id="selall" href="#" style="color:#0063AF;" onClick="javascript:return SelectMultiple('.implode(",",$mids).');">Удалить</a> ').'</th> ' */.'
										  <th width="70" style="text-align:center" align="center">Удалить</th>
                    </tr>
										'.$messagesList->renderHTML().'
										'.($m_action=="blacklist"?"":
										'<tr>
											<td style="border:none" colspan="'.($m_action=="inbox"?"4":"5").'">&nbsp;</td>
											<td style="border:none" align="center"><input type="hidden" name="delete_multiple" value="1"><input type="hidden" value="Удалить"/></td>
										</tr>').'
										</table></form>

<div id="dialog-confirm" title="Удалить?">
  <p></p>
</div>
									 
<script type="text/javascript">
$(function() {

	$(".fav_del_cross").click( function() {
	   
	   var itemText = "Вы уверены что хотите удалить сообщение от "+$(this).parents("tr").find("a").html()+"?";
	   var checkItem = $(this).next();
	   
		$( "#dialog-confirm" ).dialog({
			resizable: false,
			height:160,
			width:400,
			dialogClass: "dialog-confirm",
			modal: false,
  		position: "center",
			open: function(event, ui) { 
          $(this).find("p").html(itemText);
       },
			buttons: {
				"Да": function() {
				  checkItem.attr("checked","checked");
					$("#fav_del_form").submit();
					$( this ).dialog( "close" );
				},
				"Нет": function() {
					$( this ).dialog( "close" );
				}
			}
		});
		
	});
		
});
</script>

                    <script type="text/javascript">
                    /*
										var del_selected = false;
										$("#btn_fav_del").click(function(){
											if(confirm("Вы уверены что хотите удалить сообщение?")) {
												$(".fav_del").attr("checked","checked");
												$("#fav_del_form").submit();
											}
										return false;});
										$(".fav_del_cross").click( function() {
											if(confirm("Вы уверены что хотите удалить сообщение от "+$(this).parents("tr").find("a").html()+"?")) {
												$(this).next().attr("checked","checked");
												$("#fav_del_form").submit();
											}
										});
										*/
									</script>
                    
                    ';

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
		}

		$menu_render = "";

		foreach ($left_menu as $key=>$menu) {
			if (isset($menu["type"]))
			{
				if ($menu["type"] == "label")
				$menu_render .= $menu["text"]."<br>";
				else {
					$menu_render .= "<a href=/u_cabinet/data/".$key." style=\"color: ".$menu["color"].";\">";
					if ($menu["selected"])
						$menu_render .= "<span style=\" text-decoration: underline;\">".$menu["text"]."</span>";
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
