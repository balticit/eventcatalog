<?php
	class opened_area_full_php extends CPageCodeHandler 
	{
		//public $max_per_page = 100;
		
		public function news_php()
		{
			$this->CPageCodeHandler();
		}
		
		public function PreRender()
		{
			$year = GP("year",date("Y",time()));
			//$news = SQLProvider::ExecuteQuery( "select * from `vw__opened_areas` v where YEAR(v.date)=$year order by v.date desc limit $this->max_per_page");
			$news = SQLProvider::ExecuteQuery( "select * from `vw__opened_areas` v order by v.date desc limit 5,100");
			foreach ($news as $key=>$value)
			{
				$news[$key]['date'] = date("d.m.Y",strtotime($news[$key]['date']));
				$news[$key]["parent"] = CURLHandler::$currentPath;
			}
			
			$newsList = $this->GetControl("newsList");
			$newsList->dataSource = $news;
			
			
			
			$years = array();
		$yy = array();
		if ((int)date("Y")>2008) {
			for ($y = 2008; $y<(int)date("Y");$y++) {
				$yy["year"] = $y;
				$years[] = $yy;
			}
		}
		
		//print_r($years);
		
		$yearss = $this->GetControl("archive");
		$yearss->dataSource = $years;
		}
	}
?>

