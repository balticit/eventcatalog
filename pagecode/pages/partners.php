<?php
	class partners_php extends CPageCodeHandler 
	{
		public $max_per_page = 10;
		
		public function partners_php()
		{
			$this->CPageCodeHandler();
		}
		
		public function PreRender()
		{
			$av_rwParams = array();
		  CURLHandler::CheckRewriteParams($av_rwParams);  	
      
			$news = SQLProvider::ExecuteQuery( "select * from tbl__partners  order by display_order desc limit $this->max_per_page");
			foreach ($news as $key=>$value)
			{
				$news[$key]['date'] = date("d.m.Y",strtotime($news[$key]['date']));
				$news[$key]["parent"] = CURLHandler::$currentPath;
			}
			
			$newsList = $this->GetControl("newsList");
			$newsList->dataSource = $news;
		}
	}
?>
