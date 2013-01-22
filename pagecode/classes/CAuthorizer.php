<?php
class CAuthorizer extends CObject 
{	

	public function CAuthorizer()
	{
		$this->CObject();
	}
	
	public static function AuthentificateUser($login,$password,&$sessionUser = null,$types = array("user","agency","area","contractor","artist"))
	{
		$password = md5($password);
		$user = SQLProvider::ExecuteQuery("SELECT 
						  `tbl_obj_id`,
						  `title`,
						  `title_url`,
						  `login`,
						  `login_type`
						FROM 
						  `vw__all_users` ust 
						  where ust.login='$login' and UPPER(ust.password)=UPPER('$password') and ust.registration_confirmed=1 and ust.active=1;");
		if (sizeof($user)==0)
		{
			return false;
		}
		$user = $user[0];
		if (array_search($user["login_type"],$types)===false)
		{
			return false;
		}
		$su = new CSessionUser($user["login_type"]);
		$su->name = $user["title"];
		$su->authorized = true;
		$su->id = $user["tbl_obj_id"];
		$su->Save();
		$sessionUser = $su;
		return true;
	}
	
	public static function AuthentificateUserFromCookie(&$sessionUser = null,$types = array("user","agency","area","contractor","artist"))
	{
		if (!isset($_COOKIE['COOKIE_USER_AUTH']))
		{
			return false;
		}
		$login = $_COOKIE['COOKIE_USER_AUTH'];
		$user = SQLProvider::ExecuteQuery("SELECT 
						  `tbl_obj_id`,
						  `title`,
						  `title_url`,
						  `login`,
						  `login_type`
						FROM 
						  `vw__all_users` ust 
						  where ust.login='$login' and ust.registration_confirmed=1 and ust.active=1;");
		if (sizeof($user)==0)
		{
			return false;
		}
		$user = $user[0];
		if (array_search($user["login_type"],$types)===false)
		{
			return false;
		}
		$su = new CSessionUser($user["login_type"]);
		$su->name = $user["title"];
		$su->authorized = true;
		$su->id = $user["tbl_obj_id"];
		$su->Save();
		$sessionUser = $su;
		return true;
	}
	
	public static function RestoreUserFromSession(&$sessionUser,$types = array("user","agency","area","contractor","artist"))
	{
		foreach ($types as $type) {
			$su = new CSessionUser($type);
			if ($su->authorized)
			{
				$sessionUser = $su;
				return ;
			}
		}
	}
}
?>