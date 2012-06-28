<?php
	class interactive_php extends CPageCodeHandler 
	{
		public $max_per_page = 10;
		
		public function interactive_php()
		{
			$this->CPageCodeHandler();
		}
		
		public function PreRender()
		{
			$news = SQLProvider::ExecuteQuery( "select * from `tbl__interactive` where active=1 order by display_order desc ");
			$flashs = array();
			foreach ($news as $key=>$value)
			{
				$flashs[floor($key/3)]["flash".($key%3)] = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="200" height="200">
				  <param name="movie" value="/upload/'.$news[$key]['s_flash'].'" />
				  <param name="quality" value="high" />
				  <embed src="/upload/'.$news[$key]['s_flash'].'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="200" height="200"></embed>
				</object>';
				$flashs[floor($key/3)]["flash".($key%3+1)] = "";
				$flashs[floor($key/3)]["flash".($key%3+2)] = "";
			}
			
			$newsList = $this->GetControl("newsList");
			$newsList->dataSource = $flashs;
		}
	}
?>
