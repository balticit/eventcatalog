<?php
	class contact_php extends CPageCodeHandler 
	{
		public function contact_php()
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
