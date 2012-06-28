<?php
	class recent_php extends CPageCodeHandler 
	{
		public $max_per_page = 10;
		
		public function recent_php()
		{
			$this->CPageCodeHandler();
		}
		
		public function PreRender()
		{
			$news = SQLProvider::ExecuteQuery( "select * from `vw__news_recent`  order by display_order desc limit $this->max_per_page");
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
