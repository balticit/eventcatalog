<?php
class authorization_required_php extends CPageCodeHandler
{
	
	var $reqMessage;
	public $login;
	
	public function authorization_required_php()
	{
		$this->CPageCodeHandler();
	}

	private function Authorize()
	{
		$user = new CSessionUser("user");
		CAuthorizer::RestoreUserFromSession(&$user);
		$error = null;
		if ($this->IsPostBack)
		{
			$event = GP("event");
			if ($event=="login")
			{
				$login = GP("login");
				$password = GP("password");
				if (!CAuthorizer::AuthentificateUser($login,$password,&$user))
				{
					$error = $this->GetMessage("badauth");
				}
				else
				{
					$target = GP("target");
					if (!is_null($target))
					{
						CURLHandler::Redirect($target);
					}
				}
			}
			if ($event=="logout")
			{
				$user->Clear();
			}
		}
		if (!$user->authorized)
		{
			$this->login = GP("login");
		}
		if ($user->authorized)
		{
			$target = GP("target");
			if (!is_null($target))
			{
				CURLHandler::Redirect($target);
			}
			else
			{
				CURLHandler::Redirect("/");
			}
		}
		
		$messageKey = "default";
		$target = GP("target");
		if (strpos($target,"/registration/personal/type/vacancy")===0)
		{
			$messageKey = "vacancy";
		}
		if (strpos($target,"/registration/personal/type/cv")===0)
		{
			$messageKey = "cv";
		}
		if (strpos($target,"/registration/junksale/type/sale")===0)
		{
			$messageKey = "sale";
		}
		if (strpos($target,"/registration/junksale/type/buy")===0)
		{
			$messageKey = "buy";
		}
		$this->reqMessage = $this->GetMessage($messageKey);
	}

	public function PreRender()
	{
		$this->Authorize();
	}
}
?>
