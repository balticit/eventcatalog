<?php
	class registration_reset_php extends CPageCodeHandler 
	{
		public $mailpath;

		public $mailtitle;
		
		public function registration_reset_php()
		{
			$this->CPageCodeHandler();
		}
		
		public function PreRender()
		{
			$code = GP("resetcode");
			$reset = $this->GetControl("reset");
			if (is_null($code))
			{
				$error = "";
				$email = "";
				if (($this->IsPostBack)&&(GP("action")=="sendInstructions"))
				{
					$email = GP("email");
					$email = str_replace("'","''",$email);
					$login = GP("login");
					$login = str_replace("'","''",$login);
					$users = SQLProvider::ExecuteQuery("select tbl_obj_id,login,login_type from vw__all_users where active=1
						and registration_confirmed=1 and email='$email' and login='$login'");
					if (sizeof($users)>0)
					{
						$mailTemplate = file_get_contents(RealFile($this->mailpath));
						$title = iconv($this->encoding,"utf-8",$this->mailtitle);
						foreach ($users as $user) {
							$table = null;
							switch ($user["login_type"]) {
								case "user":
								$table = 'tbl__registered_user';
								break;
								case "agency":
								$table = 'tbl__agency_doc';
								break;
								case "area":
								$table = 'tbl__area_doc';
								break;
								case "contractor":
								$table = 'tbl__contractor_doc';
								break;
								case "artist":
								$table = 'tbl__artist_doc';
								break;
							}
							$id = $user["tbl_obj_id"];
							$time = time()+259200;
							$guid = $better_token = md5(uniqid(rand(), true));
							SQLProvider::ExecuteNonReturnQuery("update $table set password_reset='$guid' ,reset_expire=$time 
								where tbl_obj_id=$id");
							$mbody = CStringFormatter::Format($mailTemplate,array("link"=>"http://".$_SERVER['HTTP_HOST']."/registration/reset/?resetcode=$guid",
								"login"=>$user["login"],"date"=>date("d.m.Y H:i:s")));
							SendHTMLMail($email,$mbody,$title);
						}
						$reset->key="mailSent";		
					}
					else 
					{
						$error = "Пользователей с таким логином или e-mail не существет";
					}
				}
				$reset->dataSource = array("email"=>$email,"login"=>$login,"error"=>$error);
			}
			else 
			{
				$error = "";
				$time = time();
				$users = SQLProvider::ExecuteQuery("select tbl_obj_id,login_type from vw__all_users where active=1
						and registration_confirmed=1 and password_reset='$code' and reset_expire>$time");
				if (sizeof($users)==0)
				{
					$reset->key="invalidCode";	
					return;
				}
				$reset->key="enterPassword";
				if (($this->IsPostBack)&&(GP("action")=="resetPassword"))
				{
					$newPass = GP("newpass");
					$confirm = GP("confirm");
					if (IsNullOrEmpty($newPass))
					{
						$error = "Ошибка при вводе пароля";
					}
					if (IsNullOrEmpty($error))
					{
						if ($newPass!=$confirm)
						{
							$error = "Пароль не совпадает с подтвержденем";
						}
					}
					if (IsNullOrEmpty($error))
					{
						$user = $users[0];
						$id = $user["tbl_obj_id"];
						$table = null;
							switch ($user["login_type"]) {
								case "user":
								$table = 'tbl__registered_user';
								break;
								case "agency":
								$table = 'tbl__agency_doc';
								break;
								case "area":
								$table = 'tbl__area_doc';
								break;
								case "contractor":
								$table = 'tbl__contractor_doc';
								break;
								case "artist":
								$table = 'tbl__artist_doc';
								break;
							}
						$newPass = md5($newPass);
						SQLProvider::ExecuteNonReturnQuery("update $table set password_reset='' ,password='$newPass' ,reset_expire=0 
								where tbl_obj_id=$id");
						$reset->key="resetComplete";
					}
				}
				$reset->dataSource = array("error"=>$error);
			}
		}
	}
?>
