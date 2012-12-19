<?php
class eventtv_details_php extends CPageCodeHandler
{
  public $pageSize = 25;

	public function eventtv_details_php()
	{
		$this->CPageCodeHandler();
	}

	public function PreRender()
	{
	
	
/*������� ������*/
$id_str = GP("id");
if (!is_null($id_str)) {
  $this->id = SQLProvider::ExecuteScalar("select tbl_obj_id from tbl__eventtv_topics where title_url = '" . mysql_real_escape_string($id_str) . "'");
  if(!IsNullOrEmpty($this->id)){
    $topic = $this->id;
    $this->is_list = true;
  }
  else{
    $this->is_list = false;
  }
}
	
/////////////������ ������        
if($this->is_list){

    $av_rwParams = array("bycategory","page");
    CURLHandler::CheckRewriteParams($av_rwParams);
    
    
    $rewriteParams = $_GET;
    $page = GP("page", 1);
    $pages = 0;
    $rp = ($page - 1) * $this->pageSize;
    
    $bycategory = GP("bycategory");
    $topic = $this->id;
    //$topic = GP("topic",0);
    $topics = SQLProvider::ExecuteQuery("select tbl_obj_id, title, title_url, color from tbl__eventtv_topics order by group_num, order_num");
    array_unshift($topics,array("tbl_obj_id"=>"0","title"=>"��� ���������"));
    foreach ($topics as &$item) {
      if ($item['tbl_obj_id'] == 0)
        $item['link'] = "/eventtv";
      else
        $item['link'] = "/eventtv/".$item['title_url'];
      $item['gray'] = ($item['tbl_obj_id'] == $topic)?"":"gray";
      $item['selected'] =  ($item['tbl_obj_id'] == $topic)?'id="selectGray"':"";
    }
    $topicList = $this->GetControl("topicList");
    $topicList->dataSource = $topics;

    $html = "";
    $itemTemplate = $this->getControl("bookItem");
    
    
    
        
    
    
    /* book menu */
    $topics1 = SQLProvider::ExecuteQuery("select tbl_obj_id, title_url, title from tbl__public_topics order by order_num");
            array_unshift($topics1,array("tbl_obj_id"=>"0","title"=>"��� ������"));
            foreach ($topics1 as &$item) {
                if ($item['tbl_obj_id'] == 0)
                    $item['link'] = "/book";
                else
                    $item['link'] = "/book/".$item['title_url'];
                $item['gray'] = ($item['tbl_obj_id'] == $topic)?"":"gray";
                $item['selected'] =  ($item['tbl_obj_id'] == $topic)?'id="selectGray"':"";
            }
            $topicList = $this->GetControl("bookTopicsList");
            $topicList->dataSource = $topics1;


    if (!is_null($bycategory) || (!IsNullOrEmpty($topic) )) {
      $filter = (!IsNullOrEmpty($topic) && is_numeric($topic))?" where dir_id=$topic":"";

      // $publics = SQLProvider::ExecuteQuery(
        // "select tbl_obj_id, title,title_url, logo_image, annotation, dir_id,
        // registration_date, case when DATEDIFF(NOW(),registration_date)<10 then 1 else 0 end is_new
        // from vw__eventtv_doc $filter");
			// echo "select 
					// tbl_obj_id, title,title_url, annotation, registration_date, is_new, dir_id, logo_image,	 				
					// DATE_FORMAT(registration_date,'%d.%m.%y') formatted_date, topics
				// from (
					// select 
						// tbl_obj_id, title,title_url, annotation, registration_date, dir_id, is_new, logo_image, 
						// (select GROUP_CONCAT(pt.title SEPARATOR ', ')
									// from tbl__eventtv2topic p2t 
									// join tbl__eventtv_topics pt on p2t.parent_id = pt.tbl_obj_id
									// where p2t.child_id = tbl_obj_id
									// order by pt.order_num) topics
					// from tbl__eventtv_doc pd where $filter  					
					// ) t
				// order by is_new desc, registration_date desc";
				// die('!');	
				
	
	 $publics = SQLProvider::ExecuteQuery("
			select tbl_obj_id, title,title_url, logo_image, annotation, dir_id, 
			(select GROUP_CONCAT(pt.title SEPARATOR ', ')
									from tbl__eventtv2topic p2t 
									join tbl__eventtv_topics pt on p2t.parent_id = pt.tbl_obj_id
									where p2t.child_id = tbl_obj_id
									order by pt.order_num) topics,
			registration_date, case when DATEDIFF(NOW(),registration_date)<10 then 1 else 0 end is_new
			from vw__eventtv_doc $filter order by is_new desc, registration_date desc limit $rp,$this->pageSize
	 ");
	 
	 $count = SQLProvider::ExecuteQuery("
			select tbl_obj_id, title,title_url, logo_image, annotation, dir_id, 
			(select GROUP_CONCAT(pt.title SEPARATOR ', ')
									from tbl__eventtv2topic p2t 
									join tbl__eventtv_topics pt on p2t.parent_id = pt.tbl_obj_id
									where p2t.child_id = tbl_obj_id
									order by pt.order_num) topics,
			registration_date, case when DATEDIFF(NOW(),registration_date)<10 then 1 else 0 end is_new
			from vw__eventtv_doc $filter order by is_new desc, registration_date desc 
	 ");
	 
	 $count = count($count);
	 $pages = floor($count / $this->pageSize) + (($count % $this->pageSize == 0) ? 0 : 1);

      foreach ($topics as $topic) {
        $docs = array();
        foreach ($publics as $doc) {
          if ($doc['dir_id'] == $topic['tbl_obj_id']) {
            $docs[] = $doc;
          }
        }

        if (count($docs)>0) {
          $html .= "<div><b>".$topic['title']."</b></div>\n";

          $html .= '<table cellspacing="0" cellpadding="0" border="0" style="margin-top:10px">'; 

          foreach ($docs as $doc) {
            $doc['isNew'] = "";
            if ($doc['is_new']==1) {
              $doc['isNew'] = "&nbsp;&nbsp;<span class='isNew'>(�����!)</span>";
            }
            $doc['formatted_date'] = date("d.m.Y",strtotime($doc["registration_date"]));
			$doc['link'] = "eventtv";
            $itemTemplate->dataSource = $doc;

            $html .= $itemTemplate->Render();
          }

          $html .= "</table>";
        }
      }
      $sortLinks = '<a href="/eventtv">�� ����</a>&nbsp;&nbsp;&nbsp;<a class="currentSort" href="/eventtv?bycategory">�� ���������</a>';
    }
    else {
      // $publics = SQLProvider::ExecuteQuery(
        // "select tbl_obj_id, title, title_url, logo_image, annotation, registration_date,
          // case when DATEDIFF(NOW(),registration_date)<10 then 1 else 0 end is_new
        // from tbl__eventtv_doc where active = 1 order by registration_date desc");
		
		$publics = SQLProvider::ExecuteQuery("
			select 
					tbl_obj_id, title,title_url, annotation, text, registration_date, is_new, logo_image, link,					
					DATE_FORMAT(registration_date,'%d.%m.%y') formatted_date, topics
				from (
					select 
						tbl_obj_id, title,title_url, annotation, text, registration_date, is_new, logo_image, 'eventtv' link,
						(select GROUP_CONCAT(pt.title SEPARATOR ', ')
									from tbl__eventtv2topic p2t 
									join tbl__eventtv_topics pt on p2t.parent_id = pt.tbl_obj_id
									where p2t.child_id = tbl_obj_id
									order by pt.order_num) topics
					from tbl__eventtv_doc pd where active = 1 					
					) t
				order by is_new desc, registration_date desc limit $rp,$this->pageSize
		
		");
		
		$count = SQLProvider::ExecuteQuery("
			select 
					tbl_obj_id, title,title_url, annotation, text, registration_date, is_new, logo_image, link,					
					DATE_FORMAT(registration_date,'%d.%m.%y') formatted_date, topics
				from (
					select 
						tbl_obj_id, title,title_url, annotation, text, registration_date, is_new, logo_image, 'eventtv' link,
						(select GROUP_CONCAT(pt.title SEPARATOR ', ')
									from tbl__eventtv2topic p2t 
									join tbl__eventtv_topics pt on p2t.parent_id = pt.tbl_obj_id
									where p2t.child_id = tbl_obj_id
									order by pt.order_num) topics
					from tbl__eventtv_doc pd where active = 1 					
					) t
				order by is_new desc, registration_date desc
		
		");
		
		$count = count($count);
		$pages = floor($count / $this->pageSize) + (($count % $this->pageSize == 0) ? 0 : 1);
     

	 $html .= '<table cellspacing="0" cellpadding="0" border="0" style="margin-top:10px">';
      foreach ($publics as $doc) {
        $doc['isNew'] = "";
        if ($doc['is_new']==1) {
          $doc['isNew'] = "&nbsp;&nbsp;<span class='isNew'>(�����!)</span>";
        }
        $doc['formatted_date'] =  date("d.m.Y",strtotime($doc["registration_date"]));
        $itemTemplate->dataSource = $doc;

        $html .= $itemTemplate->Render();
      }
      $html .= "</table>";
      $sortLinks = '<a class="currentSort" href="/eventtv">�� ����</a>&nbsp;&nbsp;&nbsp;<a href="/eventtv?bycategory">�� ���������</a>';
    }


    $publicList = $this->GetControl("publicList");
    $publicList->html = $html;
    
    
    //setting pager
    $pager = $this->GetControl("pager");
    $pager->currentPage = $page;
    $pager->totalPages = $pages;
    $pager->rewriteParams = $rewriteParams;
    

    $topic = GP("topic");
    $sortTypes = $this->GetControl("sortTypes");
    if (!IsNullOrEmpty($topic))
        $sortTypes->html = "";
    else
        $sortTypes->html = "<div class=\"sortTypes\" style=\"color:#6096CA;margin-bottom:10px\">&nbsp;$sortLinks</div>";
  
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


/////////////������

else {	

	
		$av_rwParams = array("id");
		CURLHandler::CheckRewriteParams($av_rwParams);
        $id_str = GP("id");
        /*��� ��������� ������ ������, �� �������� ����� ����� ������*/
        if (preg_match('/^details\d+$/i', $id_str)) {
          $tmp_id = str_replace('details', '', $id_str);
          if (is_numeric($tmp_id)){
            $id_str = CURLHandler::GetTranslitID('eventtv', $tmp_id);
            CURLHandler::Redirect301('/eventtv/'.$id_str);
          }
        }
        if (!is_null($id_str)){
            $id = SQLProvider::ExecuteScalar("select tbl_obj_id from tbl__eventtv_doc where title_url = '".mysql_real_escape_string($id_str)."'");
        }
        if (!is_numeric($id))
            CURLHandler::ErrorPage();
		
		$active_check = SQLProvider::ExecuteScalar("select active from tbl__eventtv_doc where tbl_obj_id=$id");
		
		if($active_check<1){
			CURLHandler::ErrorPage();
		}
		
		$publics = SQLProvider::ExecuteQuery("select * from tbl__eventtv_doc where tbl_obj_id=$id");
		
        if (sizeof($publics)==0)
		{
			CURLHandler::Redirect("/");
		}
		$l_topics = SQLProvider::ExecuteQuery(
		"SELECT pt.tbl_obj_id, pt.title
		FROM tbl__eventtv2topic p2t join tbl__eventtv_topics pt on p2t.parent_id = pt.tbl_obj_id
		WHERE p2t.child_id = $id order by pt.order_num");
		$publics[0]['topic_links'] = '<span style="color:#6096CA; font-size: 10pt; font-weight: normal;">';
		foreach ($l_topics as $l_top)
			$publics[0]['topic_links'] .= '<a href="/eventtv/?topic='.$l_top['tbl_obj_id'].'">'.$l_top['title'].'</a>&nbsp;&nbsp;';
		$publics[0]['topic_links'] .= '</span>';
		$publics[0]['formatted_date'] = '<span style="font-size: 10pt; font-weight: normal;">'.date("d.m.Y",strtotime($publics[0]["registration_date"])).'</span>';
		$details = $this->GetControl("details");

		$user = new CSessionUser("user");
		CAuthorizer::AuthentificateUserFromCookie(&$user);
		CAuthorizer::RestoreUserFromSession(&$user);
		$show_ilike = true;
		$btn_action = "ShowLikeMessage(); return false;";

		if ($user->authorized) {
			$btn_action = "$(this).parent().submit();";
			$currentmark = SQLProvider::ExecuteScalar("select count(1) from tbl__userlike where
			to_resident_type='eventtv' and to_resident_id='$id' and from_resident_type='".$user->type."' and from_resident_id='".$user->id."'");
			$show_ilike = $currentmark==0;
			if ($show_ilike and GP('action')=='ilike') {
				SQLProvider::ExecuteQuery("
						insert into tbl__userlike
						(to_resident_id,to_resident_type,from_resident_id,from_resident_type,date)
						values
						($id,'eventtv',".$user->id.",'".$user->type."',FROM_UNIXTIME(".time()."))
						");
				header("location: /eventtv/details/id/".$id);
			}
			if (!$show_ilike and GP('action')=='unlike') {
				SQLProvider::ExecuteQuery("
								delete from tbl__userlike
								where to_resident_id = $id
									and to_resident_type = 'eventtv'
									and from_resident_id = ".$user->id."
									and from_resident_type = '".$user->type."'");
				header("location: /eventtv/details/id/".$id);
			}
		}
		if ($show_ilike) {
			$btn_i_like='<form method="post" style="margin: 0; padding:0;"><input type="hidden" name="action" value="ilike"><img class="btn_ilike" src="/images/rating/btn_ilike.png" onmouseover="javascript: this.src=\'/images/rating/btn_white.png\';" onmouseout="javascript: this.src=\'/images/rating/btn_ilike.png\';" onclick="javascript: '.$btn_action.'"></form>';
		}
		else {
			$btn_i_like='</td><td><form method="post" style="margin: 0; padding:0;"><input type="hidden" name="action" value="unlike"><a href="" class="black" onclick="javascript: $(this).parent().submit(); return false;"><img onmouseover="javascript: this.src=\'/images/rating/unlike_white.png\';" onmouseout="javascript: this.src=\'/images/rating/btn_unlike.png\';" src="/images/rating/btn_unlike.png" alt="������ �� ����������" /></a></form>';
		}

		$mark = SQLProvider::ExecuteQuery("select au.user_id,au.type,au.title from tbl__userlike ul
																			 join vw__all_users_full au
																					 on au.type = ul.from_resident_type and
																							au.user_id = ul.from_resident_id
																			 where to_resident_type='eventtv' and to_resident_id='$id'");
		$mark_cnt = 0;
		$mark_links = "";
		foreach($mark as $m_item) {
			$mark_cnt++;
			if ($mark_links)
				$mark_links .= ", ";
			$mark_links .= '<a class="user_like_link" href="/u_profile/type/'.$m_item['type'].'/id/'.$m_item['user_id'].'">'.$m_item['title'].'</a>';
		}
		$voted = "";
		if ($mark_cnt>0) {
			$u_text = "�������������";
			if ($mark_cnt == 1)
				$u_text = "������������";

			$voted = "<div class=\"user_liked\">�������� <span class=\"user_liked_num\">$mark_cnt</span> $u_text:<br><span class=\"user_like_link\">$mark_links</span></div>";
		}
		$rating = $this->GetControl("rating");
		$rating->dataSource = array(
			"btn_i_like" => $btn_i_like,
			"voted" => $voted,
			"res_type" => "eventtv"
		);

		$publics[0]['rating'] = $rating->Render();

		$topics = SQLProvider::ExecuteQuery("select * from tbl__eventtv_topics order by group_num, order_num");
        array_unshift($topics,array("tbl_obj_id"=>"0","title"=>"��� ���������"));
        foreach ($topics as &$item) {
            if ($item['tbl_obj_id'] == 0)
                $item['link'] = "/eventtv";
            else
                $item['link'] = "/eventtv/".$item['title_url'];
            $item['gray'] = "gray";
            $item['selected'] = "";
        }
		$topicList = $this->GetControl("topicList");
		$topicList->dataSource = $topics;
		$bookTopics = SQLProvider::ExecuteQuery("select tbl_obj_id, title from tbl__public_topics order by order_num");
		foreach ($bookTopics as &$item) {
                if ($item['tbl_obj_id'] == 0)
                    $item['link'] = "/book";
                else
                    $item['link'] = "/book/".$item['title_url'];
                $item['gray'] = "gray";
                $item['selected'] =  "";
            }
        $bookTopicsList = $this->GetControl("bookTopicsList");
        $bookTopicsList->dataSource = $bookTopics;
		
		$lastBookItems = SQLProvider::ExecuteQuery("select * from tbl__eventtv_doc
												where `active`=1
												order by rand() desc limit 5");
	$last_book = $this->GetControl("last_book");
	for ($i=0;$i<sizeof($lastBookItems);$i++)
	{
		$lastBookItems[$i]["title"] = CutString($lastBookItems[$i]["title"]);
	}
	$last_book->dataSource = $lastBookItems;
		$news_date = $publics[0]["registration_date"];
		//��������
		if (!empty($news_date)) {				
				$publics[0]['show_news_1'] = $publics[0]['show_news_2'] = '';
				$sql = 'SELECT * from tbl__eventtv_doc dc where active=1 
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
				
				$sql = 'SELECT * from tbl__eventtv_doc dc where active=1 
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
			$sql  = 'SELECT * from tbl__eventtv_doc dc where active=1 ORDER BY RAND() LIMIT 6';
			$random_news_list = SQLProvider::ExecuteQuery($sql);
			if(!empty($random_news_list)) {
				$publics[0]['random_news'] .= '<div id="random_news_block">';
				foreach($random_news_list as $i => &$item) {
					$publics[0]['random_news'] .=
						'<div class="rnb_item">'.
							'<a class="news" href="/eventtv/'.$item['title_url'].'">'.
								'<img width="120" height="80" class="newsimg" alt="" src="/upload/'.$item['logo_image'].'" />'.
							'</a>'.
							'<div class="rnb_item_text">'.
								'<b>'.$item['title'].'</b> <small>'.date('d.m.Y', strtotime($item['registration_date'])).'</small>'.
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
}
?>
