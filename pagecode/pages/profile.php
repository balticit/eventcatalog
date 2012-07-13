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
			if (!$user->authorized && $user_data["display_type"]== '1') { $this->user_info = '<a onclick="javascript: return false;" id="selectenter" style="color: black" href="">Войдите</a> или <a href="/registration/?type=user">зарегистрируйтесь</a>.'; }
			
			if ( $user->authorized && $user_data["display_type"]== '1' or $user_data["display_type"]== '0') {
			
  			if ($user_data["sity"] != '') $this->user_info = "<b>Город:</b> ".$user_data["sity"]."<br />";
  			if ($user_data["contact_phone"] != '') $this->user_info .= "<b>Мобильный телефон:</b> ".$user_data["contact_phone"]."<br />";
  			
  			if ($user_data["skype"] != '') $this->user_info .= "<b>Skype:</b> ".$user_data["skype"]."<br />";
  			if ($user_data["icq"] != '') $this->user_info .= "<b>ICQ:</b> ".$user_data["icq"]."<br />";
  			
  			
  			if ($user_data["company_phone"] != '') $this->user_info .= "<b>Рабочий телефон:</b> ".$user_data["company_phone"]."<br />";
  			if ($user_data["email"] != '') $this->user_info .= "<b>Электронная почта:</b> ".$user_data["email"]."<br />";
  			if ($user_data["site"] != '') $this->user_info .= "<b>Сайт компании:</b> ".$user_data["site"]."<br /><br />";
  
  
        $this->user_info .= "<b>На сайте:</b> ".onSiteTime($user_data["registration_date"])."<br />";
        if ($user_data["birthday"] != '') $this->user_info .= "<b>Возраст:</b> ".UserAge($user_data["birthday"])."<br />";
        
     
        if ($user_data["sex"] != '0') { $sex = "Женский"; } else { $sex = "Мужской";}
        if ($user_data["sex"] != '') $this->user_info .= "<b>Пол:</b> ".$sex."<br />";
        
  	    if (!IsNullOrEmpty($user_data["company"])) {
  				$this->user_info .= "<b>Компания:</b> ".$user_data["company"]."<br />";
  			}
  			if ($user_data["position"] != '') $this->user_info .= "<b>Должность:</b> ".$user_data["position"]."<br />";
  			if ($user_data["address"] != '') $this->user_info .= "<b>Адрес:</b> ".$user_data["address"]."<br />";
			
			}
			
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
