<?php
	class CHeaderObject extends CHTMLObject 
	{
		public $key;

		public $itemTemplates = array();

		public function CKeyTemplateObject()
		{
			$this->CHTMLObject();
		}
		
		public function RenderHTML()
		{
			$texts = array();
			$texts[] = "быстрый поиск по каталогу...";
			$texts[] = "введите слово или фразу...";
			$texts[] = "вы нашли то, что искали...";
			$texts[] = "поищите здесь...";
			$texts[] = "введите слово, мы найдЄм...";
			$texts[] = "всЄ что вы ищите - здесь...";
			$texts[] = "попробуйте поискать здесь...";
			
			$num = rand(0,sizeof($texts)-1);
			
			$headerdata["searchtext"] = $texts[$num];
			switch (date("m")) {
								case "01" : $month = "€нвар€"; break;
								case "02" : $month = "феврал€"; break;
								case "03" : $month = "марта"; break;
								case "04" : $month = "апрел€"; break;
								case "05" : $month = "ма€"; break;
								case "06" : $month = "июн€"; break;
								case "07" : $month = "июл€"; break;
								case "08" : $month = "августа"; break;
								case "09" : $month = "сент€бр€"; break;
								case "10" : $month = "окт€бр€"; break;
								case "11" : $month = "но€бр€"; break;
								case "12" : $month = "декабр€"; break;
							}
			$headerdata["logodate"] = date("d $month Y");
			
			
			$user = new CSessionUser("user");
			CAuthorizer::AuthentificateUserFromCookie(&$user);
			CAuthorizer::RestoreUserFromSession(&$user);
			$this->key = "login";
			$error = null;
						
			if (CURLHandler::IsPost()) {
				
				$event = GP("event");
				
				if ($event=="login") {
					 
					$login = GP("login");
					$password = GP("password");
					
				   if ($login!=NULL) { 
					   $authresult = CAuthorizer::AuthentificateUser($login,$password,&$user);
					   if ($authresult == false){
						  $error = $this->GetMessage("badauth");
					   }
					   if ($authresult == true && GP("remeber")=="remember") {
						   setcookie(COOKIE_USER_AUTH,$login,time()+60*60*24*14,"/");
					   }
									
				   }		
				}			
				if ($event=="logout")
				{
					setcookie(COOKIE_USER_AUTH,"",0,"/");
					$user->Clear();
				}
			}
			
			if (!$user->authorized)
			{
				$login = GP("login");
				$type = GP("type");
				$headerdata["login"] = $login;
				$headerdata["error"] = $error;
				$headerdata["type"] = $type;

			}
			if ($user->authorized)
			{
				$this->key="logout";

				$headerdata["name"] = $user->name;
				$headerdata["type"] = $user->type;
				
				$headerdata["user_id"] = $user->id;
				
				
				
				$headerdata["user_title_url"] = $user->title_url;
        if(empty($user->title_url)) { $headerdata["user_title_url"] = $user->id;}
  				
				
				switch ($user->type) {
					case "user" : 
						$headerdata["typecolor"] = "black";
						$headerdata["cabtype"] = "u";
					break;
					case "contractor" : 
						$headerdata["typecolor"] = "#f05620";
						$headerdata["cabtype"] = "r";
					break;
					case "area" : 
						$headerdata["typecolor"] = "#3399ff";
						$headerdata["cabtype"] = "r";
					break;
					case "artist" : 
						$headerdata["typecolor"] = "#ff0066";
						$headerdata["cabtype"] = "r";
					break;
					case "agency" : 
						$headerdata["typecolor"] = "#99cc00";
						$headerdata["cabtype"] = "r";
					break;
				}
			
				$uid = $user->type.$user->id;
				$new_count = SQLProvider::ExecuteScalar("select count(*) as quan from tbl__messages m
																		left join tbl__black_list bl on (reciever_id=bl.user_id and sender_id=blocked_id) and bl.user_id='$uid'
																	where blocked_id is null and `status`='sent' and reciever_id='$uid'");
				$headerdata["newmess"] = $new_count;
				//$account->dataSource["posted"] = "1";
				//$account->dataSource["regtype"] = GP("regtype","");
				//if ($this->IsPostBack)
				//	$account->dataSource["posted"] = "1";
				//else
				//	$account->dataSource["posted"] = "0";	
			}
			
			$city = GP("city");
			if (isset($city) && is_numeric($city) && $city > 0)
			{
				$cities = SQLProvider::ExecuteQuery("select * from `tbl__city` where `tbl_obj_id` = $city");
				$headerdata["selcity"] = $cities[0]["title"];
			}
			else
			{
				$headerdata["selcity"] = "¬се города";
			}		
			$this->dataSource = $headerdata;

			if (is_array($this->itemTemplates))
			{
				$ikeys = array_keys($this->itemTemplates);
				foreach ($ikeys as $i) 
				{
					if (is_a($this->itemTemplates[$i],"CTemplateData"))
					{
						if ($this->itemTemplates[$i]->key==$this->key)
						{
							return CStringFormatter::Format($this->itemTemplates[$i]->GetTemplate(),$this->GetDataSourceData());
						}
					}
				}
			}
			return "";
		}
	}
?>
