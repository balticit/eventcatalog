<?php
	class download_php extends CPageCodeHandler 
	{
		public function download_php()
		{
			$this->CPageCodeHandler();
		}
		
		public function PreRender()
		{
			$av_rwParams = array("file");
		  CURLHandler::CheckRewriteParams($av_rwParams);  
      $file = GP("file");	
			
			if (IsNullOrEmpty($file))
			{
				CURLHandler::ErrorPage();
			}
			$file = base64_decode($file);
			
			$path = ROOTDIR.IMAGES_UPLOAD_DIR.$file;
			if (!file_exists($path))
			{
				CURLHandler::ErrorPage();
			}
			header('Content-Disposition: attachment; filename="'.$file.'"');
			readfile($path);
		}
	}
?>
