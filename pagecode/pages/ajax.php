<?php
	class ajax_php extends CPageCodeHandler 
	{

		public function ajax_php()
		{
			$this->CPageCodeHandler();
		}
		
		public function PreRender()
		{
			iconv_set_encoding("output_encoding", "WINDOWS-1251");
			
			$query = GP("query");
			
			$bodyData = array();
			//$bodyData[0] = array();
			
			$path = "http://maps.google.com/maps/geo?q=".$query."&output=xml&key=".GOOGLEMAPKEY;
			
			$path = iconv("WINDOWS-1251","UTF-8",$path);
			
			//iconv("WINDOWS-1251","UTF-8",$path);
			
			$xml = file_get_contents($path);
			header('Content-type: application/xml; charset="utf-8"',true);
			$bodyData["body"] = $xml;
			
			//echo $xml->Response->Status->code;
			
			$body = $this->GetControl("body");
			$body->dataSource = $bodyData;
			
			

		}
	}
?>
