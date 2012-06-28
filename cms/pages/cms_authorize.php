<?php
class cms_authorize_php extends CPageCodeHandler
{
	public function cms_authorize_php()
	{
		$this->CPageCodeHandler();
	}

	public function PreRender()
	{
		$error = "";
		$login = "";
		$redir = "false";
		$su = new CSessionUser(CMS_ADMIN_SESSION_KEY);
		if ($this->IsPostBack)
		{
			$event = GP("event");
			if (($event=="login")&&(!$su->authorized))
			{
				$login = GP("login","");
				$password = md5(GP("password",""));
				$user = SQLProvider::ExecuteQuery("select * from dir_admins where `username`='$login' and `password`='$password'");
				if (sizeof($user)==0)
				{
					$error = $this->GetMessage("badauth");
				}
				else 
				{
					$su->authorized = true;
					$su->name = $user[0]["username"];
					$su->admintype = $user[0]["admtype"];
					$su->Save();
					$redir = "true";
				}
			}
			if (($event=="logout")&&($su->authorized))
			{
				$su->Clear();
				$redir = "true";
			}
		}
		$account = $this->GetControl("account");
		if ($su->authorized)
		{
			$account->key = "logout";
			$account->dataSource =array("name"=>$su->name,"redir"=>$redir);
		}
		else 
		{
			$account->key = "login";
			$account->dataSource = array("login"=>$login,"error"=>$error,"redir"=>$redir);
		}
	}
}
?>
