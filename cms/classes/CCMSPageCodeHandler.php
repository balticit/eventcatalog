<?php
class CCMSPageCodeHandler extends CPageCodeHandler
{
	public $admtype;
	
	public function CCMSPageCodeHandler()
	{
		$this->CPageCodeHandler();
		$su = new CSessionUser(CMS_ADMIN_SESSION_KEY);
		if (!$su->authorized)
		{
			CURLHandler::Redirect("/cms/requireauthorization/");
		}
		else {
			$admtype = $su->admintype;
		}
	}

	public function Render()
	{
		$this->rewriteParams = CURLHandler::$rewriteParams;
		$this->PreRender();
		//var_dump($this->rewriteParams);
		include($this->template);
	}
}
?>
