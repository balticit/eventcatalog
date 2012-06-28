<?php
	class about_php extends CPageCodeHandler 
	{
		public function about_php()
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