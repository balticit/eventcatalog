<?php
class agency_php extends CPageCodeHandler
{
    public $pagesize = 25;
    public $recommendedLimit = 20; 
    public $newLimit = 6;
    public $newsLimit = 7;
    public $ratedLimit = 15;
    public $is_main = true;
    public $descriptionSize = 256;

    public function agency_php()
    {
        $this->CPageCodeHandler();
    }

    public function BuildFilter($filter, $name, $value, $cond = "=", $type = "numeric")
    {
        switch ($type) {
            case "numeric":
                {
                if (is_numeric($value)) {
                    if (strlen($filter) > 0)
                        $filter .= " and ";
                    $filter .= " $name $cond $value ";
                }
                break;
                }

            case "string":
                if (is_string($value)) {
                    if (strlen($filter) > 0)
                        $filter .= " and ";
                    $filter .= " $name $cond '$value' ";
                }
                break;
        }
        return $filter;
    }

    private function PrepareAgenciesMain($agencies)
    {
        foreach ($agencies as &$agency)
            $agency["annotation"] = strip_tags($agency["description"]);
        return $agencies;
    }


    public function PreRender()
    {
        $metadata = $this->GetControl("metadata");
        $metadata->keywords = "эвент-агентство, программа мероприятий";
        $metadata->description = "Эвент-агентства – категории, контакты и услуги. Проведение мероприятий «под ключ»: подбор программы мероприятия, площадки, оформления и представления. Рейтинг и отзывы эвент-агентств.";

        /*Провека адреса*/
        $av_rwParams = array("activity", "page", "letter");
        CURLHandler::CheckRewriteParams($av_rwParams);
        $rewriteParams = $_GET;
        $page = GP("page", 1);
        $first = "NULL";
        $city = GP("city");
        $activity = GP("activity");
        $letter_filter = GP("letter");
        $footerTextHTML = "";
        if (!IsNullOrEmpty($letter_filter))
            $letter_filter = urldecode($letter_filter);
        $filter = "";
        if (!IsNullOrEmpty($letter_filter)) {
            if (strlen($filter) > 0)
                $filter .= " and ";
            $filter .= " title like '$letter_filter%' ";
        }
        if (is_numeric($activity)) {
            $rewriteParams["activity"] = $activity;
            $first = SQLProvider::ExecuteQuery(
                "select r.tbl_obj_id
        from tbl__agency_type t
        join tbl__agency_doc r on r.tbl_obj_id = t.first_id
        where t.tbl_obj_id=$activity
          and r.active=1");
            if (sizeof($first) > 0)
                $first = $first[0]["tbl_obj_id"];
            else
                $first = "NULL";
            if (strlen($filter) > 0)
                $filter .= " and ";
            $filter .= " tbl_obj_id in (select tbl_obj_id from tbl__agency2activity a2a where a2a.kind_of_activity = $activity) ";

            //выставляем keywords, description и текст для каждого раздела
            $info = SQLProvider::ExecuteQuery(
                "select keywords, description, seo_text_caption, seo_text, page_title, title
        from tbl__agency_type where tbl_obj_id = $activity");
            if (sizeof($info) > 0) {
                $info = $info[0];
                if (!empty($info["page_title"]))
                    $this->GetControl('title')->text = $info["page_title"] . " - ";
                else
                    $this->GetControl('title')->text = $info["title"] . " - Каталог агентств - ";
                if (!empty($info["keywords"]))
                    $metadata->keywords = $info["keywords"];
                if (!empty($info["description"]))
                    $metadata->description = $info["description"];
                if (!empty($info["seo_text_caption"]) && !empty($info["seo_text"]) && $page == 1) {
                    $footerTextHTML = '<div><div class="recomendTitle agency">' . $info["seo_text_caption"] .
                        '</div>' . $info["seo_text"] . '</div>';
                }
            }
        }
        if (is_numeric($city) && strlen($filter) > 0) {
            $filter = $this->BuildFilter($filter, "city", $city);
        }


        $pages = 0;
        if (strlen($filter) > 0) {
            $filter = " where $filter";
        }
        $this->is_main = strlen($filter) == 0;
        if (!$this->is_main) {
            $this->GetControl("footerText")->template = $footerTextHTML;
            $count = SQLProvider::ExecuteQuery("select count(*) as counter from  `vw__agency_list_pro` $filter");
            $count = $count[0]["counter"];
            $pages = floor($count / $this->pagesize) + (($count % $this->pagesize == 0) ? 0 : 1);
            if (($page > $pages) && ($pages > 0)) {
                $page = $pages;
                $rewriteParams["page"] = $page;
                CURLHandler::Redirect(CURLHandler::$currentPath . CURLHandler::BuildQueryParams($rewriteParams));
            }
            $sp = ($page - 1) * $this->pagesize;
            if (($page == 1) && (isset($rewriteParams["page"]))) {
                unset($rewriteParams["page"]);
            }
            $agencies = SQLProvider::ExecuteQuery(
                "select *,10 height from  `vw__agency_list_pro` $filter
        order by if(tbl_obj_id=$first,0,1), pro_type desc, pro_cost desc, pro_date_pay desc, title limit $sp,$this->pagesize");
            $agencyList = $this->GetControl("agencyList");
            foreach ($agencies as &$agency)
            {
                $agency["space_height"] = "10";
                $agency["info"] = CutString(strip_tags($agency["description"]), $this->descriptionSize);
                $agency["logo"] = $agency["logo_image"];
                switch ($agency["selection"]) {
                    case 1:
                        $agency["selection_type"] = "color:#EE0000; font-weight:bold;";
                        break;
                    case 2:
                        $agency["selection_type"] = "color:#000; font-weight:bold;";
                        break;
                    case 3:
                        $agency["selection_type"] = "color:#EE0000; font-weight:bold;";
                        break;
                    default:
                        $agency["selection_type"] = "color:#000; font-weight:bold;";
                        break;
                }
                $agency["annotation"] = BreakString(CutString(strip_tags($agency["description"]), $this->descriptionSize), 50);
                $agencyTypes = SQLProvider::ExecuteQuery("SELECT t.* FROM tbl__agency_type t, tbl__agency2activity a WHERE a.kind_of_activity = t.tbl_obj_id and a.tbl_obj_id=" . $agency["tbl_obj_id"]);
                $agencyTypeLinks = array();
                foreach ($agencyTypes as $aType) {
                    $link = '/agency/'.$aType['title_url'];
                    array_push($agencyTypeLinks, CStringFormatter::buildCategoryLinks($aType['title'], $link, "common"));
                }
                $agency['category'] = implode(" / ", $agencyTypeLinks);
                $agency['links'] = "";
                /*$photos = SQLProvider::ExecuteQuery("select 1 from tbl__agency_photos where parent_id = ".$agency['tbl_obj_id']);
                    if (sizeof($photos)) {
                        $agency['links'] .= '<a class="common" href="/agency/'.$agency['title_url'].'?page=photos" style="margin: 0 10px 0 0">Фото</a>';
                    }
                    if ($agency["youtube_video"]) {
                        $agency['links'] .= '<a class="common" href="/agency/'.$agency['title_url'].'?page=video" style="margin: 0 10px 0 0">Видео</a>';
                    }*/
                $agency['resident_type'] = 'agency';
                $agency['class'] = 'agency_table_hover';
				$agency["city_item"] = (!empty($agency["city_title"])) ? '<span style="color: #000;">('.$agency["city_title"].')</span>' : '';
                
                //PRO
				$agency['background']		= '0';
                $agency['pro_logo']			= '';
                $agency['pro_logo_prew']	= '';
                if($agency['pro_type'] == 1 || $agency['pro_type'] == 2) {
					$agency['border']			= 'border:2px solid '.getProBackgroud('agency').';';
					$agency['pro_logo_prew']	= getProLogoForPreview('agency');
					$agency['pro_logo']			= getProLogo();
                }
            }
            $agencyList->dataSource = $agencies;
            //setting pager
            $pager = $this->GetControl("pager");
            $pager->currentPage = $page;
            $pager->totalPages = $pages;
            $pager->rewriteParams = $rewriteParams;
        }
        else {
            $this->GetControl('title')->text = "Каталог агентств - ";
            /*recommended list*/
            $recommended_conts = SQLProvider::ExecuteQuery("select tbl_obj_id, title,recommended, description, logo_image, 'agency' resident_type, title_url
																										 from `vw__agency_list3`
																										 where recommended>0
																										 order by recommended, tbl_obj_id desc limit $this->recommendedLimit");
			$recommended_conts_1 = array();
			$recommended_conts_2 = array();
			foreach($recommended_conts as $key=>$value){
				if($value["recommended"]<11){
					$recommended_conts_2[] = $value;
				}
				else{
					$recommended_conts_1[] = $value;
				}
			}           				
			
            $recomedList_1 = $this->GetControl("RecomedList_1");
            $recomedList_1->dataSource = $this->PrepareAgenciesMain($recommended_conts_1); 
			
			$recomedList_2 = $this->GetControl("RecomedList_2");
            $recomedList_2->dataSource = $this->PrepareAgenciesMain($recommended_conts_2);

            /*new list*/
            $new_conts = SQLProvider::ExecuteQuery("select tbl_obj_id, title, description, DATE_FORMAT(registration_date,'%d.%m.%y') as formatted_date,
                                        'agency' resident_type, title_url
																				from  `vw__agency_list3`
																				order by registration_date desc limit $this->newLimit"); 
            
			$new_conts_1 = array_slice($new_conts, 0, floor(count($new_conts)/2));
			$new_conts_2 = array_slice($new_conts, floor(count($new_conts)/2), count($new_conts));

            $newList_1 = $this->GetControl("NewList_1");
            $newList_1->dataSource = $this->PrepareAgenciesMain($new_conts_1);  
			
			      $newList_2 = $this->GetControl("NewList_2");
            $newList_2->dataSource = $this->PrepareAgenciesMain($new_conts_2);
			

            /*news list*/
            /*
            $news = SQLProvider::ExecuteQuery("select rn.tbl_obj_id, rn.title, DATE_FORMAT(rn.date,'%d.%m.%y') as `strdate`,
                                           rn.resident_type, rn.resident_id, res.title_url
																						 from `tbl__resident_news` rn
                                             join `tbl__agency_doc` res on res.tbl_obj_id = rn.resident_id
																						 where rn.`active`=1 and rn.`resident_type`='agency'
																						 order by rn.`date` DESC limit $this->newsLimit");
            $newsList = $this->GetControl("NewsList");
            $newsList->dataSource = $news;
            */
            
        		$res_news = SQLProvider::ExecuteQuery(
                    "select rn.*, DATE_FORMAT(date,'%d.%m.%y') as `strdate`
        						 from `tbl__resident_news` rn
        												where rn.`active`=1 and rn.`resident_type`='agency'
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
        			$res_news[$key]["text"] = strip_tags(CutString($res_news[$key]["text"], 100));
        			switch($val['resident_type']) {
        			case 'area': $res_news[$key]['sub'] = 'Новость площадки'; break;
        			case 'artist': $res_news[$key]['sub'] = 'Новость артиста'; break;
        			case 'contractor': $res_news[$key]['sub'] = 'Новость подрядчика'; break;
        			case 'agency': $res_news[$key]['sub'] = 'Агентство'; break;
        			}
        		}
        		$this->GetControl("NewsList")->dataSource = $res_news;
            /*end news list*/

            /*rate list*/
            $ratings = SQLProvider::ExecuteQuery("select ul.to_resident_id as tbl_obj_id, c.title ,count(ul.tbl_obj_id) as voted, c.title_url
																		from tbl__userlike ul
																		join tbl__agency_doc c on c.tbl_obj_id = ul.to_resident_id and ul.to_resident_type='agency'
																		where c.active = 1
																		group by ul.to_resident_id, c.title
																		order by voted desc, tbl_obj_id limit $this->ratedLimit");
            $user = new CSessionUser("user");
            CAuthorizer::AuthentificateUserFromCookie(&$user);
            CAuthorizer::RestoreUserFromSession(&$user);
            foreach ($ratings as $key => &$rating) {
                $rating["index"] = $key + 1;
                $rating["resident_type"] = 'agency';
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
            $pro2_list->dataSource = getPro2List('agency');
        }

        //kind of activity
        $activities = SQLProvider::ExecuteQuery("select `tbl_obj_id` as kind_of_activity, title,title_url from  `tbl__agency_type` order by priority desc");
        foreach ($activities as $key => $value) {
            $cpars = $rewriteParams;
			unset($cpars['activity']);
            $activities[$key]["link"] = '/agency/'.$value['title_url'] . CURLHandler::BuildQueryParams($cpars);
            $activities[$key]["selected"] = $value["kind_of_activity"] == $activity ? 'id="selectGreen"' : "";
            $activities[$key]["green"] = $value["kind_of_activity"] == $activity ? '' : "green";
        }
        $activityList = $this->GetControl("activityList");
        $activityList->dataSource = $activities;
        $titlefilter = "";
        $titlefilterLinks = "";
        foreach ($activities as $activ) {
            if ($activ['kind_of_activity'] == $activity) {
			     $link = "/agency/".$activ['title_url'];
                 $titlefilter = trim($activ['title']);
                 $titlefilterLinks = CStringFormatter::buildCategoryLinks($titlefilter, $link, "agency");
            }
        }
        $titlefil = $this->GetControl("titlefilter");
        if (!IsNullOrEmpty($titlefilter))
            $titlefil->text = $titlefilter . " - ";
        if (!IsNullOrEmpty($titlefilterLinks))
            $this->GetControl("titlefilterLinks")->html = '<div class="titlefilter agency">' . $titlefilterLinks . '</div>';
        else
            $this->GetControl("titlefilterLinks")->html = '';

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
        $mainMenu->dataSource["polymedia"] =
            array("link" => "http://www.polymedia.ru/",
                "imgname" => "polymedia",
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
