<?php

class authorize_php extends CPageCodeHandler
{
	public function authorize_php()
	{
		$this->CPageCodeHandler();
	}

	private function Authorize()
	{
		$account = $this->GetControl("account");
		$user = new CSessionUser("user");
		CAuthorizer::AuthentificateUserFromCookie(&$user);
		CAuthorizer::RestoreUserFromSession(&$user);
		$error = null;
		
		
		if ($this->IsPostBack) {
			
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
			$account->key="login";
			$type = GP("type");
			$account->dataSource = array("login"=>$login,"error"=>$error,"type"=>$type);
			$account->dataSource["width"] = GP("width","auto");
		}
		if ($user->authorized)
		{
			$account->key="logout";
			$account->dataSource = $user->ToHashMap();
			$account->dataSource["width"] = (int)GP("width","auto")+13;
			
			$account->dataSource["user_id"] = $user->id;
			$account->dataSource["type"] = $user->type;
			
			$account["user_title_url"] = $user->title_url;
      if(empty($user->title_url)) { $account["user_title_url"] = $user->id;}


			
			switch ($user->type) {
				case "user" : 
					$account->dataSource["typecolor"] = "black";
					$account->dataSource["cabtype"] = "u";
				break;
				case "contractor" : 
					$account->dataSource["typecolor"] = "#f05620";
					$account->dataSource["cabtype"] = "r";
				break;
				case "area" : 
					$account->dataSource["typecolor"] = "#3399ff";
					$account->dataSource["cabtype"] = "r";
				break;
				case "artist" : 
					$account->dataSource["typecolor"] = "#ff0066";
					$account->dataSource["cabtype"] = "r";
				break;
				case "agency" : 
					$account->dataSource["typecolor"] = "#99cc00";
					$account->dataSource["cabtype"] = "r";
				break;
			}
		}
		//$account->dataSource["width"] = GP("width","auto");
		$uid = $user->type.$user->id;
		$new_count = SQLProvider::ExecuteScalar("select count(*) as quan from tbl__messages m
																left join tbl__black_list bl on (reciever_id=bl.user_id and sender_id=blocked_id) and bl.user_id='$uid'
															where blocked_id is null and `status`='sent' and reciever_id='$uid'");
		$account->dataSource["newmess"] = $new_count;
		$account->dataSource["posted"] = "1";
		$account->dataSource["regtype"] = GP("regtype","");
		if ($this->IsPostBack)
			$account->dataSource["posted"] = "1";
		else
			$account->dataSource["posted"] = "0";	
	}

	public function PreRender()
	{
		
		$this->Authorize();
	}
}
?>
