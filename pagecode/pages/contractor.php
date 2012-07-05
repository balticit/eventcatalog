<?php
class contractor_php extends CPageCodeHandler
{
    public $pageSize = 25;
    public $recommendedLimit = 20; //7 
    public $newLimit = 6;
    public $newsLimit = 9;
    public $ratedLimit = 15;
    public $is_main = true;
    public $descriptionSize = 256;

    public function contractor_php()
    {
        $this->CPageCodeHandler();
    }

    private function PrepareContractors($contractors)
    {
        foreach ($contractors as &$contractor) {
            $contractor["annotation"] = strip_tags($contractor["short_description"]);
        }
        return $contractors;
    }

    public function PreRender()
    {
        $metadata = $this->GetControl("metadata");
        $metadata->keywords = "подрядчики, организация мероприятий, организация";
        $metadata->description = "Полный список надежных подрядчиков, предоставляющих свет, звук, оформление помещений и открытых площадок для организации мероприятия. Контакты и подробные отзывы о каждом организаторе.";

        $app = CApplicationContext::GetInstance();
        /*Провека адреса*/
        $av_rwParams = array("activity", "page", "letter","city");
        CURLHandler::CheckRewriteParams($av_rwParams);
        $rewriteParams = $_GET;
        $page = GP("page", 1);
        $city = GP("city");
        $activity = GP("activity");
		$letter = GP("letter");
        $first = "NULL";
        if (!is_numeric($page) || ($page < 1)) {
            $page = 1;
        }
        if ($page > 1)
            $rewriteParams["page"] = $page;
        $letter_filter = GP("letter");
        if (!IsNullOrEmpty($letter_filter))
            $letter_filter = urldecode($letter_filter);
        $filter = "";

        if (is_numeric($activity)) {
            $rewriteParams["activity"] = $activity;
            $first = SQLProvider::ExecuteQuery(
                "select r.tbl_obj_id
        from tbl__activity_type t
        join tbl__contractor_doc r on r.tbl_obj_id = t.first_id
        where t.tbl_obj_id=$activity
          and r.active=1");
            if (sizeof($first) > 0)
                $first = $first[0]["tbl_obj_id"];
            else
                $first = "NULL";
            if (strlen($filter) > 0)
                $filter .= " and ";
            $filter .= " (tbl_obj_id in (select distinct tbl_obj_id from tbl__contractor2activity where kind_of_activity=$activity ";
            $subactivs = SQLProvider::ExecuteQuery("select distinct child_id from `vw__activity_hierarcy2contractors` where parent_id=$activity ");
            foreach ($subactivs as $sub) {
                $sa = $sub["child_id"];
                $filter .= " or kind_of_activity=$sa ";
            }
            $filter .= ")) ";

        }
        if (!IsNullOrEmpty($letter_filter)) {
            if (strlen($filter) > 0)
                $filter .= " and ";
            $filter .= " title like '$letter_filter%' ";
        }
        if (is_numeric($city) && strlen($filter) > 0)
            $filter .= " and city = $city ";
        $pages = 0;
        /*setting contractors list*/
        $this->is_main = !strlen($filter) > 0;
        if (strlen($filter) > 0) {
            $filter = " where $filter ";
            $count = SQLProvider::ExecuteQuery("select count(*) as quan from `vw__contractor_list_pro` $filter ");
            $count = $count[0]["quan"];
            $pages = floor($count / $this->pageSize) + (($count % $this->pageSize == 0) ? 0 : 1);
            if (($page > $pages) && ($pages > 0)) {
                $page = $pages;
                $rewriteParams["page"] = $page;
                CURLHandler::Redirect(CURLHandler::$currentPath . CURLHandler::BuildQueryParams($rewriteParams));
            }
            if ($page == 1)
                unset($rewriteParams["page"]);
            $rp = ($page - 1) * $this->pageSize;
            $contractors = SQLProvider::ExecuteQuery(
                "select * from `vw__contractor_list_pro` $filter
        order by if(tbl_obj_id=$first,0,1), pro_type desc, pro_cost desc, pro_date_pay desc, title limit $rp,$this->pageSize");
			// echo "select * from `vw__contractor_list_pro` $filter";
			// die('!');
		   $letter = "";
            foreach ($contractors as &$contractor)
            {
                $contractor["logo"] = $contractor["logo_image"];
                $contractor["city_item"] = (!empty($contractor["city_name"])) ? '<span style="color: #000;">('.$contractor["city_name"].')</span>' : '';
                $contractor["class"] = "contractor_table_hover";
                if ($contractor["title"][0] != $letter) {
                    $contractor["space_height"] = 15;
                    $letter = $contractor["title"][0];
                }
                else
                    $contractor["space_height"] = 5;

                switch ($contractor["selection"]) {
                    case 1:
                        $contractor["selection_type"] = "color:#EE0000; font-weight:bold;";
                        break;
                    case 2:
                        $contractor["selection_type"] = "color:#000; font-weight:bold;";
                        break;
                    case 3:
                        $contractor["selection_type"] = "font-weight:bold; color:#EE0000;";
                        break;
                    default:
                        $contractor["selection_type"] = "color:#000; font-weight:bold;";
                        break;
                }
                $gr = SQLProvider::ExecuteQuery("select act.* from tbl__activity_type act, tbl__contractor2activity ca where ca.tbl_obj_id=" . $contractor["tbl_obj_id"] . " and ca.kind_of_activity=act.tbl_obj_id");
                $contractor['category'] = "";
                foreach ($gr as $gkey => $value) {
                    if ($contractor['category'] != "")
                        $contractor['category'] .= " / ";
                    $contractor['category'] .= '<a class="common" href="/contractor/activity/' . $value['tbl_obj_id'] . '">' . $value['title'] . '</a>';
                }

                $contractor["info"] = CutString(strip_tags($contractor["short_description"]), $this->descriptionSize);

                $contractor['links'] = "";
                /*$photos = SQLProvider::ExecuteQuery("select 1 from tbl__contractor_photos where parent_id = ".$contractor['tbl_obj_id']);
                    if (sizeof($photos)) {
                        $contractor['links'] .= '<a class="common" href="/contractor/'.$contractor['title_url'].'?page=photos" style="margin: 0 10px 0 0">Фото</a>';
                    }
                    if ($contractor["youtube_video"]) {
                        $contractor['links'] .= '<a class="common" href="/contractor/'.$contractor['title_url'].'?page=video" style="margin: 0 10px 0 0">Видео</a>';
                    }*/
                $contractor['resident_type'] = 'contractor';
                
				//PRO
                $contractor['background'] 		= '0';
                $contractor['pro_logo']			= '';
				$contractor['pro_logo_prew']	= '';
                if($contractor['pro_type'] == 1 || $contractor['pro_type'] == 2){
					$contractor['activeEl'] = 'activeEl';
                    $contractor['border']			= 'border:2px solid '.getProBackgroud('contractor').';';
					$contractor['pro_logo_prew']	= getProLogoForPreview('contractor');
					$contractor['pro_logo']			= getProLogo();
                }
            }
            $catList = $this->GetControl("contList");
            $catList->dataSource = $contractors;
        }
        else 
        { 
            /*recommended list*/
            $recommended_conts = SQLProvider::ExecuteQuery("select tbl_obj_id,recommended, title, short_description, logo_image,
						                                                  'contractor' resident_type, title_url
																													 from `vw__contractor_list`
																													 where recommended>0
																													 order by recommended, tbl_obj_id desc limit $this->recommendedLimit");			
			
			$recommended_conts_2 = array();
			$recommended_conts_1 = array();
			foreach($recommended_conts as $key=>$value){
				if($value['recommended']<11){
					$recommended_conts_2[] = $value;
				}
				else{
					$recommended_conts_1[] = $value;
				}
			}

            $recomedList_1 = $this->GetControl("RecomedList_1");
            $recomedList_1->dataSource = $this->PrepareContractors($recommended_conts_1); 
			
			$recomedList_2 = $this->GetControl("RecomedList_2");
            $recomedList_2->dataSource = $this->PrepareContractors($recommended_conts_2);

            /*end recommended list*/

            /*new list*/
            $new_conts = SQLProvider::ExecuteQuery("select tbl_obj_id, title, short_description, formatted_date,
						                                          'contractor' resident_type, title_url
																									 from  `vw__contractor_list`
																									 order by registration_date desc limit $this->newLimit");
           
			$new_conts_1 = array_slice($new_conts, 0, floor(count($new_conts)/2));
			$new_conts_2 = array_slice($new_conts, floor(count($new_conts)/2), count($new_conts));

            $newList_1 = $this->GetControl("NewList_1");
            $newList_1->dataSource = $this->PrepareContractors($new_conts_1); 
			
			      $newList_2 = $this->GetControl("NewList_2");
            $newList_2->dataSource = $this->PrepareContractors($new_conts_2);


            /*news list*/
            /*
            $cont_news = SQLProvider::ExecuteQuery("select rn.tbl_obj_id, rn.title, DATE_FORMAT(rn.date,'%d.%m.%y') as `strdate`,
						                                          rn.resident_type, rn.resident_id, res.title_url
																									 from `tbl__resident_news` rn
																									 join `tbl__contractor_doc` res on res.tbl_obj_id = rn.resident_id
																									 where rn.`active`=1 and rn.`resident_type`='contractor'
																									 order by rn.`date` DESC limit $this->newsLimit");
            $newsList = $this->GetControl("NewsList");
            $newsList->dataSource = $cont_news;
            */

        		$res_news = SQLProvider::ExecuteQuery(
                    "select rn.*, DATE_FORMAT(date,'%d.%m.%y') as `strdate`
        						 from `tbl__resident_news` rn
        												where rn.`active`=1 and rn.`resident_type`='contractor'
        												order by rn.`date` DESC limit $this->newsLimit
        												");
        		foreach($res_news as $key => $val) {
        			$res = SQLProvider::ExecuteQuery("SELECT * FROM tbl__".$res_news[$key]["resident_type"]."_doc WHERE tbl_obj_id=".$res_news[$key]["resident_id"]);
        			$res_news[$key]["title_url"] = $res[0]['title_url'];
        			$res_news[$key]["res_title"] = $res[0]['title'];
        			
        			if(!empty($res_news[$key]["logo_image"])) {
                $res_news[$key]["logo"] = $res_news[$key]["logo_image"];
              }
        			else {
          			if (isset($res[0]['logo'])) {
          				$res_news[$key]["logo"] = $res[0]['logo'];
          			}
          			else {
          				$res_news[$key]["logo"] = $res[0]['logo_image'];
          			}
        			}
        			
        			$res_news[$key]["title"] = CutString($res_news[$key]["title"]);
        			$res_news[$key]["text"] = strip_tags(CutString($res_news[$key]["text"], 150));
        			switch($val['resident_type']) {
        			case 'area': $res_news[$key]['sub'] = 'Новость площадки'; break;
        			case 'artist': $res_news[$key]['sub'] = 'Новость артиста'; break;
        			case 'contractor': $res_news[$key]['sub'] = 'Подрядчик'; break;
        			case 'agency': $res_news[$key]['sub'] = 'Агентство'; break;
        			}
        		}
        		$this->GetControl("NewsList")->dataSource = $res_news;
            /*end news list*/

            /*rate list*/
            $ratings = SQLProvider::ExecuteQuery("select ul.to_resident_id as tbl_obj_id, c.title ,count(ul.tbl_obj_id) as voted, c.title_url
																					from tbl__userlike ul
																					join tbl__contractor_doc c on c.tbl_obj_id = ul.to_resident_id and ul.to_resident_type='contractor'
																					where c.active = 1
																					group by ul.to_resident_id, c.title
																					order by voted desc, tbl_obj_id limit $this->ratedLimit");
            $user = new CSessionUser("user");
            CAuthorizer::AuthentificateUserFromCookie(&$user);
            CAuthorizer::RestoreUserFromSession(&$user);
            foreach ($ratings as $key => &$rating) {
                $rating["index"] = $key + 1;
                $rating["resident_type"] = 'contractor';
                if (!$user->authorized)
                    $rating["msg_vote"] = "onclick=\"javascript: ShowLikeMessage(); return false;\"";
                else
                    $rating["msg_vote"] = "";
            }
            $ratingList = $this->GetControl("RatingList");
            $ratingList->dataSource = $ratings;
            /*end rate list*/	
			
            //PRO2-list
            $pro2_list = $this->GetControl("pro2List");
			//var_dump($pro2_list);
            $pro2_list->dataSource = getPro2List('contractor');
        }

        //SEO text
        if (isset($activity)) {
            $ft = SQLProvider::ExecuteQuery("select seo_text from tbl__activity_type where tbl_obj_id=" . $activity);
            $ft["seo_text"] = $ft[0]["seo_text"];
        }
        else {
            $ft["seo_text"] = "";
        }
        $footerText = $this->GetControl("footerText");
        $footerText->dataSource = $ft;

        /*setting activity types*/
        $activities = SQLProvider::ExecuteQueryIndexed("select *, tbl_obj_id as child_id from `tbl__activity_type` order by priority desc", "child_id");
        $titlefilter = array();
        $titlefilterLinks = array();
        foreach ($activities as &$value) {
            $cpars = array();
            if(!IsNullOrEmpty($letter)){
                $cpars['letter']=$letter;
            }
            $value["link"] = "/contractor/" .$value['title_url'].CURLHandler::BuildQueryParams($cpars);
            $value["selected"] = "";
            $value["orange"] = "orange";
        }
        if (isset($activity)) {
            if (isset($activities[$activity])) {
                $value = &$activities[$activity];
                $value["selected"] = 'id="selectOrange"';
                $value["orange"] = '';
                array_push($titlefilter, CStringFormatter::buildCategoryLinks($value['title'], null));
                array_push($titlefilterLinks, CStringFormatter::buildCategoryLinks($value['title'], $value['link'], "contractor"));
                if ($value["parent_id"]) {
                    $activities[$value["parent_id"]]["selected"] = 'id="selectOrange"';
                    $activities[$value["parent_id"]]["orange"] = '';
                    array_unshift($titlefilter, CStringFormatter::buildCategoryLinks($activities[$value["parent_id"]]['title'], null));
                    array_unshift($titlefilterLinks, CStringFormatter::buildCategoryLinks($activities[$value["parent_id"]]['title'], $activities[$value["parent_id"]]['link'], "contractor"));
                }
            }
            else
                CURLHandler::ErrorPage();
        }
        $actList = $this->GetControl("actList");
        $actList->dataSource = $activities;
        $titlefil = $this->GetControl("titlefilter");
        if (sizeof($titlefilter))
            $titlefil->text = implode(" / ", $titlefilter) . " - ";
        if (sizeof($titlefilterLinks))
            $this->GetControl("titlefilterLinks")->html = '<div class="titlefilter contractor">' . implode(" / ", $titlefilterLinks) . '</div>';
        else
            $this->GetControl("titlefilterLinks")->html = '';
        //setting pager
        $pager = $this->GetControl("pager");
        $pager->currentPage = $page;
        $pager->totalPages = $pages;
        $pager->rewriteParams = $rewriteParams;
        //setting letter
        $letters = array();
        $len = strlen(FILTER_LETTERS);
        $fl = FILTER_LETTERS;
        for ($i = 0; $i < $len; $i++)
        {
            $pars = $rewriteParams;
            $pars["letter"] = urlencode($fl[$i]);
            $link = CURLHandler::$currentPath . CURLHandler::BuildQueryParams($pars);
            $let = array("letter" => $fl[$i], "link" => $link, "selected" => ($fl[$i] == $letter_filter));
            array_push($letters, $let);
        }
        $letterFilter = $this->GetControl("letterFilter");
        $letterFilter->dataSource = $letters;

        $mainMenu = $this->GetControl("menu");
        $mainMenu->dataSource["kinodoktor"] =
            array("link" => "http://www.kinodoctor.ru/",
                "imgname" => "kinodoktor",
                "title"=>"",
                "target" => 'target="_blank"');
				
		
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
