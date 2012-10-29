<?php
    class book_php extends CPageCodeHandler
    {
        public $pageSize = 25;
        
        public function book_php()
        {
            $this->CPageCodeHandler();
        }

        public function PreRender()
        {
        
        

          
        
        
            $av_rwParams = array("bycategory","topic","page");
            CURLHandler::CheckRewriteParams($av_rwParams);
            
            $rewriteParams = $_GET;
            $page = GP("page", 1);
            $pages = 0;
            $rp = ($page - 1) * $this->pageSize;
            
	        $bycategory = GP("bycategory");
            $topic = GP("topic",0);
            $topics = SQLProvider::ExecuteQuery("select tbl_obj_id, title from tbl__public_topics order by order_num");
            array_unshift($topics,array("tbl_obj_id"=>"0","title"=>"��� ������"));
            foreach ($topics as &$item) {
                if ($item['tbl_obj_id'] == 0)
                    $item['link'] = "/book";
                else
                    $item['link'] = "/book/?topic=".$item['tbl_obj_id'];
                $item['gray'] = ($item['tbl_obj_id'] == $topic)?"":"gray";
                $item['selected'] =  ($item['tbl_obj_id'] == $topic)?'id="selectGray"':"";
            }
            $topicList = $this->GetControl("topicList");
            $topicList->dataSource = $topics;
            
            
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
            

            $html = "";
            $itemTemplate = $this->getControl("bookItem");


            if (!is_null($bycategory) || (!IsNullOrEmpty($topic) && is_numeric($topic))) {
                $filter = (!IsNullOrEmpty($topic) && is_numeric($topic))?" where pd.dir_id=$topic":"";

                // $publics = SQLProvider::ExecuteQuery(
                  // "select tbl_obj_id, title,title_url, logo_image, annotation, dir_id,
                  // registration_date,
                  // case when DATEDIFF(NOW(),registration_date)<10 then 1 else 0 end is_new
                  // from vw__public_doc $filter");
				  
			$publics = SQLProvider::ExecuteQuery("  
				 select 
					tbl_obj_id, title,title_url, annotation, is_new, logo_image, dir_id, is_new, 				
					DATE_FORMAT(registration_date,'%d.%m.%y') formatted_date, topics
				from (
				
					select 
						tbl_obj_id, title,title_url, annotation, registration_date, dir_id, is_new, logo_image,
						(select GROUP_CONCAT(pt.title SEPARATOR ', ')
									from tbl__public2topic p2t 
									join tbl__public_topics pt on p2t.parent_id = pt.tbl_obj_id
									where p2t.child_id = pd.tbl_obj_id
									order by pt.order_num) topics 
					from vw__public_doc pd $filter
					) t
				order by is_new desc, registration_date desc   limit $rp,$this->pageSize 
			");
			
			$count = SQLProvider::ExecuteQuery("  
				 select 
					tbl_obj_id, title,title_url, annotation, is_new, logo_image, dir_id, is_new, 				
					DATE_FORMAT(registration_date,'%d.%m.%y') formatted_date, topics
				from (
				
					select 
						tbl_obj_id, title,title_url, annotation, registration_date, dir_id, is_new, logo_image,
						(select GROUP_CONCAT(pt.title SEPARATOR ', ')
									from tbl__public2topic p2t 
									join tbl__public_topics pt on p2t.parent_id = pt.tbl_obj_id
									where p2t.child_id = pd.tbl_obj_id
									order by pt.order_num) topics 
					from vw__public_doc pd $filter
					) t
				order by is_new desc, registration_date desc  
			");
			
			$count = count($count);
      $pages = floor($count / $this->pageSize) + (($count % $this->pageSize == 0) ? 0 : 1);
			
				// echo " select 
					// tbl_obj_id, title,title_url, annotation, is_new, logo_image, 					
					// DATE_FORMAT(registration_date,'%d.%m.%y') formatted_date, topics
				// from (
				
					// select 
						// tbl_obj_id, title,title_url, annotation, registration_date, is_new, logo_image,
						// (select GROUP_CONCAT(pt.title SEPARATOR ', ')
									// from tbl__public2topic p2t 
									// join tbl__public_topics pt on p2t.parent_id = pt.tbl_obj_id
									// where p2t.child_id = pd.tbl_obj_id
									// order by pt.order_num) topics 
					// from vw__public_doc pd $filter
					// ) t
				// order by is_new desc, registration_date desc  ";
				// die('!');
				//die('!');
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
							$doc['link'] = "book";
                            $itemTemplate->dataSource = $doc;

                            $html .= $itemTemplate->Render();
                        }

                        $html .= "</table>";
                    }
                }
                $sortLinks = '<a href="/book">�� ����</a>&nbsp;&nbsp;&nbsp;<a class="currentSort" href="/book/?bycategory">�� ���������</a>';
            }
            else { 
				//die('!');
                // $publics = SQLProvider::ExecuteQuery(
                  // "select tbl_obj_id, title,title_url, logo_image, annotation,
                  // registration_date,
                  // case when DATEDIFF(NOW(),registration_date)<10 then 1 else 0 end is_new
                  // from tbl__public_doc where active = 1 order by registration_date desc");
				  
				 $publics = SQLProvider::ExecuteQuery("
				 select 
					tbl_obj_id, title,title_url, annotation, text, registration_date, is_new, logo_image, link,					
					DATE_FORMAT(registration_date,'%d.%m.%y') formatted_date, topics
				from (
				
					select 
						tbl_obj_id, title,title_url, annotation, text, registration_date, is_new, logo_image, 'book' link,
						(select GROUP_CONCAT(pt.title SEPARATOR ', ')
									from tbl__public2topic p2t 
									join tbl__public_topics pt on p2t.parent_id = pt.tbl_obj_id
									where p2t.child_id = pd.tbl_obj_id
									order by pt.order_num) topics 
					from tbl__public_doc pd where active = 1
					) t
				order by is_new desc, registration_date desc limit $rp,$this->pageSize
			");
			
			$count = SQLProvider::ExecuteQuery("
				 select 
					tbl_obj_id, title,title_url, annotation, text, registration_date, is_new, logo_image, link,					
					DATE_FORMAT(registration_date,'%d.%m.%y') formatted_date, topics
				from (
				
					select 
						tbl_obj_id, title,title_url, annotation, text, registration_date, is_new, logo_image, 'book' link,
						(select GROUP_CONCAT(pt.title SEPARATOR ', ')
									from tbl__public2topic p2t 
									join tbl__public_topics pt on p2t.parent_id = pt.tbl_obj_id
									where p2t.child_id = pd.tbl_obj_id
									order by pt.order_num) topics 
					from tbl__public_doc pd where active = 1
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
                $sortLinks = '<a class="currentSort" href="/book">�� ����</a>&nbsp;&nbsp;&nbsp;<a href="/book/?bycategory">�� ���������</a>';
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
    }
?>
