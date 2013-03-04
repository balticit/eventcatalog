<?php
    class eventoteka_php extends CPageCodeHandler
    {
        public $sort = 0;
        public $pageSize = 25;

				public function eventoteka_php()
        {
            $this->CPageCodeHandler();
        }

        public function PreRender()
        {
						$metadata = $this->GetControl("metadata");
						$metadata->keywords = "ивент, мероприятие, эвентотека";
						$metadata->description = "Статьи и видео с различных мероприятий. Эвентотека включает в себя интересные ивент-события, новости и проекты для отдыха.";
            
            
        $rewriteParams = $_GET;
        $page = GP("page", 1);
        $pages = 0;
        $rp = ($page - 1) * $this->pageSize;
            
            $topics = SQLProvider::ExecuteQuery("select tbl_obj_id, title, title_url, color from tbl__eventtv_topics order by group_num, order_num");
            foreach ($topics as &$item) {
                if ($item['tbl_obj_id'] == 0)
                    $item['link'] = "/eventtv";
                else
                    $item['link'] = "/eventtv/".$item['title_url'];
                $item['gray'] = "gray";
                $item['selected'] =  "";
            }
            $topicList = $this->GetControl("tvTopicList");
            $topicList->dataSource = $topics;

            $topics = SQLProvider::ExecuteQuery("select tbl_obj_id, title, title_url from tbl__public_topics order by order_num");
            foreach ($topics as &$item) {
                if ($item['tbl_obj_id'] == 0)
                    $item['link'] = "/book";
                else
                    $item['link'] = "/book/".$item['title_url'];
                    //$item['link'] = "/book/".$item['title_url'];
                $item['gray'] = "gray";
                $item['selected'] =  "";
            }
            $topicList = $this->GetControl("topicList");
            $topicList->dataSource = $topics;



            $html = "";
            $itemTemplate = $this->getControl("listItem");

            $publics = array();
						$filter = GetParam("filter","g");
						if (isset($filter)) {
						  if ($filter == "book") {
							  $this->sort = 1;
								$publics = SQLProvider::ExecuteQuery("
									select
										tbl_obj_id, title,title_url, annotation, text, registration_date,
                    case when DATEDIFF(NOW(),registration_date)<10 then 1 else 0 end is_new,
                    logo_image, link,
										DATE_FORMAT(registration_date,'%d.%m.%Y') formatted_date, topics
									from (
										select tbl_obj_id, title,title_url, annotation, text, registration_date, is_new, logo_image, 'book' link 
										(select GROUP_CONCAT(pt.title SEPARATOR ', ')
										from tbl__eventtv2topic p2t 
										join tbl__eventtv_topics pt on p2t.parent_id = pt.tbl_obj_id
										where p2t.child_id = pd.tbl_obj_id
										order by pt.order_num) topics
										from tbl__public_doc where active = 1
										) t
									order by is_new desc, registration_date desc limit $rp,$this->pageSize");
									
									
									$count = SQLProvider::ExecuteQuery("
									select
										tbl_obj_id, title,title_url, annotation, text, registration_date,
                    case when DATEDIFF(NOW(),registration_date)<10 then 1 else 0 end is_new,
                    logo_image, link,
										DATE_FORMAT(registration_date,'%d.%m.%Y') formatted_date, topics
									from (
										select tbl_obj_id, title,title_url, annotation, text, registration_date, is_new, logo_image, 'book' link 
										(select GROUP_CONCAT(pt.title SEPARATOR ', ')
										from tbl__eventtv2topic p2t 
										join tbl__eventtv_topics pt on p2t.parent_id = pt.tbl_obj_id
										where p2t.child_id = pd.tbl_obj_id
										order by pt.order_num) topics
										from tbl__public_doc where active = 1
										) t
									order by is_new desc, registration_date desc ");
                  $count = count($count);
                  $pages = floor($count / $this->pageSize) + (($count % $this->pageSize == 0) ? 0 : 1);
									
							}
							else if ($filter == "eventtv") {								
							  $this->sort = 2;
								$publics = SQLProvider::ExecuteQuery("
									select
										tbl_obj_id, title,title_url, annotation, text, registration_date,
                    case when DATEDIFF(NOW(),registration_date)<10 then 1 else 0 end is_new,
                    logo_image, link,
										DATE_FORMAT(registration_date,'%d.%m.%Y') formatted_date, topics
									from (
										select tbl_obj_id, title,title_url, annotation, text, registration_date, is_new, logo_image, 'evettv' link 
										(select GROUP_CONCAT(pt.title SEPARATOR ', ')
										from tbl__eventtv2topic p2t 
										join tbl__eventtv_topics pt on p2t.parent_id = pt.tbl_obj_id
										where p2t.child_id = pd.tbl_obj_id
										order by pt.order_num) topics
										from tbl__eventtv_doc where active = 1
										) t
									order by is_new desc, registration_date desc limit $rp,$this->pageSize"
								);
								
								$count = SQLProvider::ExecuteQuery("
									select
										tbl_obj_id, title,title_url, annotation, text, registration_date,
                    case when DATEDIFF(NOW(),registration_date)<10 then 1 else 0 end is_new,
                    logo_image, link,
										DATE_FORMAT(registration_date,'%d.%m.%Y') formatted_date, topics
									from (
										select tbl_obj_id, title,title_url, annotation, text, registration_date, is_new, logo_image, 'evettv' link 
										(select GROUP_CONCAT(pt.title SEPARATOR ', ')
										from tbl__eventtv2topic p2t 
										join tbl__eventtv_topics pt on p2t.parent_id = pt.tbl_obj_id
										where p2t.child_id = pd.tbl_obj_id
										order by pt.order_num) topics
										from tbl__eventtv_doc where active = 1
										) t
									order by is_new desc, registration_date desc "
								);
								
								$count = count($count);
								$pages = floor($count / $this->pageSize) + (($count % $this->pageSize == 0) ? 0 : 1);
								
							}
							else {
							  CURLHandler::ErrorPage();
							}
						}
						else {							
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
									where p2t.child_id = pd.tbl_obj_id
									order by pt.order_num) topics
					from tbl__eventtv_doc pd where active = 1 
					union all
					select 
						tbl_obj_id, title,title_url, annotation, text, registration_date, is_new, logo_image, 'book' link,
						(select GROUP_CONCAT(pt.title SEPARATOR ', ')
									from tbl__public2topic p2t 
									join tbl__public_topics pt on p2t.parent_id = pt.tbl_obj_id
									where p2t.child_id = pd.tbl_obj_id
									order by pt.order_num) topics 
					from tbl__public_doc pd where active = 1
					) t
				order by is_new desc, registration_date desc limit $rp,$this->pageSize"
							);
							
							
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
									where p2t.child_id = pd.tbl_obj_id
									order by pt.order_num) topics
					from tbl__eventtv_doc pd where active = 1 
					union all
					select 
						tbl_obj_id, title,title_url, annotation, text, registration_date, is_new, logo_image, 'book' link,
						(select GROUP_CONCAT(pt.title SEPARATOR ', ')
									from tbl__public2topic p2t 
									join tbl__public_topics pt on p2t.parent_id = pt.tbl_obj_id
									where p2t.child_id = pd.tbl_obj_id
									order by pt.order_num) topics 
					from tbl__public_doc pd where active = 1
					) t
				order by is_new desc, registration_date desc "
							);
							
							$count = count($count);
							$pages = floor($count / $this->pageSize) + (($count % $this->pageSize == 0) ? 0 : 1);

							
            }
						$html .= '<table cellspacing="0" cellpadding="0" border="0" style="margin-top:10px">'; 
						foreach ($publics as $doc) { 
								$doc['isNew'] = "";
								if ($doc['is_new']==1) {
										$doc['isNew'] = "&nbsp;&nbsp;<span class='isNew'>(новое!)</span>";
								}
								$itemTemplate->dataSource = $doc;

								$html .= $itemTemplate->Render();
						}
						$html .= "</table>";

            
            //setting pager
        $pager = $this->GetControl("pager");
        $pager->currentPage = $page;
        $pager->totalPages = $pages;
        $pager->rewriteParams = $rewriteParams;
            
            $publicList = $this->GetControl("publicList");
            $publicList->html = $html;

            $topic = GP("topic");
            
            /*
            $mainMenu = $this->GetControl("menu");
            $mainMenu->dataSource["redevent"] =
              array("link"=>"http://redevent.ru/",
                    "imgname"=>"redevent",
                    "title"=>"",
                    "ads_class"=>"reklama",
                    "target"=>'target="_blank"');
					*/
					
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
		$chart["cont_`height"] = floor($counts[0]["count"]*$k);
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
