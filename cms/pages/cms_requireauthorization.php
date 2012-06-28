<?php
	class cms_requireauthorization_php extends CPageCodeHandler 
	{
		public function cms_requireauthorization_php()
		{
			$this->CPageCodeHandler();
		}
		
		public function PreRender()
		{
			$su = new CSessionUser(CMS_ADMIN_SESSION_KEY);
			if ($su->authorized)
			{ 
				CURLHandler::Redirect("/cms/");	
			}
		}
	}
?>
