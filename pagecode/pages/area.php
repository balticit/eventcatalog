<?php
class area_php extends CPageCodeHandler
{
    public $pageSize = 25;
    public $recommendedLimit = 20;
    public $newLimit = 6;
    public $newsLimit = 7;
    public $ratedLimit = 15;
    public $is_main = true;
    public $descriptionSize = 256;

    public function area_php()
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

    public function PrepareAreasMain($areas)
    {
        foreach ($areas as &$area)
            $area["annotation"] = strip_tags($area["description"]);
        return $areas;
    }

    public function PreRender()
    {
    
        // TIME IN SITE UPDATE      
    $user = new CSessionUser("user");
		CAuthorizer::RestoreUserFromSession(&$user);
    if ($user->authorized)
    {

    $user = new CSessionUser($user->type);
    CAuthorizer::AuthentificateUserFromCookie(&$user);
    CAuthorizer::RestoreUserFromSession(&$user);
    
    if ($user->type =="user")
    {
      $tbl =  "tbl__registered_user";
    }
    else { $tbl = "tbl__".$user->type."_doc"; }

    SQLProvider::ExecuteNonReturnQuery("update $tbl set last_visit_date=NOW() WHERE tbl_obj_id = $user->id AND last_visit_date<DATE_SUB(NOW(), INTERVAL 1 MINUTE) ");

    }
    
    
    
        $metadata = $this->GetControl("metadata");
        $metadata->keywords = "банкетные залы, рестораны, особняки и усадьбы, выставочные залы, конференц-залы, концертные залы, площадки для тест-драйва, летние веранды, банкетоходы, загородные усадьбы";
        $metadata->description = "EventCatalog.ru – удобный поиск площадки для проведения мероприятия, праздника или свадьбы: банкетные залы, рестораны, выставочные залы, концертные площадки, открытие площадки… и многое другое!";

        $app = CApplicationContext::GetInstance();
        /*Провека адреса*/
        $av_rwParams = array(
            "type", "subtype", "page", "letter",
            "area_doc_place", "area_doc_location",
            "area_doc_cost_from", "area_doc_cost_to",
            "area_doc_banquet_from", "area_doc_banquet_to",
            "area_doc_buffet_from", "area_doc_buffet_to",
            "metro", "category", "mdist", "mhighway", "mcity","capacity","cost","invite_catering","city",
            "car_into","my_catering");
        CURLHandler::CheckRewriteParams($av_rwParams);
        $rewriteParams = $_GET;
        $first = "NULL";
        $filter = "";
        
        $category = GP("category");
        if (isset($category)) {
          $ts = preg_split('/_/', $category);
          if(sizeof($ts) > 1) {
            $type = $ts[0];
            $subtype = $ts[1];
          }
          else {
            if ($ts[0]>0)
              $type = $ts[0];
            else
              $type = null;
            $subtype = null;
          }
        }
    
        $city = GP("city");
    
        $filter = "";
        $currentFind = "";
    
        $capacity = GP("capacity");
        if (isset($capacity) && is_array($capacity)) {
          if (strlen($filter)>0)
          {
            $filter.=" and ";
          }
          $cup_filter = "";
          $cup_title = "";
          foreach($capacity as $val) {
            if (strlen($cup_filter)>0)
            {
              $cup_filter .= " or ";
              $cup_title .= ", ";
            }
            $diap = preg_split('/_/', $val);
            $cup_filter .= "sum_places_banquet between $diap[0] and $diap[1]";
            if ($diap[1] > 1500)
              $diap[1] = '...';
            $cup_title .= "$diap[0] - $diap[1]";
          }
          $filter .= "($cup_filter)";
          
          $currentFind .= "<div style='padding:0 0 5px 5px;color:#3399FF'>Вместимость: $cup_title </div>";
        }
    
        $cost = GP("cost");
        if (isset($cost) && is_array($cost)) {
          if (strlen($filter)>0)
          {
            $filter.=" and ";
          }
          $cost_filter = "";
          $cost_title = "";
          foreach($cost as $val) {
            if (strlen($cost_filter)>0)
            {
              $cost_filter .= " or ";
              $cost_title .= ", ";
            }
            $diap = preg_split('/_/', $val);
            $cost_filter .= "ifnull(cost_banquet,0) between $diap[0] and $diap[1]";
            if ($diap[1] > 3500)
              $diap[1] = '...';
            $cost_title .= "$diap[0] - $diap[1]";
          }
          $filter .= "($cost_filter)";
          $currentFind .= "<div style='padding:0 0 5px 5px;color:#3399FF'>Стоимость: $cost_title </div>";
        }
    
        $metro = GP("metro");
        if (isset($metro) && is_array($metro)) {      
          $metro_filter = "";
          foreach($metro as $val) {
            if ($val > 0) {
              if (strlen($metro_filter)>0) {
                $metro_filter .= ",";
              }
              $metro_filter .= $val;
            }
          }
          if (strlen($metro_filter)>0) {
            if (strlen($filter)>0)
            {
              $filter.=" and ";
            }
            $metro_title = SQLProvider::ExecuteScalar("select GROUP_CONCAT(title order by title SEPARATOR ', ') from tbl__metro_stations where tbl_obj_id in ($metro_filter)");
            $filter .= "tbl_obj_id in (select area from tbl__area_metro where metro_station in ($metro_filter))";
            $currentFind .= "<div style='padding:0 0 5px 5px;color:#3399FF'>Станции метро: $metro_title </div>";
          }
        }
        $mdist = GP("mdist");
        if (isset($mdist) && is_array($mdist)) {
          $mdist_filter = "";
          foreach($mdist as $val) {
            if ($val > 0) {
              if (strlen($mdist_filter)>0) {
                $mdist_filter .= ",";
              }
              $mdist_filter .= $val;
            }
          }
          if (strlen($mdist_filter)>0) {
            if (strlen($filter)>0)
            {
              $filter.=" and ";
            }
            $mdist_title = SQLProvider::ExecuteScalar("select GROUP_CONCAT(title order by title SEPARATOR ', ') from tbl__moscow_districts where tbl_obj_id in ($mdist_filter)");
            $filter .= "moscow_district in ($mdist_filter)";
            $currentFind .= "<div style='padding:0 0 5px 5px;color:#3399FF'>Округа: $mdist_title </div>";
          }
        }
    
    
        $invite_catering = GP("invite_catering");
        if (!IsNullOrEmpty($invite_catering)) {
          if (strlen($filter)>0)
          {
            $filter.=" and ";
          }
          $filter.=" invite_catering like '%$invite_catering%' ";
          $currentFind .=
            "<div style='padding:0 0 5px 5px;color:#3399FF'>Возможность приглашения стороннего кейтеринга: ".
            ($invite_catering ? "есть":"нет")."</div>";
        }
    
        $car_into = GP("car_into");
        if (!IsNullOrEmpty($car_into)) {
          if (strlen($filter)>0)
          {
            $filter.=" and ";
          }
          $filter.=" car_into like '%$car_into%' ";
          $currentFind .= "<div style='padding:0 0 5px 5px;color:#3399FF'>Возможность установки автомобиля внутри площадки: ".
            ($car_into ? "есть":"нет")."</div>";
        }
        $mcity = GP("mcity");
        if (isset($mcity) && is_array($mcity)) {      
          $mcity_filter = "";
          foreach($mcity as $val) {
            if ($val > 0) {
              if (strlen($mcity_filter)>0) {
                $mcity_filter .= ",";
              }
              $mcity_filter .= $val;
            }
          }
          if (strlen($mcity_filter)>0) {
            if (strlen($filter)>0)
            {
              $filter.=" and ";
            }
            $mcity_title = SQLProvider::ExecuteScalar("select GROUP_CONCAT(title order by title SEPARATOR ', ') from tbl__moscow_cities where tbl_obj_id in ($mcity_filter)");
            $filter .= "tbl_obj_id in (select area_id from tbl__area_m_cities where moscow_city_id in ($mcity_filter))";
            $currentFind .= "<div style='padding:0 0 5px 5px;color:#3399FF'>Города Московской области: $mcity_title </div>";
          }
        }
        $mhighway = GP("mhighway");
        if (isset($mhighway) && is_array($mhighway)) {      
          $mhighway_filter = "";
          foreach($mhighway as $val) {
            if ($val > 0) {
              if (strlen($mhighway_filter)>0) {
                $mhighway_filter .= ",";
              }
              $mhighway_filter .= $val;
            }
          }
          if (strlen($mhighway_filter)>0) {
            if (strlen($filter)>0)
            {
              $filter.=" and ";
            }
            $mhighway_title = SQLProvider::ExecuteScalar("select GROUP_CONCAT(title order by title SEPARATOR ', ') from tbl__moscow_highways where tbl_obj_id in ($mhighway_filter)");
            $filter .= "tbl_obj_id in (select area_id from tbl__area_m_highways where moscow_highway_id in ($mhighway_filter))";
            $currentFind .= "<div style='padding:0 0 5px 5px;color:#3399FF'>Шоссе Московской области: $mhighway_title </div>";
          }
        }
    
    
        $this->GetControl("currentFind")->html = $currentFind;
        
        
        
        
        $type = GP("type");
        $page = GP("page", 1);
        $subtype = GP("subtype");
        
        
        $area_doc_place = GP("area_doc_place");
        $area_doc_location = GP("area_doc_location");
        $area_doc_cost_from = GP("area_doc_cost_from");
        $area_doc_cost_to = GP("area_doc_cost_to");
        $area_doc_banquet_from = GP("area_doc_banquet_from");
        $area_doc_banquet_to = GP("area_doc_banquet_to");
        $area_doc_buffet_from = GP("area_doc_buffet_from");
        $area_doc_buffet_to = GP("area_doc_buffet_to");

        $letter_filter = GP("letter");
        if (!IsNullOrEmpty($letter_filter)) {
            $letter_filter = urldecode($letter_filter);
        }

        if (!IsNullOrEmpty($area_doc_banquet_from)) {
            $capacityTitle = "<div style='padding:0 0 10px 5px;color:#3399FF'>Вместимость: ";
            $capacityTitle .= "$area_doc_banquet_from - ";
            $rewriteParams['area_doc_banquet_from'] = $area_doc_banquet_from;
            if (!IsNullOrEmpty($area_doc_banquet_to)) {
                $capacityTitle .= "$area_doc_banquet_to";
                $rewriteParams['area_doc_banquet_to'] = $area_doc_banquet_to;
            }
            else {
                $capacityTitle .= "...";
            }
            $capacityTitle .= "</div>";
            $this->GetControl("currentCapacity")->html = $capacityTitle;
        }
        $filter = $this->BuildFilter($filter, "city_location", $area_doc_location);
        $filter = $this->BuildFilter($filter, "area_cost", $area_doc_cost_from, ">=");
        $filter = $this->BuildFilter($filter, "area_cost", $area_doc_cost_to, "<=");
        $filter = $this->BuildFilter($filter, "max_sitting_man", $area_doc_banquet_from, ">=");
        $filter = $this->BuildFilter($filter, "max_sitting_man", $area_doc_banquet_to, "<=");
        $filter = $this->BuildFilter($filter, "max_count_man", $area_doc_buffet_from, ">=");
        $filter = $this->BuildFilter($filter, "max_count_man", $area_doc_buffet_to, "<=");
        if (is_string($area_doc_place)) {
            if (strlen($filter) > 0)
                $filter .= " and ";
            $filter .= " title like '%$area_doc_place%' ";
        }
        if (is_numeric($type)) {
            $first = SQLProvider::ExecuteQuery(
                "select r.tbl_obj_id
        from tbl__area_types t
        join tbl__area_doc r on r.tbl_obj_id = t.first_id
        where t.tbl_obj_id=$type
          and r.active=1");
            if (sizeof($first) > 0)
                $first = $first[0]["tbl_obj_id"];
            else
                $first = "NULL";
            if (strlen($filter) > 0)
                $filter .= " and ";
            $filter .= " tbl_obj_id in (select area_id from tbl__area2subtype a2s " .
                " join tbl__area_subtypes s on s.tbl_obj_id = a2s.subtype_id where s.parent_id=$type) ";
            $rewriteParams["type"] = $type;
        }
        if (is_numeric($subtype)) {
            $first = SQLProvider::ExecuteQuery(
                "select r.tbl_obj_id
        from tbl__area_subtypes t
        join tbl__area_doc r on r.tbl_obj_id = t.first_id
        where t.tbl_obj_id=$subtype
          and r.active=1");
            if (sizeof($first) > 0)
                $first = $first[0]["tbl_obj_id"];
            else
                $first = "NULL";
            if (strlen($filter) > 0)
                $filter .= " and ";
            $filter .= " tbl_obj_id in (select area_id from tbl__area2subtype where subtype_id=$subtype) ";
            $rewriteParams["subtype"] = $subtype;
        }
        if (is_numeric($type)) {
            $rewriteParams["type"] = $type;
        }
        if (!IsNullOrEmpty($letter_filter)) {
            if (strlen($filter) > 0)
                $filter .= " and ";
            $filter .= " title like '$letter_filter%' ";
        }

        $this->is_main = strlen($filter) == 0;
        if (!$this->is_main) {
            if (is_numeric($city) && $city > 0)
                $filter = $this->BuildFilter($filter, "city", $city);
            $filter = " where $filter ";
            $count = SQLProvider::ExecuteQuery("select count(*) as quan from `vw__area_list_pro` $filter ");
            $count = $count[0]["quan"];
            $pages = floor($count / $this->pageSize) + (($count % $this->pageSize == 0) ? 0 : 1);
            if (($page > $pages) && ($pages > 0)) {
                $page = $pages;
                $rewriteParams["page"] = $page;
                CURLHandler::Redirect(CURLHandler::$currentPath . CURLHandler::BuildQueryParams($rewriteParams));
            }
            if ($page == 1)
                unset($rewriteParams["page"]);
            $areas = SQLProvider::ExecuteQuery(
                "select * from `vw__area_list_pro` $filter
				order by if(tbl_obj_id=$first,0,1), pro_type desc, pro_cost desc, pro_date_pay desc, title limit " . (($page - 1) * $this->pageSize) . "," . $this->pageSize);
            $areaList = $this->GetControl("areaList");
			// echo "select * from `vw__area_list_pro` $filter";
			// die('!');
		   foreach ($areas as &$area)
            {
                switch ($area["selection"]) {
                    case 1:
                        $area["selection_type"] = "color:#EE0000; font-weight:bold;";
                        break;
                    case 2:
                        $area["selection_type"] = "color:#000; font-weight:bold;";
                        break;
                    case 3:
                        $area["selection_type"] = "color:#EE0000; font-weight:bold;";
                        break;
                    default:
                        $area["selection_type"] = "color:#000; font-weight:bold;";
                        break;
                }

                $gr = SQLProvider::ExecuteQuery("select * from tbl__area_subtypes, tbl__area2subtype where area_id=" . $area["tbl_obj_id"] . " and subtype_id=tbl_obj_id");
                $area['category'] = "";
                foreach ($gr as $gkey => $value) {
                    if ($area['category'] != "")
                        $area['category'] .= " / ";
                    $area['category'] .= '<a class="common" href="/area?type=' . $value['parent_id'] . '&subtype=' . $value['tbl_obj_id'] . '">' . $value['title'] . '</a>';
                }


                $area['links'] = "";
                /*$photos = SQLProvider::ExecuteQuery("select 1 from tbl__area_photos where parent_id = ".$area['tbl_obj_id']);
                    if (sizeof($photos)) {
                        $area['links'] .= '<a class="common" href="/area/'.$area['title_url'].'?page=photos" style="margin: 0 10px 0 0">Фото</a>';
                    }
                    if ($area["youtube_video"]) {
                        $area['links'] .= '<a class="common" href="/area/'.$area['title_url'].'?page=video" style="margin: 0 10px 0 0">Видео</a>';
                    }*/
                $area["info"] = CutString(strip_tags($area["description"]), $this->descriptionSize);
                $area["resident_type"] = "area";
                $area["space_height"] = "10";
                $area['class'] = "area_table_hover";
				$area["city_item"] = (!empty($area["city_title"])) ? '<span style="color: #000;">('.$area["city_title"].')</span>' : '';
                //PRO
                $area['background']		= '0';
                $area['pro_logo']		= '';
				$area['pro_logo_prew']	= '';
                if($area['pro_type'] == 1 || $area['pro_type'] == 2) {
					$area['border']			= 'border:2px solid '.getProBackgroud('area').';';
					$area['pro_logo_prew']	= getProLogoForPreview('area');
					$area['pro_logo']		= getProLogo();
                }
            }
            $areaList->dataSource = $areas;

            //SEO text
            if (isset($subtype)) {
                $ft = SQLProvider::ExecuteQuery("select seo_text from tbl__area_subtypes where tbl_obj_id=" . $subtype);
                $ft["seo_text"] = $ft[0]["seo_text"];
            }
            else {
                $ft["seo_text"] = "";
            }
            $footerText = $this->GetControl("footerText");
            $footerText->dataSource = $ft;

            
            //setting pager
            $pager = $this->GetControl("pager");
            $pager->currentPage = $page;
            $pager->totalPages = $pages;
            $pager->rewriteParams = $rewriteParams;
        }
        else
        {
            /*recommended list*/
			//  ar.`area` resident_type, - было в запросе !!!!!!!!!!!!!!!!!!!!!!!!!! // && и не зря!!! вернул обратно
            $recommended = SQLProvider::ExecuteQuery("select ar.tbl_obj_id,ar.recommended as recommended, ar.title, ar.description, ar.logo as logo_image, 'area' resident_type, ar.title_url, city.title as city_name
                                                      from `tbl__area_doc` ar
                                                      LEFT JOIN tbl__area_city city ON ar.city = city.tbl_obj_id
                                                      where ar.recommended>0
                                                        order by recommended, ar.tbl_obj_id desc limit $this->recommendedLimit");            
           	$recommended_conts_2 = array();			
			$recommended_conts_1 = array();
			foreach($recommended as $key=>$value){
				if($value['recommended']<11){
					$recommended_conts_2[] = $value;
				}
				else{
					$recommended_conts_1[] = $value;
				}
			}

            $recomedList_1 = $this->GetControl("RecomedList_1");
            $recomedList_1->dataSource = $this->PrepareAreasMain($recommended_conts_1); 
			
			$recomedList_2 = $this->GetControl("RecomedList_2");
            $recomedList_2->dataSource = $this->PrepareAreasMain($recommended_conts_2);

            // $recomedList = $this->GetControl("RecomedList");
            // $recomedList->dataSource = $this->PrepareAreasMain($recommended);
		   /*end recommended list*/

            /*new list*/
            $new = SQLProvider::ExecuteQuery("select tbl_obj_id, title, description,
			                                    DATE_FORMAT(registration_date,'%d.%m.%y') as formatted_date,
																					'area' resident_type, title_url
																				from  `tbl__area_doc`
																				where active = 1
																				order by registration_date desc limit $this->newLimit");
            
			$new_conts_1 = array_slice($new, 0, floor(count($new)/2));
			$new_conts_2 = array_slice($new, floor(count($new)/2), count($new));
 
            $newList_1 = $this->GetControl("NewList_1");
            $newList_1->dataSource = $this->PrepareAreasMain($new_conts_1); 
			
			      $newList_2 = $this->GetControl("NewList_2");
            $newList_2->dataSource = $this->PrepareAreasMain($new_conts_2);
			
			      // $newList = $this->GetControl("NewList");
            // $newList->dataSource = $this->PrepareAreasMain($new);
            /*end new list*/

            /*news list*/
            /*
            $news = SQLProvider::ExecuteQuery("select
			                                    rn.tbl_obj_id, rn.title, DATE_FORMAT(rn.date,'%d.%m.%y') as `strdate`,
																					rn.resident_type, rn.resident_id, res.title_url
																				from `tbl__resident_news` rn
																				join `tbl__area_doc` res on res.tbl_obj_id = rn.resident_id
																				where rn.`active`=1 and rn.`resident_type`='area'
																				order by rn.`date` DESC limit $this->newsLimit");
            $newsList = $this->GetControl("NewsList");
            $newsList->dataSource = $news;
            */
        		$res_news = SQLProvider::ExecuteQuery(
                    "select rn.*, DATE_FORMAT(date,'%d.%m.%y') as `strdate`
        						 from `tbl__resident_news` rn
        												where rn.`active`=1 and rn.`resident_type`='area'
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
        			$res_news[$key]["text"] = strip_tags(CutString($res_news[$key]["text"], 90));
        			switch($val['resident_type']) {
        			case 'area': $res_news[$key]['sub'] = 'Площадка'; break;
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
																		join tbl__area_doc c on c.tbl_obj_id = ul.to_resident_id and ul.to_resident_type='area'
																		where c.active = 1
																		group by ul.to_resident_id, c.title
																		order by voted desc, tbl_obj_id limit $this->ratedLimit");
            $user = new CSessionUser("user");
            CAuthorizer::AuthentificateUserFromCookie(&$user);
            CAuthorizer::RestoreUserFromSession(&$user);

            foreach ($ratings as $key => &$rating) {
                $rating["index"] = $key + 1;
                $rating["resident_type"] = 'area';
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
            $pro2_list->dataSource = getPro2List('area');
        }

        //groups
        $groups = SQLProvider::ExecuteQuery("select
					  sg.`tbl_obj_id` AS `child_id`,
					  sg.`parent_id`,
					  sg.`title`,
					  sg.`title_url`,
					  sg.`priority`
					from
					  `tbl__area_subtypes` sg
					union
					select
					  g.`tbl_obj_id` AS `child_id`,
					  0 AS `parent_id`,
					  g.`title` AS `title`,
					  g.`title_url`,
					  g.`priority`
					from
					  `tbl__area_types` g
					ORDER by priority desc");
        foreach ($groups as $key => $value) {
            $cpars = array();
            if (!IsNullOrEmpty($area_doc_banquet_from) && !IsNullOrEmpty($area_doc_banquet_to)) {
                $cpars['area_doc_banquet_from'] = $area_doc_banquet_from;
                $cpars['area_doc_banquet_to'] = $area_doc_banquet_to;
            }
            $groups[$key]["selected"] = $value["child_id"] == $type || $value["child_id"] == $subtype ? 'id="selectBlue"' : "";
            $groups[$key]["blue"] = $value["child_id"] == $type || $value["child_id"] == $subtype ? "" : "blue";
            $groups[$key]["link"] = "/area/".$value['title_url'] . CURLHandler::BuildQueryParams($cpars);

        }
        $groupList = $this->GetControl("typeList");
        $groupList->dataSource = $groups;

        $titlefilter = array();
        $titlefilterLinks = array();
        if (isset($type)) {
            $type_finded = false;
            $subtype_finded = false;
            foreach ($groups as $gr) {
                if ($gr["child_id"] == $type) {
                    $urlparams = array();
                    $urlparams["type"] = $gr["child_id"];
                    $link = "/area/" . CURLHandler::BuildQueryParams($urlparams);
                    array_unshift($titlefilter, CStringFormatter::buildCategoryLinks($gr['title'], null));
                    array_unshift($titlefilterLinks, CStringFormatter::buildCategoryLinks($gr['title'], $link, "area"));
                    $type_finded = true;
                }
                else if ($gr["child_id"] == $subtype) {
                    $urlparams = array();
                    $urlparams["type"] = $gr["parent_id"];
                    $urlparams["subtype"] = $gr["child_id"];
                    $link = "/area/" . CURLHandler::BuildQueryParams($urlparams);
                    array_push($titlefilter, CStringFormatter::buildCategoryLinks($gr['title'], null));
                    array_push($titlefilterLinks, CStringFormatter::buildCategoryLinks($gr['title'], $link, "area"));
                    $subtype_finded = true;
                }
            }
            if (!$type_finded || (isset($subtype) && !$subtype_finded))
                CURLHandler::ErrorPage();
        }

        $titlefil = $this->GetControl("titlefilter");
        if (sizeof($titlefilter))
            $titlefil->text = implode(" / ", $titlefilter) . " - ";
        if (sizeof($titlefilterLinks))
            $this->GetControl("titlefilterLinks")->html = '<div class="titlefilter area">' . implode(" / ", $titlefilterLinks) . '</div>';
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

        //setting capacity filter
        $cpars = $rewriteParams;
        unset($cpars["area_doc_banquet_from"]);
        unset($cpars["area_doc_banquet_to"]);
        $url = CURLHandler::$currentPath . CURLHandler::BuildRewriteParams($cpars);
        if (!IsNullOrEmpty($area_doc_banquet_to)) {
            $capacityFilter = $this->buildCapacityFilter($url, "ВСЕ");
        }
        else {
            $capacityFilter = $this->buildCapacityFilter($url, "ВСЕ", false, true);
        }

        $capacityRanges = array(
            array("from" => 0, "to" => 10, "title" => "0-10"),
            array("from" => 10, "to" => 50, "title" => "10-50"),
            array("from" => 50, "to" => 100, "title" => "50-100"),
            array("from" => 100, "to" => 200, "title" => "100-200"),
            array("from" => 200, "to" => 300, "title" => "200-300"),
            array("from" => 300, "to" => 400, "title" => "300-400"),
            array("from" => 400, "to" => 500, "title" => "400-500"),
            array("from" => 500, "to" => 600, "title" => "500-600"),
            array("from" => 600, "to" => 700, "title" => "600-700"),
            array("from" => 700, "to" => 800, "title" => "700-800"),
            array("from" => 800, "to" => 900, "title" => "800-900"),
            array("from" => 900, "to" => 1000, "title" => "900-1000"),
            array("from" => 1000, "to" => 1500, "title" => "1000-1500"),
            array("from" => 1500, "to" => 10000, "title" => "1500-..."),
        );

        foreach ($capacityRanges as $range) {
            $urlParams = $rewriteParams;
            $urlParams['area_doc_banquet_from'] = $range["from"];
            $urlParams['area_doc_banquet_to'] = $range["to"];
            $url = CURLHandler::$currentPath . CURLHandler::BuildQueryParams($urlParams);
            if (!($area_doc_banquet_to == $range['to'])) {
                $capacityFilter .= $this->buildCapacityFilter($url, $range["title"], $range["from"] >= 100);
            }
            else {
                $capacityFilter .= $this->buildCapacityFilter($url, $range['title'], $range["from"] >= 100, true);
            }
        }

        $this->GetControl("capacityFilter")->html = $capacityFilter;
		
        
        /*
        $mainMenu = $this->GetControl("menu");
        $mainMenu->dataSource["shelk"] =
				array("link" => "http://shelkevent.ru/",
				"imgname" => "shelk",
				"title"=>"",
				"ads_class"=>"reklama",
				"target" => "target='_blank'");
    		*/
        
        
        $areasearch = $this->GetControl("areasearch");
        $areasearch->headerTemplate =
          '<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>';
        $areasearch->footerTemplate =
         '<td class="area"><div style="text-align:justify !important;"><p>
          <a href="" class="area" onclick="javascript: DlgByPlace(); return false;">Поиск по местоположению</a>&nbsp;/&nbsp;<a href="" class="area" onclick="javascript: DlgByCapacity(); return false;">Поиск по вместимости</a>&nbsp;/&nbsp;<a href="" class="area" onclick="javascript: DlgByCost(); return false;">Поиск по стоимости</a>&nbsp;/&nbsp;<a href="" class="area" onclick="javascript: DlgAdditional(); return false;">Расширенный поиск</a></p></div></td></tr></table>';
        
         $capacityRanges = array(
            array("from"=>0,"to"=>10,"title"=>"до 10", "checked"=>""),
            array("from"=>10,"to"=>50,"title"=>"10-50", "checked"=>""),
            array("from"=>50,"to"=>100,"title"=>"50-100", "checked"=>""),
            array("from"=>100,"to"=>200,"title"=>"100-200", "checked"=>""),
            array("from"=>200,"to"=>300,"title"=>"200-300", "checked"=>""),
            array("from"=>300,"to"=>400,"title"=>"300-400", "checked"=>""),
            array("from"=>400,"to"=>500,"title"=>"400-500", "checked"=>""),
            array("from"=>500,"to"=>600,"title"=>"500-600", "checked"=>""),
            array("from"=>600,"to"=>700,"title"=>"600-700", "checked"=>""),
            array("from"=>700,"to"=>800,"title"=>"700-800", "checked"=>""),
            array("from"=>800,"to"=>900,"title"=>"800-900", "checked"=>""),
            array("from"=>900,"to"=>1000,"title"=>"900-1000", "checked"=>""),
            array("from"=>1000,"to"=>1500,"title"=>"1000-1500", "checked"=>""),
            array("from"=>1500,"to"=>10000,"title"=>"1500-...", "checked"=>"")
        );
    
        $caps = $this->GetControl("capacityList");
        $caps->dataSource = $capacityRanges;
    
        $costRanges = array(
            array("from"=>0,"to"=>1000,"title"=>"до 1000 руб."),
            array("from"=>1000,"to"=>1500,"title"=>"1000-1500 руб."),
            array("from"=>1500,"to"=>2000,"title"=>"1500-2000 руб."),
            array("from"=>2000,"to"=>2500,"title"=>"2000-2500 руб."),
            array("from"=>2500,"to"=>3000,"title"=>"2500-3000 руб."),
            array("from"=>3000,"to"=>3500,"title"=>"3000-3500 руб."),
            array("from"=>3500,"to"=>99999,"title"=>"более 3500 руб.")
        );
    
        $costs = $this->GetControl("costList");
        $costs->dataSource = $costRanges;

        
        $this->GetControl("jsArea")->dataSource = array(
            "show_metro"=> (empty($city) or $city == 204)?"true":"false",
            "city"=>isset($city)?"$city":"204",
            "cap_list"=>$caps->RenderHTML(),
            "cost_list"=>$costs->RenderHTML(),
            "params"=>CURLHandler::BuildQueryParams($rewriteParams)
        );    
		
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

    private function buildCapacityFilter($url, $title, $moreThenHundred = false, $active = false)
    {
        if ($moreThenHundred) {
            if ($active) {
                return "<td class='active_area_bg_large'><a class='alterItem1' href='$url'>$title</a></td>";
            }
            return "<td class='areabg1'><a class='alterItem1' href='$url'>$title</a></td>";
        }
        else {
            if ($active) {
                return "<td class='active_area_bg'><a class='alterItem' href='$url'>$title</a></td>";
            }
            else {
                return "<td class='areabg'><a class='alterItem' href='$url'>$title</a></td>";
            }
        }
    }
}

?>
