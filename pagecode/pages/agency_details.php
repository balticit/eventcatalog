<?php
class agency_details_php extends CPageCodeHandler
{
    private $id = 0;
    public $pagesize = 25;
    public $recommendedLimit = 20;
    public $newLimit = 6;
    public $newsLimit = 9;
    public $ratedLimit = 15;
    public $is_main = true;
    public $descriptionSize = 256;
    public $activity_id = null;
    public $is_activity = false;

    public function agency_details_php()
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
    
    
    
        /*Провека адреса*/
        $id_str = GP("id");
        //Costyl!!!!
        if (!is_null($id_str)) {
            //Can be activity url
			if($id_str=='cvadebnye_agentstva'){
				CURLHandler::Redirect301('/agency/svadebnye_agentstva');
			}
            $actid = SQLProvider::ExecuteScalar("select tbl_obj_id from tbl__agency_type where title_url='" . mysql_real_escape_string($id_str) . "'");
            if (!IsNullOrEmpty($actid)) {
                $this->is_activity = true;
                $this->activity_id = $actid;
            }
            else {
                //is activity
                $this->is_activity = false;
                $this->id = SQLProvider::ExecuteScalar("select tbl_obj_id from tbl__agency_doc where title_url = '" . mysql_real_escape_string($id_str) . "'");
            }
        }

        if($this->is_activity){
            $metadata = $this->GetControl("metadata");
            $metadata->keywords = "эвент-агентство, программа мероприятий";
            $metadata->description = "Эвент-агентства – категории, контакты и услуги. Проведение мероприятий «под ключ»: подбор программы мероприятия, площадки, оформления и представления. Рейтинг и отзывы эвент-агентств.";
            $av_rwParams = array("page", "letter");
            CURLHandler::CheckRewriteParams($av_rwParams);
            $rewriteParams = $_GET;
            $page = GP("page", 1);
            $first = "NULL";
            $city = GP("city");
            $activity = $this->activity_id;
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
					order by priority desc, title ASC, if(tbl_obj_id=$first,0,1), pro_type desc, pro_cost desc, pro_date_pay desc, title limit $sp,$this->pagesize");
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
            $activities = SQLProvider::ExecuteQuery("select `tbl_obj_id` as kind_of_activity, title,title_url from  `tbl__agency_type` order by priority desc");
            foreach ($activities as $key => $value) {
                $cpars = $rewriteParams;
                $activities[$key]["selected"] = $value["kind_of_activity"] == $activity ? 'id="selectGreen"' : "";
                $activities[$key]["green"] = $value["kind_of_activity"] == $activity ? '' : "green";
                unset($cpars['activity']);
                $activities[$key]["link"] = "/agency/".$value['title_url'] . CURLHandler::BuildQueryParams($cpars);
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
        } else {
        $av_rwParams = array("id", "add", "delete");
        CURLHandler::CheckRewriteParams($av_rwParams);
		if (!is_null($id_str)){
			$this->id = SQLProvider::ExecuteScalar("select tbl_obj_id from tbl__agency_doc where title_url = '".mysql_real_escape_string($id_str)."'");
			$this->last_visit_date = SQLProvider::ExecuteScalar("select last_visit_date from tbl__agency_doc where title_url = '".mysql_real_escape_string($id_str)."'");
		}
		if (is_numeric($this->id))
			SQLProvider::ExecuteNonReturnQuery("update tbl__agency_doc set visited = ifnull(visited,0)+1 where tbl_obj_id=$this->id");
		else
			CURLHandler::ErrorPage();

    $unit = SQLProvider::ExecuteQuery("select * from  vw__agency_list3 a where a.tbl_obj_id=$this->id");
    if (sizeof($unit)==1)
		  $unit = $unit[0];
	  else
		  CURLHandler::ErrorPage();
          /* доп блоки */
                $unit['reg_date'] = onSiteTime($unit['registration_date']);
                $unit['last_visit_date'] = lastVisitSite($this->last_visit_date,$unit['registration_date']);
                $unit['description'] = (!empty($unit['description'])?'<h4 class="detailsBlockTitle"><a name="description">Описание</a></h4>'.$unit['description']:'');
          /**/
		$pro_type = getProType('agency', $this->id);
		$unit['pro_logo_prew'] = '';
		if($pro_type == 1 || $pro_type == 2) {
			$unit['pro_logo_prew'] = getProLogoForPreview('agency');
		}
		
		$agencyTypes = SQLProvider::ExecuteQuery("SELECT t.* FROM tbl__agency_type t, tbl__agency2activity a WHERE a.kind_of_activity = t.tbl_obj_id and a.tbl_obj_id=$this->id");
        $agencyTypeLinks = array();
        foreach ($agencyTypes as $aType) {
			$kind_of_activity = $aType['tbl_obj_id'];
            $link = '/agency/'.$aType['title_url'];
            array_push($agencyTypeLinks, CStringFormatter::buildCategoryLinks($aType['title'], $link, "commom"));
        }
        $unit['activity_title'] = implode(", ",$agencyTypeLinks);		

        $user = new CSessionUser("user");
        CAuthorizer::AuthentificateUserFromCookie(&$user);
        CAuthorizer::RestoreUserFromSession(&$user);
        $show_ilike = true;
        $btn_action = "ShowLikeMessage(); return false;";
        $unit["ShowMsgMessage"] = 'onclick="javascript: ShowMsgMessage(); return false;"';
        $fav_add = true;
        if ($user->authorized) {
            $btn_action = "$(this).parent().submit();";
            $unit["ShowMsgMessage"] = "";

            $fav = SQLProvider::ExecuteQuery("select 1 from tbl__user_favorites
                                            where user_type = '".$user->type."'
                                            and user_id = ".$user->id."
                                            and resident_type = 'agency'
                                            and resident_id=$this->id");
            if (sizeof($fav))
              $fav_add = false;

            if (GP("add")=="favorite") {
                SQLProvider::ExecuteNonReturnQuery("insert into tbl__user_favorites (user_type,user_id,resident_type,resident_id)
                    values ('".$user->type."',".$user->id.",'agency',$this->id)");
                CURLHandler::Redirect("/agency/".$id_str);
            }
			if (GP("delete")=="favorite") {
					SQLProvider::ExecuteNonReturnQuery("delete from tbl__user_favorites
					where tbl__user_favorites.user_type='".$user->type."' and tbl__user_favorites.user_id=".$user->id." and
					tbl__user_favorites.resident_type='agency' and tbl__user_favorites.resident_id=".$this->id);
					CURLHandler::Redirect("/agency/$id_str");
			}

            $currentmark = SQLProvider::ExecuteScalar("select count(1) from tbl__userlike where
            to_resident_type='agency' and to_resident_id='$this->id' and from_resident_type='".$user->type."' and from_resident_id='".$user->id."'");
            $show_ilike = $currentmark==0;
            if ($show_ilike and GP('action')=='ilike')
            {
                    SQLProvider::ExecuteQuery("
                        insert into tbl__userlike
                        (to_resident_id,to_resident_type,from_resident_id,from_resident_type,date)
                        values
                        ($this->id,'agency',".$user->id.",'".$user->type."',FROM_UNIXTIME(".time()."))
                        ");

                    //отправка сообщения резиденту об оценке
                    $residend_info = SQLProvider::ExecuteQuery(
                        "SELECT u.tbl_obj_id, u.login_type, u.email
                        FROM `vw__all_users` u\n
                        where u.login_type = 'agency' and u.tbl_obj_id = $this->id");
                    if (sizeof($residend_info)) {
                      $app = CApplicationContext::GetInstance();
                      $link = $_SERVER['HTTP_HOST'].'/'.$residend_info[0]['login_type'].'/'.$id_str;
                      $mtitle = iconv($app->appEncoding,"utf-8","Вы понравились пользователю на портале eventcatalog.ru");
                      $mbody = iconv($app->appEncoding,"utf-8",'<div id="content"><p>Уважаемый резидент!</p>'.
                      '<p>Вы понтравились пользователю.</p>'.
                      '<p>Вы можете посмотреть кому, перейдя по ссылке: <a target="_blank" href="http://'.$link.'">http://'.$link .'</a></p>'.
                      '<p>С уважением,<br />'.
                      'EVENTКАТАЛОГ<br />'.
                      'Ежедневный помощник организаторов мероприятий.</p></div>');
                      SendHTMLMail($residend_info[0]['email'],$mbody,$mtitle);
                    }

                CURLHandler::Redirect("/agency/".$id_str);
            }
            if (!$show_ilike and GP('action')=='unlike')
			{

                SQLProvider::ExecuteQuery("
                        delete from tbl__userlike
                        where to_resident_id = $this->id
                          and to_resident_type = 'agency'
                          and from_resident_id = ".$user->id."
                          and from_resident_type = '".$user->type."'");

                CURLHandler::Redirect("/agency/".$id_str);
            }
        }
        if ($show_ilike) {
			$unit["btn_i_like"]='<form method="post"><input type="hidden" name="action" value="ilike">
			<img class="btn_ilike" src="/images/rating/btn_ilike.png" onmouseover="javascript: this.src=\'/images/rating/btn_agency.png\';" onmouseout="javascript: this.src=\'/images/rating/btn_ilike.png\';" onclick="javascript: '.$btn_action.'"></form>';
		}
        else {
            $unit["btn_i_like"]='</td><td><form method="post"><input type="hidden" name="action" value="unlike"><a href="" class="black" onclick="javascript: $(this).parent().submit(); return false;"><img onmouseover="javascript: this.src=\'/images/rating/unlike_agency.png\';" onmouseout="javascript: this.src=\'/images/rating/btn_unlike.png\';" src="/images/rating/btn_unlike.png" alt="Больше не рекомендую" /></a></form>';
        }

        $mark = SQLProvider::ExecuteQuery("select au.user_id,au.type,au.title from tbl__userlike ul
                                           join vw__all_users_full au
                                               on au.type = ul.from_resident_type and
                                                  au.user_id = ul.from_resident_id
                                           where to_resident_type='agency' and to_resident_id='$this->id'");
        $mark_cnt = 0;
        $mark_links = "";
        foreach($mark as $m_item) {
            $mark_cnt++;
            if ($mark_links)
              $mark_links .= ", ";
            $mark_links .= '<a rel="nofollow" class="user_like_link" href="/u_profile/?type='.$m_item['type'].'&id='.$m_item['user_id'].'">'.$m_item['title'].'</a>';
        }
        $unit["voted"] = "";
        if ($mark_cnt>0) {
			$u_text = " ";
			if ($mark_cnt == 1)
				$u_text = " ";

			$unit["voted"] = "<div class=\"user_liked\">Рекомендуют <span class=\"user_liked_num\">$mark_cnt</span> $u_text: <span class=\"user_like_link\">$mark_links</span></div>";
		}

    if ($fav_add) {
        $msg = "";
			if (!$user->authorized) $msg = 'onclick="javascript: ShowFavMessage(); return false;"';
		$unit["fav_link"] = '<a class="agency in_favorite" href="/agency/'.$id_str.'?add=favorite" '.$msg.'><span>Добавить в избранное</span></a>';
	}
      else {
        $msg = "";
			if (!$user->authorized) $msg = 'onclick="javascript: ShowFavMessage(); return false;"';
		$unit["fav_link"] = '<a class="agency out_favorite" href="/agency/'.$id_str.'?delete=favorite" '.$msg.'><span>Убрать из избранного</span></a>';
	}


		$unit["u_link"] = "";
		$u_links = SQLProvider::ExecuteQuery("select ru.tbl_obj_id, IF(ru.nikname is NULL or ru.nikname = '',ru.title,ru.nikname) title from tbl__registered_user_link_resident rl left join tbl__registered_user ru on ru.tbl_obj_id = rl.user_id
		                                      where rl.resident_type = 'agency' and rl.resident_id = $this->id");
		if (sizeof($u_links)>0 && !empty($pro_type))
		{
			$unit["u_link"] = "<div style=\"padding-bottom: 20px;\"><b>Представители агентства:</b><br />";
			foreach ($u_links as $num=>$u_link)
			{
				$unit["u_link"] .= "<a href=\"/u_profile/?type=user&id=".$u_link["tbl_obj_id"]."\">".$u_link["title"]."</a><br />";
			}
			$unit["u_link"] .= "</div>";
		}


    //news
    $news = SQLProvider::ExecuteQuery("select rn.*,DATE_FORMAT(date,'%d.%m.%y') as `strdate` from tbl__resident_news rn where active=1
                                       and resident_id=$this->id and resident_type='agency' order by rn.date desc");
    $unit["news_list"]='';
    if(!empty($news)){
        $unit["news_list"] = '<h4 class="detailsBlockTitle"><a name="news">Новости</a></h4>';
			foreach($news as $item) {
				$item["title"] = CutString($item["title"]);
				$item["text"] = strip_tags(CutString($item["text"], 150));
				
				if (IsNullOrEmpty($item["logo_image"]))
				$item["logo_image"] = "/images/nologo.png";
  		  else
  		  $item["logo_image"] = "/upload/".GetFileName($item["logo_image"]);
				
				$unit["news_list"] .= getNewsItem($item);
			}
    }
    else $unit["news_list"] .= '';

    /* похожие подрядчики */ 
		// var_dump($unit);
		// die('!');
        //$similar = SQLProvider::ExecuteQuery('select * from `tbl__agency_doc` where active=1 order by rand() LIMIT 4');
        $is_kind_of_activity = !empty($kind_of_activity) ? $kind_of_activity : 0;  
		//var_dump($is_kind_of_activity);
		$isRecomended = SQLProvider::ExecuteScalar("SELECT recommended from tbl__agency_doc where tbl_obj_id=".$unit['tbl_obj_id']);
		$similar = SQLProvider::ExecuteQuery('
										SELECT * FROM `tbl__agency_doc` 
										WHERE tbl_obj_id in (
											SELECT tbl_obj_id 
											FROM tbl__agency2activity a2a 
											WHERE a2a.kind_of_activity = '.$is_kind_of_activity.') 
										AND tbl_obj_id != '.$unit['tbl_obj_id'].' 
										AND active=1 
										ORDER BY rand() 
										LIMIT 4');
        
		if(!empty($similar)&&$isRecomended==0){
            $unit["similar"] = '<h4 class="detailsBlockTitle"><a name="similar">Похожие агентства</a></h4>
                <table width="100%" class="similarBlock">';
            foreach($similar as $i=>$s){
                $unit["similar"].= ($i%2 == 0?'<tr>':'').
                                    '<td><div class="logo_wrap"><img src="/upload/'.$s['logo_image'].'" class="logo120border" alt="" title=""></div><strong><a href="/agency/'.$s['title_url'].'">'.$s['title'].'</a></strong><br/>'.substr(strip_tags($s['description']),0,125).'</td>'.
                    ($i%2!= 0?'</tr>':'');
            }
            $unit["similar"].='</table>';
        }
		$unit["similar"] = !empty($unit["similar"]) ? $unit["similar"] : '';
    
    $agenDetails = $this->GetControl("agenDetails");
    
    $photos = $this->GetControl("photos");
    $photos->dataSource = SQLProvider::ExecuteQuery(
      "select p.*
		  from `tbl__agency_photos`  ap
			join `tbl__photo` p on ap.child_id = p.tbl_obj_id
			where parent_id=$this->id limit 8");
    $unit["photos"] = $photos->Render();
    
    //video load
            $matches = array();
            $matches_2 = array();
            $matches_3 = array();
            if (strlen($unit['youtube_video']) > 0 && (preg_match('/^http:\/\/[w\.]*youtube\.com\/watch\?v=([A-z0-9-_]+).*$/i', $unit['youtube_video'], $matches) > 0)) {
                $unit["video_visible"] = "";
                $unit["youtubevideo"] = $matches[1];
            }
            else {
                $_URL = explode("/", $unit['youtube_video']);
                if($_URL("embed",$_URL)) {
                  $lvl = count($_URL)-1;
	                $unit["youtubevideo"] = $_URL[$lvl];
                }
                else {
                  $needle = "youtu.be/";
                  $pos = null;
                  $pos = strpos($unit['youtube_video'], $needle);
                  if ($pos !== false) {
                      $start = $pos + strlen($needle);
                      $unit["youtubevideo"] = substr($unit['youtube_video'], $start, 11);
                  }
                  else {
                  $unit["video_visible"] = 'style="display: none;"';
                  }
                }
            }
            
            if (strlen($unit['youtube_video_2']) > 0 && (preg_match('/^http:\/\/[w\.]*youtube\.com\/watch\?v=([A-z0-9-_]+).*$/i', $unit['youtube_video_2'], $matches_2) > 0)) {
                $unit["video_visible_2"] = "";
                $unit["youtubevideo_2"] = $matches_2[1];
            }
            else {
                $_URL = explode("/", $unit['youtube_video_2']);
                if($_URL("embed",$_URL)) {
                  $lvl = count($_URL)-1;
	                $unit["youtubevideo_2"] = $_URL[$lvl];
                }
                else {
            
                  $needle = "youtu.be/";
                  $pos = null;
                  $pos = strpos($unit['youtube_video_2'], $needle);
                  if ($pos !== false) {
                      $start = $pos + strlen($needle);
                      $unit["youtubevideo_2"] = substr($unit['youtube_video_2'], $start, 11);
                  }
                  else {
                  $unit["video_visible_2"] = 'style="display: none;"';
                  }
                }
            }
            
            if (strlen($unit['youtube_video_3']) > 0 && (preg_match('/^http:\/\/[w\.]*youtube\.com\/watch\?v=([A-z0-9-_]+).*$/i', $unit['youtube_video_3'], $matches_3) > 0)) {
                $unit["video_visible_3"] = "";
                $unit["youtubevideo_3"] = $matches_3[1];
            }
            else {
                else {
                $_URL = explode("/", $unit['youtube_video_3']);
                if($_URL("embed",$_URL)) {
                  $lvl = count($_URL)-1;
	                $unit["youtubevideo_3"] = $_URL[$lvl];
                }
                else {
            
                  $needle = "youtu.be/";
                  $pos = null;
                  $pos = strpos($unit['youtube_video_3'], $needle);
                  if ($pos !== false) {
                      $start = $pos + strlen($needle);
                      $unit["youtubevideo_3"] = substr($unit['youtube_video_3'], $start, 11);
                  }
                  else {
                  $unit["video_visible_3"] = 'style="display: none;"';
                  }
                }
            }
    
    //Remove direct
    if ($unit['direct'] == 1) {
        $this->GetControl('yaPersonal')->template = "";
        $this->GetControl('topLine')->template = "";
        $unit["description"] = nl2br($unit["description"]);
        $unit["similar"] ="";
    }		
    else {
      $unit["description"] = nl2br(strip_tags($unit["description"]));
    }
            
		$agenDetails->dataSource = $unit;
		
		
		if ( $unit['direct'] == 1) {
        $this->GetControl('yaPersonal')->template = "";
    }			
    

		if( is_numeric($unit['priority'] )) {
		 if($unit['priority'] != 0) { $this->GetControl('yaPersonal')->template = ""; }
    }
    else {
			if (!IsNullOrEmpty($unit['priority'])){
        $this->GetControl('yaPersonal')->template = "";
      }
    }


    /* Baltic IT */
    $agencyLists = SQLProvider::ExecuteQuery("SELECT t.* FROM tbl__agency_type t, tbl__agency2activity a WHERE a.kind_of_activity = t.tbl_obj_id and a.tbl_obj_id=$this->id");
    $agencyArrayTitle = array();
    foreach ($agencyLists as $aType) {
        array_push($agencyArrayTitle,$aType['title']);
    }
    $agency_list = implode(", ",$agencyArrayTitle);
    /* End Baltic It*/
    
    $title = $this->GetControl("title");
		$title->text = $unit["title"]." - ".$agency_list; /* Add in Title Type List */
		
		
		$unit["logo_visible"] = IsNullOrEmpty( $unit["logo_image"])?"hidden":"visible";
		//cities
        $city = $unit["city"];

		//kind of activity
		$activities  = SQLProvider::ExecuteQuery("select `tbl_obj_id` as kind_of_activity, title,title_url from  `tbl__agency_type` order by priority desc");
		foreach ($activities as $key => $value) {
			$activities[$key]["link"] = "/agency/".$value['title_url'];
			$activities[$key]["green"] = "green";
		}
		$activityList = $this->GetControl("activityList");
		$activityList->dataSource = $activities;

    $mainMenu = $this->GetControl("menu");
    $mainMenu->dataSource["polymedia"] =
      array("link"=>"http://www.polymedia.ru/",
            "imgname"=>"polymedia",
            "title"=>"",
            "target"=>'target="_blank"');
			
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
}
?>
