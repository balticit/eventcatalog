<?php
	class show_technologies_php extends CPageCodeHandler 
	{
		public $max_per_page = 10;
		
		public function show_technologies_php()
		{
			$this->CPageCodeHandler();
		}
		
		public function PreRender()
		{
			$news = SQLProvider::ExecuteQuery( "select * from `tbl__show_technologies` order by date desc limit $this->max_per_page");
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
