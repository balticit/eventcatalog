<?php
	class news_details_php extends CPageCodeHandler 
	{
		public function news_details_php()
		{
			$this->CPageCodeHandler();
		}
		
		public function PreRender()
		{
			$app = CApplicationContext::GetInstance();
      $av_rwParams = array("id");
		  CURLHandler::CheckRewriteParams($av_rwParams);  
			$rewriteParams = array();
			$id = GP("id");
			if (!is_numeric($id))
			{
				$id =0;
			}
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
			 
			
			// ������ ��������� ��������
			$sql  = 'SELECT tbl_obj_id, title '.
					'FROM tbl__news_dir '.
					'ORDER BY sort DESC';
			$list = SQLProvider::ExecuteQuery($sql);
			if(!empty($list)) {
				foreach($list as $i => &$item) {
					$item['link']		= "/news/?cat=".$item['tbl_obj_id'];
					$item['gray']		= "gray";
					$item['selected']	= ($item['tbl_obj_id'] == $unit[0]['tbl_cai_id']) ? 'class="selected"' : '';
				}
				$list[] = array(
					'tbl_obj_id'	=> 0,
					'title'			=> '��� �������',
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
			$unit[0]['date'] = !empty($unit[0]['date']) ?  '<div class="newsParam"><b>���� �������:</b> '.date("d.m.Y",strtotime($unit[0]['date'])).' �.</div>' : '';
			$unit[0]['site'] = !empty($unit[0]['site']) ?  '<div class="newsParam"><b>���� ������������: </b><a href="'.$unit[0]['site'].'" target="_blank">'.$unit[0]['site'].'</a></div>' : '';
			$unit[0]['type'] = !empty($unit[0]['type']) ?  '<div class="newsParam"><b>��� �����������:</b> '.$unit[0]['type'].'</div>' : '';
			$unit[0]['city'] = !empty($unit[0]['city_title']) ?  '<div class="newsParam"><b>�����:</b> '.$unit[0]['city_title'].'</div>' : '';
			$unit[0]['area'] = !empty($unit[0]['area_title']) ?  '<div><b>��������:</b> '.$unit[0]['area_title'].'</div>' : '';
			
			
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
			// $unit[0]['images_title'] = (sizeof($photos)>0) ? '<h4 class="detailsBlockTitle"><a name="photos">����</a></h4>' : ''; 				
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
			// �������� ������
			if (!empty($news_date)) {				
				$unit[0]['show_news_1'] = $unit[0]['show_news_2'] = '';
				$sql = 'SELECT ns.tbl_obj_id, nd.title cat_title, ns.title, ns.annotation, ns.s_image, ns.date 
						FROM `vw__news_soon` ns 
						LEFT JOIN `tbl__news_dir` nd ON ns.tbl_cai_id = nd.tbl_obj_id 
						WHERE ns.active = 1 
						AND CAST(ns.date AS DATETIME) < CAST(\''.$news_date.'\' AS DATETIME) 
						ORDER BY ns.date DESC 
						LIMIT 1';				
				// && (!!!)CAST(ns.date AS DATETIME) - ������������� ���� ���������� �����! �������� ���� date � varchar (2 ���� ���� �� ��, ��� - �� ������ ������ ���� ����������� ������������)
				$news_rotate_1 = SQLProvider::ExecuteQuery($sql);
				if (!empty($news_rotate_1)) {				
					foreach($news_rotate_1 as $i => &$item) {
						$unit[0]['news_link_1'] = $item['tbl_obj_id'];
						$unit[0]['news_title_1'] = $item['title'];
						$unit[0]['news_img_1'] = $item['s_image'];
						$unit[0]['news_text_1'] = $item['annotation'];
						$unit[0]['news_date_1'] = date('d.m.Y', strtotime($item['date']));
						$unit[0]['news_category_1'] = !empty($item['cat_title']) ? '<small>�������: '.$item['cat_title'].'</small>' : '';				
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
						$unit[0]['news_link_2'] = $item['tbl_obj_id'];
						$unit[0]['news_title_2'] = $item['title'];
						$unit[0]['news_img_2'] = $item['s_image'];
						$unit[0]['news_text_2'] = $item['annotation'];
						$unit[0]['news_date_2'] = date('d.m.Y', strtotime($item['date']));
						$unit[0]['news_category_2'] = !empty($item['cat_title']) ? '<small>�������: '.$item['cat_title'].'</small>' : '';			
					}
				} else $unit[0]['show_news_2'] = 'noDisplay';
			}
			
			// ��������� ������
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
								(!empty($item['cat_title']) ? '<small>�������: '.$item['cat_title'].'</small>' : '').
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
?>
