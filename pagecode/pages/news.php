<?php
	class news_php extends CPageCodeHandler 
	{
		public $max_per_page = 25;
		
		public function news_php()
		{
			$this->CPageCodeHandler();
		}
		
		public function PreRender() {
		
		
		
			CURLHandler::CheckRewriteParams(array('cat', 'page'));
			$page = (int)GP("page", 1);
			$rewriteParams = array();
			if($page > 1) $rewriteParams["page"] = $page;
			
			// принимаем фильтр категории
			if(isset($_GET['cat'])) {
			$cat = SQLProvider::ExecuteScalar("select tbl_obj_id from tbl__news_dir where str_id = '" . mysql_real_escape_string($_GET['cat']) . "'");
			}
			else { $cat = false;}
		  //$cat = (!empty($_GET['cat'])) ? (int)$_GET['cat'] : false;
			
			// список категорий новостей
			$sql  = 'SELECT tbl_obj_id, title, str_id '.
					'FROM tbl__news_dir '.
					'ORDER BY sort DESC';
			$list = SQLProvider::ExecuteQuery($sql);
			if(!empty($list)) {
				foreach($list as $i => &$item) {
					// if ($item['tbl_obj_id'] == 0)
					// $item['link'] = "/book";
					// else
					$item['link']		= "/news/".$item['str_id'];
					$item['gray']		= "gray";
					$item['selected']	= ($item['tbl_obj_id'] == $cat) ? 'class="selected"' : '';
				}
				$list[] = array(
					'tbl_obj_id'	=> 0,
					'title'			=> '¬се новости',
					'link'			=> '/news/',
					'gray'			=> 'gray',
					'selected'		=> (empty($cat) ? 'class="selected"' : '')
				);
				$list = array_reverse($list);
			}
			$newsCategoriesList = $this->GetControl("newsCategoriesList");
			$newsCategoriesList->dataSource = $list;
			
			// заголовок категории новостей
			// if(!empty($cat)) {
				// $header = SQLProvider::ExecuteQuery('SELECT title FROM `tbl__news_dir` WHERE tbl_obj_id = '.$cat);
				// if(!empty($header)) $header = $header[0]['title'];
			// }
			// if(empty($header)) {
				$header = '—обыти€ индустрии';
			// }
			$title = $this->GetControl("title");
			$title->text = $header;
			
			// постраничка
			$sql  = 'SELECT count(*) count FROM `vw__news_soon` '.
						(!empty($cat) ? 'WHERE tbl_cai_id = '.$cat.' ' : '');
			$count = SQLProvider::ExecuteQuery($sql);
            $count = $count[0]["count"];
            $pages = floor($count / $this->max_per_page) + (($count % $this->max_per_page == 0) ? 0 : 1);
            if(($page > $pages) && ($pages > 0)) {
                $page = $pages;
                $rewriteParams["page"] = $page;
                CURLHandler::Redirect(CURLHandler::$currentPath . CURLHandler::BuildQueryParams($rewriteParams));
            }
            if($page == 1) unset($rewriteParams["page"]);
			
			//setting pager
			$pager = $this->GetControl("pager");
			$pager->currentPage		= $page;
			$pager->totalPages		= $pages;
			$pager->rewriteParams	= $rewriteParams;
			
			// список новостей
			$sql =  'SELECT *, DATE_FORMAT(creation_date,"%d.%m.%Y") as `creation_date` FROM `vw__news_soon` '.
						(!empty($cat) ? 'WHERE tbl_cai_id = '.$cat.' ' : '').
					'ORDER BY creation_date DESC LIMIT '.(($page - 1) * $this->max_per_page).', '.$this->max_per_page;
			// var_dump($sql);		
			// die('!');
			$news = SQLProvider::ExecuteQuery($sql);
			foreach ($news as $key=>$value)
			{
				//$news[$key]['date'] = date("d.m.Y",strtotime($news[$key]['creation_date']));
				
				
				if( $news[$key]["title_url"] == '') { $news[$key]["title_url"] = 'details'.$news[$key]["tbl_obj_id"];}
		    $news[$key]["news_url"] = $news[$key]["title_url"];
				
				$cat = $news[$key]["tbl_cai_id"];
				if(!empty($cat)){
					$news[$key]["theme"] = "–убрика: ".SQLProvider::ExecuteScalar("SELECT title from tbl__news_dir where tbl_obj_id=".$cat);
				}
				else{
					$news[$key]["theme"] = "";
				}
			}
			
			$newsList = $this->GetControl("newsList");
			$newsList->dataSource = $news;
			
			// && всего резидентов
		$counts = SQLProvider::ExecuteQuery("select vm.`login_type`, COUNT(*) as `count` from `vw__all_users` vm
												where vm.`active`=1 and vm.`login_type`<>'user'
												group by vm.`login_type`
												order by vm.`login_type` desc
												");
		
		$chart = array();
		
		$chart["count"] = $counts[0]["count"]+$counts[1]["count"]+$counts[2]["count"]+$counts[3]["count"];
		$chart["cont_count"] = $counts[0]["count"];
		$chart["area_count"] = $counts[2]["count"];
		$chart["arti_count"] = $counts[1]["count"];
		$chart["agen_count"] = $counts[3]["count"];
		$max = max($counts[0]["count"],$counts[1]["count"],$counts[2]["count"],$counts[3]["count"]);
		$h = 70;
		$k = $h/$max;;
		$chart["cont_height"] = floor($counts[0]["count"]*$k);
		$chart["area_height"] = floor($counts[2]["count"]*$k);
		$chart["arti_height"] = floor($counts[1]["count"]*$k);
		$chart["agen_height"] = floor($counts[3]["count"]*$k);
		
		$chart["cont_percent"] = floor($counts[0]["count"]/$chart["count"]*100);
		$chart["area_percent"] = floor($counts[2]["count"]/$chart["count"]*100);
		$chart["arti_percent"] = floor($counts[1]["count"]/$chart["count"]*100);
		$chart["agen_percent"] = floor($counts[3]["count"]/$chart["count"]*100);		
		
		$chartObj = $this->GetControl("chart");
		$chartObj->dataSource = $chart;
		
		
		
		}
	}
?>
