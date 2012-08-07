<?php
class book_details_php extends CPageCodeHandler
{
	public function book_details_php()
	{
		$this->CPageCodeHandler();
	}

	public function PreRender()
	{
        $av_rwParams = array("id");
        CURLHandler::CheckRewriteParams($av_rwParams);
        $id_str = GP("id");
        /*��� ��������� ������ ������, �� �������� ����� ����� ������*/
        /*
        if (preg_match('/^details\d+$/i', $id_str)) {
          $tmp_id = str_replace('details', '', $id_str);
          if (is_numeric($tmp_id)){
            $id_str = CURLHandler::GetTranslitID('public', $tmp_id);
            CURLHandler::Redirect301('/book/'.$id_str);
          }
        }
        */
        if (!is_null($id_str)){
            $id = SQLProvider::ExecuteScalar("select tbl_obj_id from tbl__public_doc where title_url = '".mysql_real_escape_string($id_str)."'");
        }
        if (!is_numeric($id))
            CURLHandler::ErrorPage();

		$active_check = SQLProvider::ExecuteScalar("select active from tbl__public_doc where tbl_obj_id=$id");
		
		if($active_check<1){
			CURLHandler::ErrorPage();
		}
		
		$publics = SQLProvider::ExecuteQuery("select * from tbl__public_doc where tbl_obj_id=$id");
		if (sizeof($publics)==0)
		{
			CURLHandler::ErrorPage();
		}
		$l_topics = SQLProvider::ExecuteQuery(
		"SELECT pt.tbl_obj_id, pt.title
		FROM tbl__public2topic p2t join tbl__public_topics pt on p2t.parent_id = pt.tbl_obj_id
		WHERE p2t.child_id = $id order by pt.order_num");
		$publics[0]['topic_links'] = '<span style="color:#6096CA; font-size: 10pt; font-weight: normal;">';
		foreach ($l_topics as $l_top)
			$publics[0]['topic_links'] .= '<a href="/book/?topic='.$l_top['tbl_obj_id'].'">'.$l_top['title'].'</a>&nbsp;&nbsp;';
		$publics[0]['topic_links'] .= '</span>';
		$publics[0]['formatted_date'] = '<span style="font-size: 10pt; font-weight: normal;">'.date("d.m.Y",strtotime($publics[0]["registration_date"])).'</span>';
		$details = $this->GetControl("details");

		$topics = SQLProvider::ExecuteQuery("select * from tbl__public_topics order by order_num");
        array_unshift($topics,array("tbl_obj_id"=>"0","title"=>"��� ������"));
        foreach ($topics as &$item) {
            if ($item['tbl_obj_id'] == 0)
                $item['link'] = "/book";
            else
                $item['link'] = "/book/?topic=".$item['tbl_obj_id'];
            $item['gray'] = "gray";
            $item['selected'] = "";
        }
		$topicList = $this->GetControl("topicList");
		$topicList->dataSource = $topics;

		$lastBookItems = SQLProvider::ExecuteQuery("select * from tbl__public_doc
												where `active`=1
												order by rand() desc limit 5");
	$last_book = $this->GetControl("last_book");
	for ($i=0;$i<sizeof($lastBookItems);$i++)
	{
		$lastBookItems[$i]["title"] = CutString($lastBookItems[$i]["title"]);
        if(!$lastBookItems[$i]["title_url"])
        {
            $lastBookItems[$i]["title_url"] = SQLProvider::ExecuteScalar("select title_url from tbl_public_doc where tbl_obj_id=$lastBookItems[$i]['tbl_obj_id']");
        }
	}
	$last_book->dataSource = $lastBookItems;
        
        
        /* eventtv menu */
            $topics1 = SQLProvider::ExecuteQuery("select tbl_obj_id, title, color from tbl__eventtv_topics order by group_num, order_num");
            foreach ($topics1 as &$item) {
                if ($item['tbl_obj_id'] == 0)
                    $item['link'] = "/eventtv";
                else
                    $item['link'] = "/eventtv/?topic=".$item['tbl_obj_id'];
                $item['gray'] = "gray";
                $item['selected'] =  "";
            }
            $tvTopicList = $this->GetControl("tvTopicList");
            $tvTopicList->dataSource = $topics1;
            /**/
				//��������
				$news_date = $publics[0]["registration_date"];
		if (!empty($news_date)) {				
				$publics[0]['show_news_1'] = $publics[0]['show_news_2'] = '';
				$sql = 'SELECT * from tbl__public_doc dc where active=1 
						AND dc.registration_date < CAST(\''.$news_date.'\' AS DATETIME) 
						ORDER BY dc.registration_date DESC 
						LIMIT 1';				
				// && (!!!)CAST(ns.date AS DATETIME) - ������������� ���� ���������� �����! �������� ���� date � varchar (2 ���� ���� �� ��, ��� - �� ������ ������ ���� ����������� ������������)
				$news_rotate_1 = SQLProvider::ExecuteQuery($sql);
				if (!empty($news_rotate_1)) {				
					foreach($news_rotate_1 as $i => &$item) {
						$publics[0]['news_link_1'] = $item['title_url'];
						$publics[0]['news_title_1'] = $item['title'];
						$publics[0]['news_img_1'] = $item['logo_image'];
						$publics[0]['news_text_1'] = $item['annotation'];
						$publics[0]['news_date_1'] = date('d.m.Y', strtotime($item['registration_date']));
						$publics[0]['news_category_1'] = !empty($item['cat_title']) ? '<small>�������: '.$item['cat_title'].'</small>' : '';				
					}
				} else $publics[0]['show_news_1'] = 'noDisplay';
				
				$sql = 'SELECT * from tbl__public_doc dc where active=1 
						AND dc.registration_date > CAST(\''.$news_date.'\' AS DATETIME) 
						ORDER BY dc.registration_date DESC 
						LIMIT 1';		  
				$news_rotate_2 = SQLProvider::ExecuteQuery($sql);
				if (!empty($news_rotate_2)) {				
					foreach($news_rotate_2 as $i => &$item) {
						$publics[0]['news_link_2'] =$item['title_url'];						
						$publics[0]['news_title_2'] = $item['title'];
						$publics[0]['news_img_2'] = $item['logo_image'];
						$publics[0]['news_text_2'] = $item['annotation'];
						$publics[0]['news_date_2'] = date('d.m.Y', strtotime($item['registration_date']));
						$publics[0]['news_category_2'] = !empty($item['cat_title']) ? '<small>�������: '.$item['cat_title'].'</small>' : '';			
					}
				} else $publics[0]['show_news_2'] = 'noDisplay';
			}
			// ��������� ������
			$publics[0]['random_news'] = '';
			$sql  = 'SELECT * from tbl__public_doc dc where active=1 ORDER BY RAND() LIMIT 6';
			$random_news_list = SQLProvider::ExecuteQuery($sql);
			if(!empty($random_news_list)) {
				$publics[0]['random_news'] .= '<div id="random_news_block">';
				foreach($random_news_list as $i => &$item) {
					$publics[0]['random_news'] .=
						'<div class="rnb_item">'.
							'<a class="news" href="/book/'.$item['title_url'].'">'.
								'<img width="120" height="80" class="newsimg" alt="" src="/upload/'.$item['logo_image'].'" />'.
							'</a>'.
							'<div class="rnb_item_text">'.
								'<b><a class="news" href="/book/'.$item['title_url'].'">'.$item['title'].'</a></b> '.
								'<p>'.$item['annotation'].'</p>'.
								(!empty($item['cat_title']) ? '<small>�������: '.$item['cat_title'].'</small>' : '').
							'</div>'.
						'</div>';
					if($i % 2 == 1) {
						$publics[0]['random_news'] .= '<div class="clear">&nbsp;</div>';
					}
				}
				$publics[0]['random_news'] .= '</div>';
			}
			$details->dataSource = $publics[0];

		
		// && ����� ����������
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
