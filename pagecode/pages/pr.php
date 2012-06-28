<?php
	class pr_php extends CPageCodeHandler 
	{
		public function pr_php()
		{
			$this->CPageCodeHandler();
			
		}
		
		public function PreRender()
		{
			$av_rwParams = array();
		  CURLHandler::CheckRewriteParams($av_rwParams);  
		}
	}
?>
