<?php
	class news_details_php extends CPageCodeHandler 
	{
	
	  public $max_per_page = 25;
	
		public function news_details_php()
		{
			$this->CPageCodeHandler();
		}
		
		public function PreRender()
		{
			
			
			
$id_str = GP("id");
if (!is_null($id_str)) {
  $this->id = SQLProvider::ExecuteScalar("select tbl_obj_id from tbl__news_dir where str_id = '" . mysql_real_escape_string($id_str) . "'");
  if(!IsNullOrEmpty($this->id)){
    $cat = $this->id;
    $this->is_list = true;
  }
  else{
    $this->is_list = false;
  }
}			
			
			
			
			
			
			
if($this->is_list){
	
  CURLHandler::CheckRewriteParams(array('cat', 'page'));
			$page = (int)GP("page", 1);
			$rewriteParams = array();
			if($page > 1) $rewriteParams["page"] = $page;
			
			// принимаем фильтр категории
			//if(isset($_GET['cat'])) {
			//$cat = SQLProvider::ExecuteScalar("select tbl_obj_id from tbl__news_dir where str_id = '" . mysql_real_escape_string($_GET['cat']) . "'");
			//}
			//else { $cat = false;}
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
					'title'			=> 'Все новости',
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
				$header = 'События индустрии';
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
					$news[$key]["theme"] = "Рубрика: ".SQLProvider::ExecuteScalar("SELECT title from tbl__news_dir where tbl_obj_id=".$cat);
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
			
			
			
			
	
else {
/*Провека адреса*/
      $id_str = GP("id");
      if (!is_null($id_str)) {
        $this->id = SQLProvider::ExecuteScalar("select tbl_obj_id from tbl__news where title_url = '" . mysql_real_escape_string($id_str) . "'");
        if(!IsNullOrEmpty($this->id)){
          $id = $this->id;
        }
        else{
          $id = GP("id");
          $id = (int)str_replace('details', '', $id);
        }
      }		
			
			
			
			$app = CApplicationContext::GetInstance();
      $av_rwParams = array("id");
		  CURLHandler::CheckRewriteParams($av_rwParams);  
			$rewriteParams = array();
			
			
			
			
			$unit = SQLProvider::ExecuteQuery("SELECT n.*, c.title as city_title, a.title as area_title from tbl__news n 
												LEFT JOIN tbl__city c
												ON n.city = c.tbl_obj_id 
												LEFT JOIN tbl__artist_doc a
												ON n.area_id = a.tbl_obj_id 
												where n.tbl_obj_id = $id");
			if (sizeof($unit)==0)
			{
				CURLHandler::ErrorPage();
			}
			 
			
			// список категорий новостей
			$sql  = 'SELECT tbl_obj_id, title, str_id '.
					'FROM tbl__news_dir '.
					'ORDER BY sort DESC';
			$list = SQLProvider::ExecuteQuery($sql);
			if(!empty($list)) {
				foreach($list as $i => &$item) {
					$item['link']		= "/news/".$item['str_id'];
					$item['gray']		= "gray";
					$item['selected']	= ($item['tbl_obj_id'] == $unit[0]['tbl_cai_id']) ? 'class="selected"' : '';
				}
				$list[] = array(
					'tbl_obj_id'	=> 0,
					'title'			=> 'Все новости',
					'link'			=> '/news/',
					'gray'			=> 'gray',
					'selected'		=> (empty($unit[0]['tbl_cai_id']) ? 'class="selected"' : '')
				);
				$list = array_reverse($list);
			}
			$newsCategoriesList = $this->GetControl("newsCategoriesList");
			$newsCategoriesList->dataSource = $list;
			
			$news_date = $unit[0]['date']; 
			$title = $this->GetControl("title");
			$title->text = $unit[0]["title"];			
			$unit[0]['date'] = !empty($unit[0]['date']) ?  '<div class="newsParam"><b>Дата события:</b> '.date("d.m.Y",strtotime($unit[0]['date'])).' г.</div>' : '';
			$unit[0]['site'] = !empty($unit[0]['site']) ?  '<div class="newsParam"><b>Сайт организатора: </b><a href="'.$unit[0]['site'].'" target="_blank">'.$unit[0]['site'].'</a></div>' : '';
			$unit[0]['type'] = !empty($unit[0]['type']) ?  '<div class="newsParam"><b>Тип мероприятия:</b> '.$unit[0]['type'].'</div>' : '';
			$unit[0]['city'] = !empty($unit[0]['city_title']) ?  '<div class="newsParam"><b>Город:</b> '.$unit[0]['city_title'].'</div>' : '';
			$unit[0]['area'] = !empty($unit[0]['area_title']) ?  '<div><b>Площадка:</b> '.$unit[0]['area_title'].'</div>' : '';
			
			
			//photos
			// $photos = SQLProvider::ExecuteQuery("SELECT
						  // p.*
						// FROM 
						  // `tbl__news2photo`  ap
						  // inner join `tbl__photo` p 
						  // on ap.photo_id = p.tbl_obj_id 
						  // where news_id=$id limit 8");
			// $unit[0]["photo_list_1"] = "";
			// $unit[0]["photo_list_2"] = "";
			// $unit[0]["images_count"] = sizeof($photos);
			// $unit[0]["js_images"] = "";
			// $unit[0]["image_nav"] = "";
			// $hasNofotos = true;
			// if (sizeof($photos)>0)
			// {
				// $hasNofotos = false;
				// $photos1 = array();
				// $photos2 = array();
				// for ($i=0;$i<sizeof($photos);$i++)
				// {
					// $photos[$i]["number"] = $i+1;
					// if ($i<4)
					// {
						// array_push($photos1,$photos[$i]);
					// }
					// else
					// {
						// array_push($photos2,$photos[$i]);
					// }
				// }
				// $imageNav = $this->GetControl("imageNav");
				// $photos[0]["url"] = $_SERVER['HTTP_HOST'];
				// $imageNav->dataSource = $photos[0];
				// $unit[0]["image_nav"] = $imageNav->Render();
				// $jsImageList = $this->GetControl("jsImageList");
				// $jsImageList->dataSource = $photos;
				// $unit[0]["js_images"]=$jsImageList->Render();
				// while (sizeof($photos1)<4)
				// {
					// array_push($photos1,array("alt"=>1));
				// }
				// $photoList1 = $this->GetControl("photoList1");
				// $photoList1->dataSource = $photos1;
				// $unit[0]["photo_list_1"] = $photoList1->Render();
				// if (sizeof($photos)>4)
				// {
					// while (sizeof($photos2)<4)
					// {
						// array_push($photos2,array("alt"=>1));
					// }
					// $photoList2 = $this->GetControl("photoList2");
					// $photoList2->dataSource = $photos2;
					// $unit[0]["photo_list_2"] = $photoList2->Render();
				// }
			// }
			// $unit[0]['images_title'] = (sizeof($photos)>0) ? '<h4 class="detailsBlockTitle"><a name="photos">Фото</a></h4>' : ''; 				
			// $unit[0]["foto_visible"] = $hasNofotos?"hidden":"visible";
			//end photos
			
			$photos = $this->GetControl("photos");
			$photos->dataSource = SQLProvider::ExecuteQuery( 
			  "select p.*
				  from `tbl__news2photo` ap
					join `tbl__photo` p on ap.photo_id = p.tbl_obj_id
					where ap.news_id=$id limit 8");
			$unit[0]["photos"] = $photos->Render();
			
			// var_dump($id); 
			// var_dump($news_date);
			//var_dump(substr($news_date, 0, 10));
			// die('!'); 2012-01-26
			// листалка статей
			if (!empty($news_date)) {				
				$unit[0]['show_news_1'] = $unit[0]['show_news_2'] = '';
				$sql = 'SELECT ns.tbl_obj_id, nd.title cat_title, ns.title, ns.annotation, ns.s_image, ns.date 
						FROM `vw__news_soon` ns 
						LEFT JOIN `tbl__news_dir` nd ON ns.tbl_cai_id = nd.tbl_obj_id 
						WHERE ns.active = 1 
						AND CAST(ns.date AS DATETIME) < CAST(\''.$news_date.'\' AS DATETIME) 
						ORDER BY ns.date DESC 
						LIMIT 1';				
				// && (!!!)CAST(ns.date AS DATETIME) - проектировщик базы редкостный мудак! запихнул поле date в varchar (2 часа убил на то, что - бы понять почему даты неправильно сравниваются)
				$news_rotate_1 = SQLProvider::ExecuteQuery($sql);
				if (!empty($news_rotate_1)) {				
					foreach($news_rotate_1 as $i => &$item) {
					
					  if( $item["title_url"] == '') { $item["title_url"] = 'details'.$item["tbl_obj_id"];}
		        $item["news_url_1"] = $item["title_url"];
					
						$unit[0]['news_link_1'] = $item['news_url_1'];
						$unit[0]['news_title_1'] = $item['title'];
						$unit[0]['news_img_1'] = $item['s_image'];
						$unit[0]['news_text_1'] = $item['annotation'];
						$unit[0]['news_date_1'] = date('d.m.Y', strtotime($item['date']));
						$unit[0]['news_category_1'] = !empty($item['cat_title']) ? '<small>Рубрика: '.$item['cat_title'].'</small>' : '';				
					}
				} else $unit[0]['show_news_1'] = 'noDisplay';
				
				$sql = 'SELECT ns.tbl_obj_id, nd.title cat_title, ns.title, ns.annotation, ns.s_image, ns.date 
						FROM `vw__news_soon` ns 
						LEFT JOIN `tbl__news_dir` nd ON ns.tbl_cai_id = nd.tbl_obj_id 
						WHERE ns.active = 1 
						AND CAST(ns.date AS DATETIME) > CAST(\''.$news_date.'\' AS DATETIME)  
						ORDER BY ns.date 
						LIMIT 1';   
				$news_rotate_2 = SQLProvider::ExecuteQuery($sql);
				if (!empty($news_rotate_2)) {				
					foreach($news_rotate_2 as $i => &$item) {
					
					  if( $item["title_url"] == '') { $item["title_url"] = 'details'.$item["tbl_obj_id"];}
		        $item["news_url_2"] = $item["title_url"];
					
						$unit[0]['news_link_2'] = $item['news_url_2'];
						$unit[0]['news_title_2'] = $item['title'];
						$unit[0]['news_img_2'] = $item['s_image'];
						$unit[0]['news_text_2'] = $item['annotation'];
						$unit[0]['news_date_2'] = date('d.m.Y', strtotime($item['date']));
						$unit[0]['news_category_2'] = !empty($item['cat_title']) ? '<small>Рубрика: '.$item['cat_title'].'</small>' : '';			
					}
				} else $unit[0]['show_news_2'] = 'noDisplay';
			}
			
			// случайные статьи
			$unit[0]['random_news'] = '';
			$sql  = 'SELECT ns.tbl_obj_id, nd.title cat_title, ns.title, ns.annotation, ns.s_image, ns.date '.
					'FROM `vw__news_soon` ns '.
						'LEFT JOIN `tbl__news_dir` nd ON ns.tbl_cai_id = nd.tbl_obj_id '.
					'WHERE ns.active = 1 '.
					'ORDER BY RAND() LIMIT 6';
			$random_news_list = SQLProvider::ExecuteQuery($sql);
			if(!empty($random_news_list)) {
				$unit[0]['random_news'] .= '<div id="random_news_block">';
				foreach($random_news_list as $i => &$item) {
					$unit[0]['random_news'] .=
						'<div class="rnb_item">'.
							'<a class="news" href="/news/details'.$item['tbl_obj_id'].'">'.
								'<img width="120" height="80" class="newsimg" alt="" src="/upload/'.$item['s_image'].'" />'.
							'</a>'.
							'<div class="rnb_item_text">'.
								'<b>'.$item['title'].'</b> <small>'.date('d.m.Y', strtotime($item['date'])).'</small>'.
								'<p>'.$item['annotation'].'</p>'.
								(!empty($item['cat_title']) ? '<small>Рубрика: '.$item['cat_title'].'</small>' : '').
							'</div>'.
						'</div>';
					if($i % 2 == 1) {
						$unit[0]['random_news'] .= '<div class="clear">&nbsp;</div>';
					}
				}
				$unit[0]['random_news'] .= '</div>';
			}
			
			$unit[0]["newstype"] = "news";
			$contDetails = $this->GetControl("details");
			$contDetails->dataSource = $unit[0];	
			
			
			
			// $leftMenuWitgets = $this->GetControl("leftMenuWitgets");
			// $leftMenuWitgets->dataSource
			
			}
			
			
		}
	}
?>
