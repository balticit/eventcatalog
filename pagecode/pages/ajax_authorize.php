<?php
	class ajax_authorize_php extends CPageCodeHandler 
	{
		public function ajax_authorize_php()
		{
			$this->CPageCodeHandler();
		}
		public function PreRender()
		{
		}
		public function Render()
		{

			header('Content-type: text/html;charset=windows-1251');
			$user = new CSessionUser("user");
			CAuthorizer::AuthentificateUserFromCookie(&$user);
			CAuthorizer::RestoreUserFromSession(&$user);
			$error = null;
			$event = GP("event");
			if (isset($event))
			{
				if ($event=="login") {
					$login = iconv("utf-8","windows-1251",GP("login")); 
					$password = GP("password");
					if ($login!=NULL) { 
					   $authresult = CAuthorizer::AuthentificateUser($login,$password,&$user);
					   echo($authresult);
					   if ($authresult == true && GP("remeber")=="remember") {
						   setcookie('COOKIE_USER_AUTH',$login,time()+60*60*24*14,"/");
					   }
				   }		
				}			
				else
				{
					setcookie('COOKIE_USER_AUTH',"",0,"/");
					$user->Clear();
				}
			}	
		}
	}
?>
