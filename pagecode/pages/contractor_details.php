<?php
class contractor_details_php extends CPageCodeHandler
{
    private $id = 0;
    public $is_type = false;
    public $type_id = null;
    public $pageSize = 25;
    public $recommendedLimit = 10;
    public $newLimit = 7;
    public $newsLimit = 9;
    public $ratedLimit = 15;
    public $is_main = true;
    public $descriptionSize = 256;

    public function contractor_details_php()
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
        if (!is_null($id_str)) {
            //can be type
            $typeid = SQLProvider::ExecuteScalar("select tbl_obj_id from tbl__activity_type where title_url='".mysql_real_escape_string($id_str)."'");
            if(IsNullOrEmpty($typeid)){
                $this->is_type = false;
                $this->id = SQLProvider::ExecuteScalar("select tbl_obj_id from tbl__contractor_doc where title_url = '" . mysql_real_escape_string($id_str) . "'");
            }
            else{
                $this->is_type = true;
                $this->type_id = $typeid;
            }
        }
        if($this->is_type){
            $metadata = $this->GetControl("metadata");
            $metadata->keywords = "подрядчики, организация мероприятий, организация";
            $metadata->description = "Полный список надежных подрядчиков, предоставляющих свет, звук, оформление помещений и открытых площадок для организации мероприятия. Контакты и подробные отзывы о каждом организаторе.";

            $app = CApplicationContext::GetInstance();
            /*Провека адреса*/
            $av_rwParams = array("activity", "page", "letter");
            CURLHandler::CheckRewriteParams($av_rwParams);
            $rewriteParams = $_GET;
            $page = GP("page", 1);
            $city = GP("city");
			$letter = GP("letter");
            $activity = $this->type_id;
            unset($rewriteParams['activity']);
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
				order by priority desc, title ASC, if(tbl_obj_id=$first,0,1),
				pro_type desc, pro_cost desc, pro_date_pay desc, title limit $rp,$this->pageSize");				                
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
                        $contractor['category'] .= '<a class="common" href="/contractor/' . $value['title_url'] . '">' . $value['title'] . '</a>';
                    }

                    $contractor["info"] = CutString(strip_tags($contractor["short_description"]), $this->descriptionSize);
					//Pro
                    $contractor['links'] = "";
                    $contractor['resident_type'] = 'contractor';
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
                
                if((!isset($page)||$page==1)&&$activity==1141){                    
                    $mf = SQLProvider::ExecuteQuery("select * from `vw__contractor_list_pro` where tbl_obj_id=7826");                    
                    $mf=$mf[0];
                    $mf["logo"] = $mf["logo_image"];
                    $mf["city_item"] = (!empty($mf["city_name"])) ? '<span style="color: #000;">('.$mf["city_name"].')</span>' : '';
                    $mf["class"] = "contractor_table_hover";
                    if ($mf["title"][0] != $letter) {
                        $mf["space_height"] = 15;
                        $letter = $mf["title"][0];
                    }
                    else
                        $mf["space_height"] = 5;

                    switch ($mf["selection"]) {
                        case 1:
                            $mf["selection_type"] = "color:#EE0000; font-weight:bold;";
                            break;
                        case 2:
                            $mf["selection_type"] = "color:#000; font-weight:bold;";
                            break;
                        case 3:
                            $mf["selection_type"] = "font-weight:bold; color:#EE0000;";
                            break;
                        default:
                            $mf["selection_type"] = "color:#000; font-weight:bold;";
                            break;
                    }
                    $gr = SQLProvider::ExecuteQuery("select act.* from tbl__activity_type act, tbl__contractor2activity ca where ca.tbl_obj_id=" . $mf["tbl_obj_id"] . " and ca.kind_of_activity=act.tbl_obj_id");
                    $mf['category'] = "";
                    foreach ($gr as $gkey => $value) {
                        if ($mf['category'] != "")
                            $mf['category'] .= " / ";
                        $mf['category'] .= '<a class="common" href="/mf/' . $value['title_url'] . '">' . $value['title'] . '</a>';
                    }

                    $mf["info"] = CutString(strip_tags($mf["short_description"]), $this->descriptionSize);
                    //Pro
                    $mf['links'] = "";
                    $mf['resident_type'] = 'mf';
                    $mf['background']       = '0';
                    $mf['pro_logo']         = '';
                    $mf['pro_logo_prew']    = '';
                    if($mf['pro_type'] == 1 || $mf['pro_type'] == 2){
                        $mf['activeEl'] = 'activeEl';
                        $mf['border']           = 'border:2px solid '.getProBackgroud('contractor').';';
                        $mf['pro_logo_prew']    = getProLogoForPreview('contractor');
                        $mf['pro_logo']         = getProLogo();
                    }
                    $change = $contractors[2];
                    foreach ($contractors as $key => $value) {
                        if($value['tbl_obj_id']==7826){
                            $contractors[$key] = $change;
                        }                                        
                                    }                
                    $contractors[2] = $mf;

                }

                if((!isset($page)||$page==1)&&($activity==139||$activity==140)){
                    $mf = SQLProvider::ExecuteQuery("select * from `vw__contractor_list_pro` where tbl_obj_id=7825");                    
                    $mf=$mf[0];
                    $mf["logo"] = $mf["logo_image"];
                    $mf["city_item"] = (!empty($mf["city_name"])) ? '<span style="color: #000;">('.$mf["city_name"].')</span>' : '';
                    $mf["class"] = "contractor_table_hover";
                    if ($mf["title"][0] != $letter) {
                        $mf["space_height"] = 15;
                        $letter = $mf["title"][0];
                    }
                    else
                        $mf["space_height"] = 5;

                    switch ($mf["selection"]) {
                        case 1:
                            $mf["selection_type"] = "color:#EE0000; font-weight:bold;";
                            break;
                        case 2:
                            $mf["selection_type"] = "color:#000; font-weight:bold;";
                            break;
                        case 3:
                            $mf["selection_type"] = "font-weight:bold; color:#EE0000;";
                            break;
                        default:
                            $mf["selection_type"] = "color:#000; font-weight:bold;";
                            break;
                    }
                    $gr = SQLProvider::ExecuteQuery("select act.* from tbl__activity_type act, tbl__contractor2activity ca where ca.tbl_obj_id=" . $mf["tbl_obj_id"] . " and ca.kind_of_activity=act.tbl_obj_id");
                    $mf['category'] = "";
                    foreach ($gr as $gkey => $value) {
                        if ($mf['category'] != "")
                            $mf['category'] .= " / ";
                        $mf['category'] .= '<a class="common" href="/mf/' . $value['title_url'] . '">' . $value['title'] . '</a>';
                    }

                    $mf["info"] = CutString(strip_tags($mf["short_description"]), $this->descriptionSize);
                    //Pro
                    $mf['links'] = "";
                    $mf['resident_type'] = 'mf';
                    $mf['background']       = '0';
                    $mf['pro_logo']         = '';
                    $mf['pro_logo_prew']    = '';
                    if($mf['pro_type'] == 1 || $mf['pro_type'] == 2){
                        $mf['activeEl'] = 'activeEl';
                        $mf['border']           = 'border:2px solid '.getProBackgroud('contractor').';';
                        $mf['pro_logo_prew']    = getProLogoForPreview('contractor');
                        $mf['pro_logo']         = getProLogo();
                    }
                    $change = $contractors[2];
                    foreach ($contractors as $key => $value) {
                        if($value['tbl_obj_id']==7825){
                            $contractors[$key] = $change;
                        }                                        
                                    }                
                    $contractors[2] = $mf;
                }

                $catList->dataSource = $contractors;
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
                $value["link"] = "/contractor/" .$value['title_url'];
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
                unset($rewriteParams['activity']);
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
        else{
        $av_rwParams = array("id", "add", "delete");
        CURLHandler::CheckRewriteParams($av_rwParams);
        if (is_numeric($this->id))
            SQLProvider::ExecuteNonReturnQuery("update tbl__contractor_doc set visited = ifnull(visited,0)+1 where tbl_obj_id=$this->id");
        else
            CURLHandler::ErrorPage();

        $unit = SQLProvider::ExecuteQuery("
		  select
			    `c`.`tbl_obj_id` AS `tbl_obj_id`,
				`c`.`title` AS `title`,
				`c`.`kind_of_activity` AS `kind_of_activity`,
				`c`.`short_description` AS `short_description`,
				`c`.`description` AS `description`,
				`c`.`address` AS `address`,
				`c`.`phone` AS `phone`,
				`c`.`site_address` AS `site_address`,
				`c`.`last_visit_date` AS `last_visit_date`,
				`c`.`email` AS `email`,
				`c`.`selection` AS `selection`,
				`c`.`logo_image` AS `logo_image`,
				`c`.`phone2` AS `phone2`,
				`c`.`city` AS `city`,
				`c`.`priority` AS `priority`,
	      `c`.`direct` AS `direct`,
				`c`.`other_city` AS `other_city`,
				`ct`.`title` AS `city_name`,
                                `c`.`registration_date`,
				`c`.`youtube_video` as `youtube_video`,
				`c`.`youtube_video_2` as `youtube_video_2`,
				`c`.`youtube_video_3` as `youtube_video_3`,
				group_concat(`a`.`title` separator '|') AS `activity_title`,
				group_concat(`a`.`tbl_obj_id` separator '|') AS `activity_ids`,
				group_concat(`a`.`title_url` separator '|') AS `activity_urls`
				from `tbl__contractor_doc` `c`
                left join `tbl__contractor2activity` `c2a` on `c2a`.`tbl_obj_id` = `c`.`tbl_obj_id`
                left join `tbl__activity_type` `a` on `a`.`tbl_obj_id` = `c2a`.`kind_of_activity`
                left join `tbl__city` `ct` on `c`.`city` = `ct`.`tbl_obj_id`
                where c.tbl_obj_id = $this->id
			  group by
			    `c`.`tbl_obj_id`,
					`c`.`title`,
					`c`.`description`,
					`c`.`city`,
					`ct`.`title`,
					`c`.`kind_of_activity`,
					`c`.`short_description`,
					`c`.`last_visit_date`,
					`c`.`address`,
					`c`.`phone`,
					`c`.`site_address`,
					`c`.`email`,
					`c`.`selection`,
					`c`.`logo_image`,
					`c`.`phone2`,`c`.`youtube_video`");

		if (sizeof($unit)==1)
		  $unit = $unit[0];
	  else
		  CURLHandler::ErrorPage();
                $unit['reg_date'] = onSiteTime($unit['registration_date']);
                $unit['last_visit_date'] = lastVisitSite($unit['last_visit_date'],$unit['registration_date']);
                $unit['description'] = (!empty($unit['description'])?'<h4 class="detailsBlockTitle"><a name="description">Описание</a></h4>'.$unit['description']:'');
		$pro_type = getProType('contractor',$this->id);
		$unit['pro_logo_prew'] = '';
		if($pro_type == 1 || $pro_type == 2){
			$unit['pro_logo_prew'] = getProLogoForPreview('contractor');
		}
		
		$unit["u_link"] = "";
		$u_links = SQLProvider::ExecuteQuery("select ru.tbl_obj_id, IF(ru.nikname is NULL or ru.nikname = '',ru.title,ru.nikname) title from tbl__registered_user_link_resident rl left join tbl__registered_user ru on ru.tbl_obj_id = rl.user_id
		                                      where rl.resident_type = 'contractor' and rl.resident_id = ".$this->id);
		if (sizeof($u_links)>0 && !empty($pro_type))
		{
			$unit["u_link"] = "<div style=\"padding-bottom: 20px;\"><b>Представители подрядчика:</b><br />";
			foreach ($u_links as $num=>$u_link)
			{
				$unit["u_link"] .= "<a href=\"/u_profile/?type=user&id=".$u_link["tbl_obj_id"]."\">".$u_link["title"]."</a><br />";
			}
			$unit["u_link"] .= "</div>";
		}
		
    /* Baltic IT */
    $activityTitles = explode("|", $unit["activity_title"]);
    $activityTitleArray = array();
    for ($i = 0; $i < sizeof($activityTitles); $i++) {
        array_push($activityTitleArray,$activityTitles[$i]); // balticit, массив подкатегорий
    }
    $activity_title_list = implode(", ",$activityTitleArray);
    /* End Baltic It*/
		
		$title = $this->GetControl("title");
    $title->text = $unit["title"].' - '.$activity_title_list;
		
		$unit["logo_visible"] = IsNullOrEmpty( $unit["logo_image"])?"hidden":"visible";
		if ($unit['city']==-1) $unit['city_name'] = $unit['other_city'];


		$user = new CSessionUser("user");
		CAuthorizer::AuthentificateUserFromCookie(&$user);
		CAuthorizer::RestoreUserFromSession(&$user);
        $show_ilike = true;
        $btn_action = "ShowLikeMessage(); return false;";
        $unit["ShowMsgMessage"] = 'onclick="javascript: ShowMsgMessage(); return false;"';
		$fav_add = true;
		if ($user->authorized)
		{
			$btn_action = "$(this).parent().submit();";
            $unit["ShowMsgMessage"] = "";
			$fav = SQLProvider::ExecuteQuery("select 1 from tbl__user_favorites
                                            where user_type = '".$user->type."'
                                            and user_id = ".$user->id."
                                            and resident_type = 'contractor'
                                            and resident_id=$this->id");
            if (sizeof($fav))
              $fav_add = false;

            if (GP("add")=="favorite")
			{
				SQLProvider::ExecuteNonReturnQuery("insert into tbl__user_favorites (user_type,user_id,resident_type,resident_id)
						values ('".$user->type."',".$user->id.",'contractor',$this->id)");
				CURLHandler::Redirect("/contractor/$id_str");
			}
			if (GP("delete")=="favorite") {
					SQLProvider::ExecuteNonReturnQuery("delete from tbl__user_favorites
					where tbl__user_favorites.user_type='".$user->type."' and tbl__user_favorites.user_id=".$user->id." and
					tbl__user_favorites.resident_type='contractor' and tbl__user_favorites.resident_id=".$this->id);
					CURLHandler::Redirect("/contractor/$id_str");
			}

			$currentmark = SQLProvider::ExecuteScalar("select count(1) from tbl__userlike where
				to_resident_type='contractor' and to_resident_id='$this->id' and from_resident_type='".$user->type."' and from_resident_id='".$user->id."'");
			$show_ilike = $currentmark==0;
			if ($show_ilike and GP('action')=='ilike')
			{
                SQLProvider::ExecuteQuery("
                        insert into tbl__userlike
                        (to_resident_id,to_resident_type,from_resident_id,from_resident_type, date)
                        values
                        ($this->id,'contractor',".$user->id.",'".$user->type."',FROM_UNIXTIME(".time()."))
                        ");

                //отправка сообщения резиденту об оценке
                $residend_info = SQLProvider::ExecuteQuery(
                    "SELECT u.tbl_obj_id, u.login_type, u.email
                    FROM `vw__all_users` u\n
                    where u.login_type = 'contractor' and u.tbl_obj_id = $this->id");
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
				CURLHandler::Redirect("/contractor/".$id_str);
			}
            if (!$show_ilike and GP('action')=='unlike')
			{

                SQLProvider::ExecuteQuery("
                        delete from tbl__userlike
                        where to_resident_id = $this->id
                          and to_resident_type = 'contractor'
                          and from_resident_id = ".$user->id."
                          and from_resident_type = '".$user->type."'");

				CURLHandler::Redirect("/contractor/".$id_str);
            }
		}
        if ($show_ilike) {
			$unit["btn_i_like"]='<form method="post"><input type="hidden" name="action" value="ilike">
			<img class="btn_ilike" src="/images/rating/btn_ilike.png" onmouseover="javascript: this.src=\'/images/rating/btn_contractor.png\';" onmouseout="javascript: this.src=\'/images/rating/btn_ilike.png\';" onclick="javascript: '.$btn_action.'"></form>';
		}
        else {
            $unit["btn_i_like"]='</td><td><form method="post"><input type="hidden" name="action" value="unlike"><a href="" class="black" onclick="javascript: $(this).parent().submit(); return false;"><img onmouseover="javascript: this.src=\'/images/rating/unlike_contractor.png\';" onmouseout="javascript: this.src=\'/images/rating/btn_unlike.png\';" src="/images/rating/btn_unlike.png" alt="Больше не рекомендую" /></a></form>';
        }


		$mark = SQLProvider::ExecuteQuery("select au.user_id,au.type,au.title from tbl__userlike ul
                                           join vw__all_users_full au
                                               on au.type = ul.from_resident_type and
                                                  au.user_id = ul.from_resident_id
                                           where to_resident_type='contractor' and to_resident_id='$this->id'");
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
			$unit["fav_link"] = '<noindex><a class="contractor in_favorite" rel="nofollow" href="/contractor/'.$id_str.'?add=favorite" '.$msg.'><span>Добавить в избранное</span></a></noindex>';
		}
		else {
			$msg = "";
			if (!$user->authorized) $msg = 'onclick="javascript: ShowFavMessage(); return false;"';
			$unit["fav_link"] = '<noindex><a class="contractor out_favorite" rel="nofollow" href="/contractor/'.$id_str.'?delete=favorite" '.$msg.'><span>Убрать из избранного</span></a></noindex>';
		}
        //process activities
        $activityIds = explode("|", $unit["activity_ids"]);
        $activityTitles = explode("|", $unit["activity_title"]);
        $activityUrls = explode("|",$unit["activity_urls"]);
        $activityLinks = array();
        $params = array();
        for ($i = 0; $i < sizeof($activityTitles); $i++) {
			$kind_of_activity = $activityIds[$i];
            $params['activity'] = $activityIds[$i];
            $link = "/contractor/" .$activityUrls[$i];
            array_push($activityLinks, "<a href=\"$link\" class=\"common\">" . $activityTitles[$i] . "</a>");
        }
        $unit["activity_title"] = CStringFormatter::FromArray($activityLinks, ", ");
        //news
        $news = SQLProvider::ExecuteQuery("select rn.*,DATE_FORMAT(date,'%d.%m.%y') as `strdate` from tbl__resident_news rn where active=1
                                       and resident_id=$this->id and resident_type='contractor' order by rn.date desc");
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
        //$similar = SQLProvider::ExecuteQuery('select * from `tbl__contractor_doc` where active=1 order by rand() LIMIT 4');
        $is_kind_of_activity = !empty($kind_of_activity) ? $kind_of_activity : 0;
		$isRecomended = SQLProvider::ExecuteScalar("SELECT recommended from tbl__contractor_doc where tbl_obj_id=".$unit['tbl_obj_id']);		
		$similar = SQLProvider::ExecuteQuery('SELECT * FROM `tbl__contractor_doc` 
											WHERE (tbl_obj_id IN (
												SELECT distinct tbl_obj_id 
												FROM tbl__contractor2activity 
												WHERE kind_of_activity='.$is_kind_of_activity.')) 
											AND tbl_obj_id != '.$unit['tbl_obj_id'].' 
											AND active=1 
											ORDER BY rand() 
											LIMIT 4');
        if(!empty($similar)&&$isRecomended==0){
            $unit["similar"] = '<h4 class="detailsBlockTitle"><a name="similar">Похожие подрядчики</a></h4>
                <table width="100%" class="similarBlock">';
            foreach($similar as $i=>$s){
                $unit["similar"].= ($i%2 == 0?'<tr>':'').
                                    '<td><div class="logo_wrap"><img src="/upload/'.(empty($s['logo_image'])?'':$s['logo_image']).'" class="logo120border" alt="" title=""></div><strong><a href="/contractor/'.$s['title_url'].'">'.$s['title'].'</a></strong><br/>'.substr(strip_tags($s['description']),0,125).'</td>'.
                    ($i%2!= 0?'</tr>':'');
            }
            $unit["similar"].='</table>';
        }
	$unit["similar"] = !empty($unit["similar"]) ? $unit["similar"] : '';
	//var_dump($this->id);
    //contractor images
    $photos = $this->GetControl("photos");
    $photos->dataSource = SQLProvider::ExecuteQuery(
      "select p.*
		  from `tbl__contractor_photos`  ap
			join `tbl__photo` p on ap.child_id = p.tbl_obj_id
			where parent_id=$this->id limit 8");
        $unit["photos"] = $photos->Render();
        
        
        //video load
        $unit["youtubevideo"] = get_video_id($unit['youtube_video']);
            $unit["video_visible"] = "";
            if($unit["youtubevideo"] == false ) {
              $unit["video_visible"] = 'style="display: none;"';
            }
            
            $unit["youtubevideo_2"] = get_video_id($unit['youtube_video_2']);
            $unit["video_visible_2"] = "";
            if($unit["youtubevideo_2"] == false ) {
              $unit["video_visible_2"] = 'style="display: none;"';
            }
            
            $unit["youtubevideo_3"] = get_video_id($unit['youtube_video_3']);
            $unit["video_visible_3"] = "";
            if($unit["youtubevideo_3"] == false ) {
              $unit["video_visible_3"] = 'style="display: none;"';
            }
        /*
            $matches = array();
            $matches_2 = array();
            $matches_3 = array();
            if (strlen($unit['youtube_video']) > 0 && (preg_match('/^http:\/\/[w\.]*youtube\.com\/watch\?v=([A-z0-9-_]+).*$/i', $unit['youtube_video'], $matches) > 0)) {
                $unit["video_visible"] = "";
                $unit["youtubevideo"] = $matches[1];
            }
            else {
                $_URL = explode("/", $unit['youtube_video']);
                if(in_array("embed",$_URL)) {
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
                if(in_array("embed",$_URL)) {
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
                if(in_array("embed",$_URL)) {
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
            */
        
        //Remove direct
        if ($unit['direct'] == 1) {
            $this->GetControl('yaPersonal')->template = "";
            $this->GetControl('topLine')->template = "";
            
            $unit["description"] = nl2br($unit["description"]);
            $unit["similar"] ="";
            
            //$header = $this->GetControl('header');
            //$header->itemTemplates['login']->login = "sdsd";
            //$header->itemTemplates['logout'] = "sdsd";
        }		
        else {
          $unit["description"] = nl2br(strip_tags($unit["description"]));
        }
        
        
        
        $contDetails = $this->GetControl("contDetails");
        $contDetails->dataSource = $unit;
        /*setting activity types*/
        $activities = SQLProvider::ExecuteQuery("select *, tbl_obj_id as child_id from `tbl__activity_type` order by priority desc ");
        foreach ($activities as $key => $value) {
            $activities[$key]["link"] = "/contractor/" . $value['title_url'];
            $activities[$key]["orange"] = "orange";
        }
        $actList = $this->GetControl("actList");
        $actList->dataSource = $activities;

        if ($this->id == 5041 || $this->id == 7474 
			|| $this->id == 4666 
            || $this->id == 329
            || $this->id == 7608
            || $this->id == 7625
            || $this->id == 5592
            || $this->id == 7623
            || $this->id == 7562
            || $this->id == 7689
            || $this->id == 7647
            || $unit['direct'] == 1
        ) {
            //turnoff yandex.direct
            $this->GetControl("yaPersonal")->template = "";

            $file = RealFile("/pagecode/settings/banner_files/bottomBannersWoYa.htm");
            if (is_file($file)) $this->GetControl("bottomBanners")->template = file_get_contents($file);
        }
        
        if( is_numeric($unit['priority'] )) {
    		 if($unit['priority'] != 0) { $this->GetControl('yaPersonal')->template = ""; }
        }
        else {
    			if (!IsNullOrEmpty($unit['priority'])) {
            $this->GetControl('yaPersonal')->template = "";
          }
        }
        
        
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
}
?>
