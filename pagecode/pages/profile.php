<?php
class profile_php extends CPageCodeHandler
{
	public $user_name;
	public $user_types;
	public $logo_link;
	public $msg_link;
  public $msg_auth = "";
	public $user_info;
  public $logo_a_start = "";
  public $logo_a_end = "";
	
	public function profile_php()
	{
		$this->CPageCodeHandler();
	}

	public function PreRender()
	{
		$av_rwParams = array("type","id");
		CURLHandler::CheckRewriteParams($av_rwParams);  
    
    $user = new CSessionUser(null);
		CAuthorizer::AuthentificateUserFromCookie(&$user);
		CAuthorizer::RestoreUserFromSession(&$user);		
    
    $user_id = GP("id");
		$user_type = GP("type");
		if (IsNullOrEmpty($user_id) or IsNullOrEmpty($user_type)) {
      			CURLHandler::Redirect("/");
		}
		
		$user_data = SQLProvider::ExecuteQuery("select * from vw__all_users_full where user_id=$user_id and `type`='$user_type'");
		if (sizeof($user_data)==0)
		{
			CURLHandler::Redirect("/");
		}
		$user_data = $user_data[0];
		$this->user_name = $user_data["title"];
		$user_data["logo"] = GetFilename($user_data["logo"]);
		$this->logo_link = "/".($user_data["logo"]==''?"images/nologo.png":"upload/".$user_data["logo"]);
		$this->msg_link = "/u_cabinet/?data=my_messages&action=compose&type=$user_type&id=$user_id";
		$this->user_types = "";
		$this->user_info = "<span style=\"font-size:13px; color:#000;\">";
    if (!$user->authorized) {
      $this->msg_auth = "onclick=\"javascript: ShowMsgMessage();return false;\"";
    }
		if ($user_type == "user") {
			$user_types = SQLProvider::ExecuteQuery( "select * from `tbl__registered_user_types` where user_id = ".$user_id);
			foreach ($user_types as $key=>$user_type)
			{
				$this->user_types .= $user_type["user_type"];
				$user_links = array();
				switch($user_type["user_type"])
				{
					case "представитель подрядчика" :
						$user_links = SQLProvider::ExecuteQuery("select '#f05620' as typecolor, tr.resident_type, t.* from 
						                                         `tbl__registered_user_link_resident` tr 
																 left join tbl__contractor_doc t on t.tbl_obj_id = tr.resident_id
																 where tr.user_id = ".$user_id." and tr.resident_type = 'contractor'");
					break;
					case "представитель площадки" :
						$user_links = SQLProvider::ExecuteQuery("select '#3399ff' as typecolor, tr.resident_type, t.* from 
						                                         `tbl__registered_user_link_resident` tr 
																 left join tbl__area_doc t on t.tbl_obj_id = tr.resident_id
																 where tr.user_id = ".$user_id." and tr.resident_type = 'area'");
					break;
					case "представитель артиста" :
						$user_links = SQLProvider::ExecuteQuery("select '#ff0066' as typecolor, tr.resident_type, t.* from 
						                                         `tbl__registered_user_link_resident` tr 
																 left join tbl__artist_doc t on t.tbl_obj_id = tr.resident_id
																 where tr.user_id = ".$user_id." and tr.resident_type = 'artist'");
					break;
					case "представитель агентства" :
						$user_links = SQLProvider::ExecuteQuery("select '#99cc00' as typecolor, tr.resident_type, t.* from 
						                                         `tbl__registered_user_link_resident` tr 
																 left join tbl__agency_doc t on t.tbl_obj_id = tr.resident_id
																 where tr.user_id = ".$user_id." and tr.resident_type = 'agency'");
					break;
				}
				foreach ($user_links as $key=>$user_link)
				{
					$this->user_types .="&nbsp;&nbsp;<a href=\"/".$user_link["resident_type"]."/".$user_link["title_url"]."\"
								  style=\"color:".$user_link["typecolor"]."\">".$user_link["title"]."</a>";
				}
				
				$this->user_types .="<br />";
								
			}
			$user_data = SQLProvider::ExecuteQuery("select * from tbl__registered_user where tbl_obj_id = $user_id");
			
			
			
			
			$user_data = $user_data[0];
			/*$this->user_info .= $user_data["position"]."<br />";
			if (!IsNullOrEmpty($user_data["company"])) {
					$this->user_info .= $user_data["company"]."<br />"; 
				}	
			if (IsNullOrEmpty($user_data["sity"])) $user_data["sity"] = SQLProvider::ExecuteScalar("select title from tbl__city where tbl_obj_id = ".$user_data["city"]);
			$this->user_info .= $user_data["sity"];
			*/
			
			// IF not reg 
			
			$show = explode('|',$user_data["display_type"]);
			
			
			
		//	echo $show[0];
			
			$this->user_info = '<div class="u_info_block">';
  			if ($user_data["sity"] != '') $this->user_info .= "<b>Город:</b> ".$user_data["sity"]."<br />";
			
			//if ( $user->authorized && $user_data["display_type"]== '1' or $user_data["display_type"]== '0') {
			
			  
			  if ($user_data["contact_phone"] != '') {
  			 if ($user->authorized && $show[10]== '1' or $show[10]== '0') {
            $this->user_info .= "<b>Мобильный телефон:</b> ".$user_data["contact_phone"]."<br />";
          }
          if (!$user->authorized && $show[10]== '1') {
            $this->user_info .= '<b style="color:#ccc">Мобильный телефон:</b><br />';
          }
        }
        
        if ($user_data["skype"] != '') {
          if ($user->authorized && $show[2]== '1' or $show[2]== '0') {
            $this->user_info .= "<b>Skype:</b> ".$user_data["skype"]."<br />";
          }
          if (!$user->authorized && $show[2]== '1') {
            $this->user_info .= '<b style="color:#ccc">Skype:</b><br />';
          }
        }
        
        if ($user_data["icq"] != '') {
          if ($user->authorized && $show[3]== '1' or $show[3]== '0') {
            $this->user_info .= "<b>ICQ:</b> ".$user_data["icq"]."<br />";
          }
          if (!$user->authorized && $show[3]== '1') {
            $this->user_info .= '<b style="color:#ccc">ICQ:</b><br />';
          }
        }
        
        if ($user_data["company_phone"] != '') {
          if ($user->authorized && $show[11]== '1' or $show[11]== '0') {
            $this->user_info .= "<b>Рабочий телефон:</b> ".$user_data["company_phone"]."<br />";
          }
          if (!$user->authorized && $show[11]== '1') {
            $this->user_info .= '<b style="color:#ccc">Рабочий телефон:</b><br />';
          }
        }
        
        if ($user_data["email"] != '') $this->user_info .= "<b>Электронная почта:</b> <a href='mailto:".$user_data["email"]."'>".$user_data["email"]."</a><br />";
        
        if ($user_data["site"] != '') {
          if ($user->authorized && $show[8]== '1' or $show[8]== '0') {
            $this->user_info .= "<b>Сайт компании:</b> <a target='_blank' href='http://".$user_data["site"]."'>".$user_data["site"]."</a><br /><br />";
          }
          if (!$user->authorized && $show[8]== '1') {
            $this->user_info .= '<b style="color:#ccc">Сайт компании:</b><br /><br />';
          }
        }
        


        $this->user_info .= "<b>На сайте:</b> ".onSiteTime($user_data["registration_date"])."<br />";
        
        if ($user_data["birthday"] != '') {
          if ($user->authorized && $show[4]== '1' or $show[4]== '0') {
            $this->user_info .= "<b>Возраст:</b> ".UserAge($user_data["birthday"])."<br />";
          }
          if (!$user->authorized && $show[4]== '1') {
            $this->user_info .= '<b style="color:#ccc">Возраст:</b><br />';
          }
        }
        
        if ($user->authorized && $show[5]== '1' or $show[5]== '0') {
    			if ($user_data["sex"] != '0') { $sex = "Женский"; } else { $sex = "Мужской";}
          if ($user_data["sex"] != '') $this->user_info .= "<b>Пол:</b> ".$sex."<br />";
        }
        if (!$user->authorized && $show[5]== '1') {
            $this->user_info .= '<b style="color:#ccc">Пол:</b><br />';
        }

        if (!IsNullOrEmpty($user_data["company"])) {
          if ($user->authorized && $show[6]== '1' or $show[6]== '0') {
    				$this->user_info .= "<b>Компания:</b> ".$user_data["company"]."<br />";
    			}
    			if (!$user->authorized && $show[6]== '1') {
            $this->user_info .= '<b style="color:#ccc">Компания:</b><br />';
          }
        }
        
        if ($user_data["position"] != '') {
          if ($user->authorized && $show[7]== '1' or $show[7]== '0') {
            $this->user_info .= "<b>Должность:</b> ".$user_data["position"]."<br />";
          }
          if (!$user->authorized && $show[7]== '1') {
            $this->user_info .= '<b style="color:#ccc">Должность:</b><br />';
          }
        }
        
        if ($user_data["address"] != '') {
          if ($user->authorized && $show[9]== '1' or $show[9]== '0') {
            $this->user_info .= "<b>Адрес:</b> ".$user_data["address"]."<br />";
          }
          if (!$user->authorized && $show[9]== '1') {
            $this->user_info .= '<b style="color:#ccc">Адрес:</b><br />';
          }
        }


  			
			
		//	}
			if (!$user->authorized && in_array('1', $show)) { $this->user_info .= '<br />Для того, чтобы увидеть содержимое полей <a href="" onclick="javascript: ShowLogonDialog(); return false;">войдите</a> или <a href="" onclick="javascript: ShowRegUser(); return false;">зарегистрируйтесь</a>.'; }
			
			$this->user_info .= '</div>';
			
		}
    else {
		  $title_url = SQLProvider::ExecuteScalar("select title_url from tbl__".$user_type."_doc where tbl_obj_id = $user_id");
      $this->user_types = '<a class="'.$user_type.'" href="/'.$user_type.'/'.$title_url.'">Перейти на персональную страницу резидента</a>';
      $this->logo_a_start = '<a href="/'.$user_type.'/'.$title_url.'">';
      $this->logo_a_end = '</a>';
    }
		$this->user_info .="</span>";
		
	}

}
?>
