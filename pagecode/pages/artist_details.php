<?php
class artist_details_php extends CPageCodeHandler
{
    private $id = 0;
    public $is_group = false;
    private $group_id = null;
    private $subgroup_id = null;
    public $pagesize = 25;
    public $descriptionSize = 256;
    public $is_main = false;
    public $search_styles = "";

    public function artist_details_php()
    {
        $this->CPageCodeHandler();
    }

    public function BuildFilter($filter, $name, $value, $cond = "=", $type = "numeric")
    {
        switch ($type) {
            case "numeric":
                {
                if (is_numeric($value)) {
                    if (strlen($filter) > 0) {
                        $filter .= " and ";
                    }
                    $filter .= " $name $cond $value ";
                }
                }
                break;
            case "string":
                if (is_string($value)) {
                    if (strlen($filter) > 0) {
                        $filter .= " and ";
                    }
                    $filter .= " $name $cond '$value' ";
                }
                break;
        }
        return $filter;
    }

    public function PrepareArtistsMain($artists)
    {
        foreach ($artists as &$artist) {
            $artist["annotation"] = strip_tags($artist["description"]);
        }
        return $artists;
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
        $searchDS = array();
        $id_str = GP("id");
        //Костыль!!!!!
        if (!is_null($id_str)) {
            //can be subgroup or group
            $this->group_id = SQLProvider::ExecuteScalar("select tbl_obj_id from tbl__artist_group where title_url = '" . mysql_real_escape_string($id_str) . "'");
            if ($this->group_id == null) {
                //subgroup
                $this->subgroup_id = SQLProvider::ExecuteScalar("select tbl_obj_id from tbl__artist_subgroup where title_url = '" . mysql_real_escape_string($id_str) . "'");
            }
            if (is_null($this->group_id) && is_null($this->subgroup_id)) {
                $this->id = SQLProvider::ExecuteScalar("select tbl_obj_id from tbl__artist_doc where title_url = '" . mysql_real_escape_string($id_str) . "'");
            }
            else {
                $this->is_group = true;
                $sbid = $this->subgroup_id;
                if ($sbid != null) {
                    $this->group_id = SQLProvider::ExecuteScalar("select parent_id from tbl__artist_subgroup where tbl_obj_id=$sbid");
                }

            }
        }
        if ($this->is_group) {
            $metadata = $this->GetControl("metadata");
            $metadata->keywords = "артисты, эвент-каталог, ведущий на свадьбу";
            $metadata->description = "Эвент-каталог содержит контактные данные артистов и музыкальных групп, выступающих на банкетах, вечеринках и корпоративных мероприятиях: ведущие на свадьбу, юбилей и семейное торжество.";
            $searchDS = array();
            $av_rwParams = array("page", "letter","artist_doc_name",
                "artist_doc_country", "artist_doc_style");
            CURLHandler::CheckRewriteParams($av_rwParams);
            $rewriteParams = $_GET;
            $first = "NULL";
            $page = GP("page", 1);
            $group = $this->group_id;
            $subgroup = $this->subgroup_id;
            $artist_doc_name = GP("artist_doc_name");
            $artist_doc_country = GP("artist_doc_country");
            $artist_doc_style = GP("artist_doc_style");
            $artist_letter = GP("letter");
            if (!IsNullOrEmpty($artist_letter)) {
                $artist_letter = urldecode($artist_letter);
            }
            $filter = "";
            $filter = $this->BuildFilter($filter, "country", $artist_doc_country);
            if (!IsNullOrEmpty($artist_doc_name)) {
                if (strlen($filter) > 0)
                    $filter .= " and ";
                $filter .= " title like '%$artist_doc_name%' ";
            }

            $artist_doc_style_title = "";
            if (is_array($artist_doc_style) && sizeof($artist_doc_style))
            {
                $str_artist_doc_style = implode(',',$artist_doc_style);
                $artist_doc_style_title = SQLProvider::ExecuteScalar("
						select GROUP_CONCAT(title SEPARATOR ', ') from tbl__styles where tbl_obj_id in ($str_artist_doc_style)");
                if (strlen($filter)>0)
                {
                    $filter.=" and ";
                }
                $filter.=" tbl_obj_id in (select artist_id from tbl__artist2style where style_id in ($str_artist_doc_style)) ";
            }

            if (is_numeric($artist_doc_style)) {
                if (strlen($filter) > 0)
                    $filter .= " and ";
                $filter .= " tbl_obj_id in (select artist_id from tbl__artist2style where style_id=$artist_doc_style) ";
            }

            if (is_numeric($group)) {
                $first = SQLProvider::ExecuteQuery(
                    "select r.tbl_obj_id
        from tbl__artist_group t
        join tbl__artist_doc r on r.tbl_obj_id = t.first_id
        where t.tbl_obj_id=$group
          and r.active=1");
                if (sizeof($first) > 0)
                    $first = $first[0]["tbl_obj_id"];
                else
                    $first = "NULL";
                if (strlen($filter) > 0)
                    $filter .= " and ";
                $filter .= " tbl_obj_id in (select a2s.artist_id
			                          from tbl__artist2subgroup a2s join tbl__artist_subgroup sg on sg.tbl_obj_id = a2s.subgroup_id
									  where sg.parent_id=$group)";
            }
            if (is_numeric($subgroup)) {
                $first = SQLProvider::ExecuteQuery(
                    "select r.tbl_obj_id
        from tbl__artist_subgroup t
        join tbl__artist_doc r on r.tbl_obj_id = t.first_id
        where t.tbl_obj_id=$subgroup
          and r.active=1");
                if (sizeof($first) > 0)
                    $first = $first[0]["tbl_obj_id"];
                else
                    $first = "NULL";
                if (strlen($filter) > 0)
                    $filter .= " and ";
                $filter .= " tbl_obj_id in (select artist_id from tbl__artist2subgroup where subgroup_id=$subgroup) ";
            }
            if (!IsNullOrEmpty($artist_letter)) {
                if (strlen($filter) > 0) {
                    $filter .= " and ";
                }
                $filter .= " title like '$artist_letter%' ";
            }
            $this->is_main = strlen($filter) == 0;
            if (!$this->is_main) {
                if (strlen($filter) > 0)
					$filter = " where $filter";
                $count = SQLProvider::ExecuteQuery("select count(*) as counter from  vw__artist_list_pro $filter");
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
                //main list

				$artists = SQLProvider::ExecuteQuery(
					"select * from  `vw__artist_list_pro` $filter
					order by priority desc, title ASC, if(tbl_obj_id=$first,0,1), pro_type desc, pro_cost desc, pro_date_pay desc, title limit $sp,$this->pagesize");
				$artistList = $this->GetControl("artistList");
				$letter = "";
                foreach ($artists as &$artist) {
                    if ($artist["title"][0] != $letter) {
                        $artist["space_height"] = 15;
                        $letter = $artist["title"][0];
                    }
                    else
                        $artist["space_height"] = 5;

                    switch ($artist["selection"]) {
                        case 1:
                            $artist["selection_type"] = "color:#EE0000; font-weight:bold;";
                            break;
                        case 2:
                            $artist["selection_type"] = "color:#000; font-weight:bold;";
                            break;
                        case 3:
                            $artist["selection_type"] = "color:#EE0000; font-weight:bold;";
                            break;
                        default:
                            $artist["selection_type"] = "color:#000; font-weight:bold;";
                            break;
                    }
					
				    if (!empty($artist['city'])) $sql = SQLProvider::ExecuteQuery('SELECT title FROM `tbl__city` WHERE tbl_obj_id = '.(int)$artist['city']);				
				    $artist["city_item"] = (!empty($sql)) ? (!empty($sql[0]['title'])) ? '<span style="color: #000;">('.$sql[0]['title'].')</span>' : '' : '';				

                    $gr = SQLProvider::ExecuteQuery("select * from tbl__artist_subgroup, tbl__artist2subgroup where artist_id=" . $artist["tbl_obj_id"] . " and subgroup_id=tbl_obj_id");
                    $artist['category'] = "";
                    foreach ($gr as $gkey => $value) {
                        if ($artist['category'] != "")
                            $artist['category'] .= " / ";
                        $artist['category'] .= '<a class="common" href="/artist/'.$value['title_url']. '">' . $value['title'] . '</a>';
                    }

                    $artist["info"] = CutString(strip_tags($artist["description"]), $this->descriptionSize);

                    $artist['links'] = "";
                    $artist['resident_type'] = 'artist';
                    $artist["class"] = 'artist_table_hover';
                    //PRO
					$artist['background']		= '0';
					$artist['pro_logo']			= '';
					$artist['pro_logo_prew']	= '';
					if($artist['pro_type'] == 1 || $artist['pro_type'] == 2) {
					$artist['border']			= 'border:2px solid '.getProBackgroud('artist').';';
					$artist['pro_logo_prew']	= getProLogoForPreview('artist');
					$artist['pro_logo']			= getProLogo();
					}
					}
					$artistList->dataSource = $artists;


                //SEO text
                if (isset($subgroup)) {
                    $ft = SQLProvider::ExecuteQuery("select seo_text from tbl__artist_subgroup where tbl_obj_id=" . $subgroup);
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
                //groups
                $groups = SQLProvider::ExecuteQuery("select
					  sg.`tbl_obj_id` AS `child_id`,
					  sg.`parent_id`,
					  sg.`title`,
					  sg.`title_url`,
					  sg.`order_id`,
					  sg.`priority`
					from
					  `tbl__artist_subgroup` sg
					union
					select
					  g.`tbl_obj_id` AS `child_id`,
					  0 AS `parent_id`,
					  g.`title` AS `title`,
					  g.`title_url`,
					  g.`order_id`,
					  g.`priority`
					from
					  `tbl__artist_group` g
					ORDER by priority desc ");
                foreach ($groups as $key => $value) {
                    $cpars = $rewriteParams;
                    unset($cpars["subgroup"]);
                    if ($value["parent_id"] == 0) {
                        $cpars["group"] = $value["child_id"];
                    }
                    else
                    {
                        $cpars["group"] = $value["parent_id"];
                        $cpars["subgroup"] = $value["child_id"];
                    }
                    $groups[$key]["selected"] = $cpars["group"] == $group && (!isset($cpars["subgroup"]) || $cpars["subgroup"] == $subgroup) ? 'id="selectRed"' : "";
                    $groups[$key]["red"] = $cpars["group"] == $group && (!isset($cpars["subgroup"]) || $cpars["subgroup"] == $subgroup) ? '' : "red";
                    unset($cpars["group"]);
                    unset($cpars["subgroup"]);
                    $groups[$key]["link"] = "/artist/".$value['title_url'].CURLHandler::BuildQueryParams($cpars);
                }
                $groupList = $this->GetControl("groupList");
                $groupList->dataSource = $groups;

                $titlefilter = array();
                $titlefilterLinks = array();
                if (isset($group)) {
                    $type_finded = false;
                    $subtype_finded = false;
                    foreach ($groups as $gr) {
                        if ($gr["parent_id"] == 0 && $gr["child_id"] == $group) {
                            $urlparams = array();
                            //$urlparams["group"] = $gr["title_url"];
                            $link = "/artist/".$gr["title_url"]. CURLHandler::BuildQueryParams($urlparams);
                            array_unshift($titlefilter, CStringFormatter::buildCategoryLinks($gr['title'], null));
                            array_unshift($titlefilterLinks, CStringFormatter::buildCategoryLinks($gr['title'], $link, "artist"));
                            $type_finded = true;
                        }
                        else if ($gr["child_id"] == $subgroup) {
                            $urlparams = array();
                            //$urlparams["group"] = $gr["parent_id"];
                            //$urlparams["subgroup"] = $gr["child_id"];
                            $link = "/artist/".$gr['title_url'].CURLHandler::BuildQueryParams($urlparams);
                            array_push($titlefilter, CStringFormatter::buildCategoryLinks($gr['title'], null));
                            array_push($titlefilterLinks, CStringFormatter::buildCategoryLinks($gr['title'], $link, "artist"));
                            $subtype_finded = true;
                        }
                    }
                    if (!$type_finded || (isset($subgroup) && !$subtype_finded))
                        CURLHandler::ErrorPage();
                }

                $titlefil = $this->GetControl("titlefilter");
                if (sizeof($titlefilter))
                    $titlefil->text = implode(" / ", $titlefilter) . " - ";
                if (sizeof($titlefilterLinks))
                    $this->GetControl("titlefilterLinks")->html = '<div class="titlefilter artist">' . implode(" / ", $titlefilterLinks) . '</div>';
                else
                    $this->GetControl("titlefilterLinks")->html = '';

                //filter
                $searchDS["artist_doc_name"] = $artist_doc_name;
                $countries = SQLProvider::ExecuteQuery("select * from  `tbl__countries` ");
                array_unshift($countries, array("title" => "все", "tbl_obj_id" => ""));
                $selcountry = new CSelect();
                $selcountry->dataSource = $countries;
                $selcountry->valueName = "tbl_obj_id";
                $selcountry->titleName = "title";
                $selcountry->name = "artist_doc_country";
                $selcountry->selectedValue = (is_null($artist_doc_country)) ? "" : $artist_doc_country;
                $selcountry->class = "width_90";
                $selcountry->id = "mySelect1";
                $selcountry->size = 1;
                $searchDS["artist_doc_country"] = $selcountry->Render();
                $styles = SQLProvider::ExecuteQuery("select * from  `tbl__styles` ");
                array_unshift($styles, array("title" => "все", "tbl_obj_id" => ""));
                $selstyle = new CSelect();
                $selstyle->dataSource = $styles;
                $selstyle->valueName = "tbl_obj_id";
                $selstyle->titleName = "title";
                $selstyle->name = "artist_doc_style";
                $selstyle->selectedValue = (is_null($artist_doc_style)) ? "" : $artist_doc_style;
                $selstyle->class = "width_90";
                $selstyle->id = "mySelect2";
                $selstyle->size = 1;
                $searchDS["artist_doc_style"] = $selstyle->Render();
                $link = CURLHandler::$currentPath . CURLHandler::BuildQueryParams($rewriteParams);
                $searchDS["postBackUrl"] = $link;
                $search = $this->GetControl("search");
                $search->dataSource = $searchDS;

                //setting letter
                $letters = array();
                $len = strlen(FILTER_LETTERS);
                $fl = FILTER_LETTERS;
                for ($i = 0; $i < $len; $i++)
                {
                    $pars = $rewriteParams;
                    $pars["letter"] = urlencode($fl[$i]);
                    $link = CURLHandler::$currentPath . CURLHandler::BuildQueryParams($pars);
                    $let = array("letter" => $fl[$i], "link" => $link, "selected" => ($fl[$i] == $artist_letter));
                    array_push($letters, $let);
                }
                $letterFilter = $this->GetControl("letterFilter");
                $letterFilter->dataSource = $letters;

                
                $mainMenu = $this->GetControl("menu");
  
                    $mainMenu->dataSource["shelk"] =
          					array("link" => "http://forevent.pro/",
          					"imgname" => "forevent",
          					"title"=>"",
          					"target" => "target='_blank'");
        
                

                $submenu = $this->GetControl("submenu");
                $submenu->headerTemplate =
                    '<div class="artist_btn_show submenu_controll" style="background: #{bgcolor} url(/images/menu1/bg_artist.png) repeat-x; height:30px; padding: 0 15px 0 37px; position: relative;">
					<form method="get" id="form_find_artist" action="/artist/">
					<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr valign="middle"><td>';
                $submenu->footerTemplate =
                    '</td><td><img src="/images/front/0.gif" width="1" height="30"></td><td align="right" style="padding-right: 60px;">
					<span class="submenu">Поиск по названию <input name="artist_doc_name" type="text" size="20" value="'.$artist_doc_name.'"></span>
					<span class="submenu">Поиск по стране '.$selcountry->Render().'</span>
					<span id="find_style" class="submenu" style="cursor: pointer;" onclick="FindStylesDlg()">Поиск по стилю</span>
					</tr></table></form></div>';

                if (!IsNullOrEmpty($artist_doc_style_title)) {
                    $this->search_styles = "<div class=\"artist\">Стили: $artist_doc_style_title</div>";
                }
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
        else {
            $av_rwParams = array("id", "add", "delete","page");
            CURLHandler::CheckRewriteParams($av_rwParams);
            if (is_numeric($this->id))
                SQLProvider::ExecuteNonReturnQuery("update tbl__artist_doc set visited = ifnull(visited,0)+1 where tbl_obj_id=$this->id");
            else
                CURLHandler::ErrorPage();

            $unit = SQLProvider::ExecuteQuery("
      select distinct
        `ar`.`tbl_obj_id` AS `tbl_obj_id`,
        `ar`.`title` AS `title`,
        `ar`.`group` AS `group`,
        `ar`.`subgroup` AS `subgroup`,
        `ar`.`region` AS `region`,
        `ar`.`country` AS `country`,
        `ar`.`style` AS `style`,
        `ar`.`description` AS `description`,
        `ar`.`site_address` AS `site_address`,
        `ar`.`manager_name` AS `manager_name`,
        `ar`.`other_country`,
        `ar`.`logo` AS `logo`,
        `ar`.`manager_phone` AS `manager_phone`,
        `ar`.`email` AS `email`,
        `ar`.`video` AS `video`,
        `ar`.`youtube_video` AS `youtube_video`,
        `ar`.`farvideo` as `farvideo`,
        `ar`.`price_to`as `price_to`,
        `ar`.`price_from`as `price_from`,
        `ar`.`rider`as `rider`,
        `ag`.`title` AS `group_title`,
        `ag`.`tbl_obj_id` AS `artist_group_id`,
        `rg`.`title` AS `region_title`,
        `cn`.`title` AS `country_title`,
        `ar`.`city`,
        `ar`.`last_visit_date` AS `last_visit_date`,
        `ar`.`other_city`,
        `ar`.`priority` AS `priority`,
        `ar`.`currency` AS `currency`,
	      `ar`.`direct` AS `direct`,
        `ar`.`registration_date` AS `reg_date`,
        if (`ar`.`city` > 0, `ct`.`title`, `ar`.`other_city`) AS `city_title`,
        group_concat(`st`.`title` separator ', ') AS `styles`,
        concat(_cp1251',',group_concat(cast(`st`.`tbl_obj_id` as char(5) charset cp1251) separator ','),_cp1251',') AS `style_ids`
      from `tbl__artist_doc` `ar`
      left join `tbl__artist_group` `ag` on `ar`.`group` = `ag`.`tbl_obj_id`
      join `tbl__regions` `rg` on `rg`.`tbl_obj_id` = `ar`.`region`
      left join `tbl__countries` `cn` on `cn`.`tbl_obj_id` = `ar`.`country`
      left join `tbl__artist2style` `a2s` on `a2s`.`artist_id` = `ar`.`tbl_obj_id`
      left join `tbl__styles` `st` on `a2s`.`style_id` = `st`.`tbl_obj_id`
      left join `tbl__city` `ct` on `ct`.`tbl_obj_id` = `ar`.`city`
      where `ar`.`active` = 1 and `ar`.`tbl_obj_id` = $this->id
      group by `ar`.`tbl_obj_id`,`ar`.`title`,`ar`.`other_country`,`ar`.`group`,`ar`.`subgroup`,`ar`.`region`,`ar`.`country`,`ar`.`style`,`ar`.`description`,`ar`.`cost`,`ar`.`site_address`,`ar`.`demo`,`ar`.`manager_name`,`ar`.`logo`,`ar`.`manager_phone`,`ar`.`email`,`ag`.`title`,`rg`.`title`,`cn`.`title`,
    `ar`.`other_city`,`ct`.`title`");

        if (sizeof($unit) == 1)
            $unit = $unit[0];
        else
            CURLHandler::ErrorPage();
        
        $pro_type = getProType('artist',$this->id);
		$unit['pro_logo_prew'] = '';
		if($pro_type == 1 || $pro_type == 2) {
			$unit['pro_logo_prew'] = getProLogoForPreview('artist');
		}
        // время на сайте
        $unit['last_visit_date'] = lastVisitSite($unit['last_visit_date'], $unit['reg_date']);
        $unit['reg_date'] = onSiteTime($unit['reg_date']);
        
        $unit['description'] = (!empty($unit['description'])?'<h4 class="detailsBlockTitle"><a name="description">Описание</a></h4>'.$unit['description']:'');
        $unit["u_link"] = "";
        $u_links = SQLProvider::ExecuteQuery("select ru.tbl_obj_id, IF(ru.nikname is NULL or ru.nikname = '',ru.title,ru.nikname) title from tbl__registered_user_link_resident rl left join tbl__registered_user ru on ru.tbl_obj_id = rl.user_id
                      where rl.resident_type = 'artist' and rl.resident_id = $this->id");
            if (sizeof($u_links) > 0 && !empty($pro_type)) {
                $unit["u_link"] = "<div style=\"padding-bottom: 20px;\"><b>Представители артиста:</b><br />";
                foreach ($u_links as $num => $u_link)
                {
                    $unit["u_link"] .= "<a href=\"/u_profile?type=user&id=" . $u_link["tbl_obj_id"] . "\">" . $u_link["title"] . "</a><br />";
                }
                $unit["u_link"] .= "</div>";
            }

            $grps = array();
            $subgrs = SQLProvider::ExecuteQuery("select * from tbl__artist_subgroup, tbl__artist2subgroup where artist_id=$this->id and subgroup_id=tbl_obj_id");
            foreach ($subgrs as $gr) {
                $link = "/artist/".$gr['title_url'];
                array_push($grps, CStringFormatter::buildCategoryLinks($gr['title'], $link, "common"));
            }
            $unit['group_title'] = CStringFormatter::FromArray($grps, ", ");

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
                                            where user_type = '" . $user->type . "'
                                            and user_id = " . $user->id . "
                                            and resident_type = 'artist'
                                            and resident_id=$this->id");
            if (sizeof($fav))
                $fav_add = false;

            if (GP("add") == "favorite") {
                SQLProvider::ExecuteNonReturnQuery("insert into tbl__user_favorites (user_type,user_id,resident_type,resident_id)
                values ('" . $user->type . "'," . $user->id . ",'artist',$this->id)");
                CURLHandler::Redirect("/artist/$id_str");
            }
			if (GP("delete")=="favorite") {
					SQLProvider::ExecuteNonReturnQuery("delete from tbl__user_favorites
					where tbl__user_favorites.user_type='".$user->type."' and tbl__user_favorites.user_id=".$user->id." and
					tbl__user_favorites.resident_type='artist' and tbl__user_favorites.resident_id=".$this->id);
					CURLHandler::Redirect("/artist/$id_str");
			}
            $currentmark = SQLProvider::ExecuteScalar("select count(1) from tbl__userlike where
        to_resident_type='artist' and to_resident_id='$this->id' and from_resident_type='" . $user->type . "' and from_resident_id='" . $user->id . "'");
            $show_ilike = $currentmark == 0;
            if ($show_ilike and GP('action') == 'ilike') {
                SQLProvider::ExecuteQuery("
                    insert into tbl__userlike
                    (to_resident_id,to_resident_type,from_resident_id,from_resident_type, date)
                    values
                    ($this->id,'artist'," . $user->id . ",'" . $user->type . "',FROM_UNIXTIME(" . time() . "))
                    ");
                    //отправка сообщения резиденту об оценке
                    $residend_info = SQLProvider::ExecuteQuery(
                        "SELECT u.tbl_obj_id, u.login_type, u.email
                    FROM `vw__all_users` u\n
                    where u.login_type = 'artist' and u.tbl_obj_id = $this->id");
                    if (sizeof($residend_info)) {
                        $app = CApplicationContext::GetInstance();
                        $link = $_SERVER['HTTP_HOST'] . '/' . $residend_info[0]['login_type'] . '/' . $id_str;
                        $mtitle = iconv($app->appEncoding, "utf-8", "Вы понравились пользователю на портале eventcatalog.ru");
                        $mbody = iconv($app->appEncoding, "utf-8", '<div id="content"><p>Уважаемый резидент!</p>' .
                            '<p>Вы понтравились пользователю.</p>' .
                            '<p>Вы можете посмотреть кому, перейдя по ссылке: <a target="_blank" href="http://' . $link . '">http://' . $link . '</a></p>' .
                            '<p>С уважением,<br />' .
                            'EVENTКАТАЛОГ<br />' .
                            'Ежедневный помощник организаторов мероприятий.</p></div>');
                        SendHTMLMail($residend_info[0]['email'], $mbody, $mtitle);
                    }
                    CURLHandler::Redirect("/artist/$id_str");
                }
                if (!$show_ilike and GP('action') == 'unlike') {

                    SQLProvider::ExecuteQuery("
                    delete from tbl__userlike
                    where to_resident_id = $this->id
                      and to_resident_type = 'artist'
                      and from_resident_id = " . $user->id . "
                      and from_resident_type = '" . $user->type . "'");

                    CURLHandler::Redirect("/artist/$id_str");
                }
            }
        if ($show_ilike) {
            $unit["btn_i_like"] = '<form method="post"><input type="hidden" name="action" value="ilike">
        <img class="btn_ilike" src="/images/rating/btn_ilike.png" onmouseover="javascript: this.src=\'/images/rating/btn_artist.png\';" onmouseout="javascript: this.src=\'/images/rating/btn_ilike.png\';" onclick="javascript: ' . $btn_action . '"></form>';
        }
        else {
            $unit["btn_i_like"] = '</td><td><form method="post"><input type="hidden" name="action" value="unlike"><a href="" class="black" onclick="javascript: $(this).parent().submit(); return false;"><img onmouseover="javascript: this.src=\'/images/rating/unlike_artist.png\';" onmouseout="javascript: this.src=\'/images/rating/btn_unlike.png\';" src="/images/rating/btn_unlike.png" alt="Больше не рекомендую" /></a></form>';
        }

        $mark = SQLProvider::ExecuteQuery("select au.user_id,au.type,au.title from tbl__userlike ul
                                            join vw__all_users_full au
                                               on au.type = ul.from_resident_type and
                                                  au.user_id = ul.from_resident_id
                                           where to_resident_type='artist' and to_resident_id='$this->id'");
        $mark_cnt = 0;
        $mark_links = "";
        foreach ($mark as $m_item) {
            $mark_cnt++;
            if ($mark_links)
                $mark_links .= ", ";
            $mark_links .= '<a rel="nofollow" class="user_like_link" href="/u_profile/?type=' . $m_item['type'] . '&id=' . $m_item['user_id'] . '">' . $m_item['title'] . '</a>';
        }
        $unit["voted"] = "";
        if ($mark_cnt > 0) {
            $u_text = " ";
            if ($mark_cnt == 1)
                $u_text = " ";

            $unit["voted"] = "<div class=\"user_liked\">Рекомендуют <span class=\"user_liked_num\">$mark_cnt</span> $u_text: <span class=\"user_like_link\">$mark_links</span></div>";
        }
        if ($fav_add) {
            $msg = "";
            if (!$user->authorized) $msg = 'onclick="javascript: ShowFavMessage(); return false;"';
            $unit["fav_link"] = '<a class="artist in_favorite" href="/artist/' . $id_str . '?add=favorite" ' . $msg . '><span>Добавить в избранное</span></a>';
        }
        else {
      $msg = "";
      if (!$user->authorized) $msg = 'onclick="javascript: ShowFavMessage(); return false;"';
      $unit["fav_link"] = '<a class="artist out_favorite" href="/artist/'.$id_str.'?delete=favorite" '.$msg.'><span>Убрать из избранного</span></a>';
    }


        $st = SQLProvider::ExecuteQuery("select * from tbl__styles, tbl__artist2style where artist_id=$this->id and style_id=tbl_obj_id");
        $styles = "";
        foreach ($st as $key => $value) {
            $styles .= " / " . $st[$key]['title'];
        }

        $user = new CSessionUser("user");
        CAuthorizer::AuthentificateUserFromCookie(&$user);
        CAuthorizer::RestoreUserFromSession(&$user);
        if ($user->authorized || $this->id == 6288 || $this->id == 4865) {

            if ($unit['manager_name'] != "") {
                $unit['manager_name'] = '<div> ' . $unit['manager_name'] . '</div>';
            }
            if ($unit['email'] != "") {
                $unit['email'] = '<div><a href="mailto:' . $unit['email'] . '">' . $unit['email'] . '</a></div>';
            }
            if ($unit['manager_phone'] != "") {
                $unit['manager_phone'] = '<div> ' . $unit['manager_phone'] . '</div>';
            }
            if ($unit['site_address'] != "") {
                $unit['site_address'] = '<a rel="nofollow" target="_blank" href="' . $unit['site_address'] . '">' . $unit['site_address'] . '</a>';
            }
           
            if($unit['rider'] == "1") { $rider = "(c райдером)";} else { $rider = "(без райдера)"; }

            $currency_en = '';
            $currency = '';
            switch ($unit['currency']) {
              case 1:
                $currency_en = "$";
                break;
              case 2:
                $currency_en = "€";
                break;
              default:
                $currency = " р.";
                break;
            }
            number_format($unit["cost_rent"], 0, ' ', ' ');             
            
            if ($unit['price_from'] != "" && $unit['price_to'] != "") {
            
              $unit['price'] = "<br /><b>Гонорар</b>: от ". $currency_en . number_format($unit['price_from'], 0, ' ', ' ') . $currency . " до ". $currency_en . number_format($unit['price_to'], 0, ' ', ' ') . $currency . " ".$rider;
      				
            }
            elseif ($unit["price_from"]!="")
            {
                $unit['price'] = "<br /><b>Гонорар</b> от ". $currency_en . number_format($unit['price_from'], 0, ' ', ' ') . $currency. " ".$rider;
            }
            elseif ($unit["price_to"]!="")
            {
                $unit['price'] = "<br /><b>Гонорар</b> до ". $currency_en . number_format($unit['price_to'], 0, ' ', ' ') . $currency . " ".$rider;
            }            
            else {
                    $unit['price'] = "";
            }

        }
        else {
            $unit['manager_name'] = "";
            $unit['email'] = "";
            $unit['manager_phone'] = "";

            
            if ($unit['price_from'] != "" && $unit['price_to'] != "") {
              $gonorar = " и гонорар";
              $gonorars = " и гонорара";
            }
            else { $gonorar = ''; $gonorars = ''; }
            
            $unit['site_address'] = "
            <br />
            <div style=\" padding: 0px;\">
                Контактные данные".$gonorar." доступны только зарегистрированным пользователям.<br /><br />
                Для просмотра контактов".$gonorars." <a href=\"\" onclick=\"javascript: ShowLogonDialog(); return false;\">войдите</a> или <a href=\"\" onclick=\"javascript: ShowRegUser(); return false;\">зарегистрируйтесь</a>.<br /><br />
            </div>";
            
           // $unit['price'] = "<b>Гонорар</b>: <a href=\"\" onclick=\"javascript: ShowLogonDialog(); return false;\">войдите</a> или <a href=\"\" onclick=\"javascript: ShowRegUser(); return false;\">зарегистрируйтесь</a>";
              $unit['price'] = '';
            }


            $unit['style'] = substr($styles, 3);

            if (sizeof($unit) == 0) {
                CURLHandler::Redirect("/");
            }
            
            /* Baltic IT */
            $subgrs = SQLProvider::ExecuteQuery("select * from tbl__artist_subgroup, tbl__artist2subgroup where artist_id=$this->id and subgroup_id=tbl_obj_id");
            $subgrArray = array();
            foreach ($subgrs as $gr) {
                array_push($subgrArray,$gr['title']);
            }
            $subgr_list = implode(", ",$subgrArray);
            /* End Baltic It*/ 
            
            $title = $this->GetControl("title");
            $title->text = $unit["title"].' - '.$subgr_list;
            
            
            if ($unit["country"] <= 0) {
                $unit["country_title"] = $unit["other_country"];
            }
            $unit["region_title"] = $unit["country_title"] .
                ((!IsNullOrEmpty($unit["city_title"])) ? " / " . $unit["city_title"] : "");
            //news
            $news = SQLProvider::ExecuteQuery("select rn.*,DATE_FORMAT(date,'%d.%m.%y') as `strdate` from tbl__resident_news rn where active=1
                                       and resident_id=$this->id and resident_type='artist' order by rn.date desc");
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
			$is_subGroup = SQLProvider::ExecuteQuery('SELECT subgroup_id FROM `tbl__artist2subgroup` WHERE artist_id = '.$unit['tbl_obj_id'].' ORDER BY rand() LIMIT 1');			
			$is_subGroup = !empty($is_subGroup[0]['subgroup_id']) ? $is_subGroup[0]['subgroup_id'] : 0;  
			$isGroup  = !empty($unit['group']) ? $unit['group'] : 0;
		    $isRecomended = SQLProvider::ExecuteScalar("SELECT recommended from tbl__artist_doc where tbl_obj_id=".$unit['tbl_obj_id']);
			$similar = SQLProvider::ExecuteQuery('SELECT * FROM tbl__artist_doc  
											WHERE tbl_obj_id IN (
												SELECT a2s.artist_id 
												FROM tbl__artist2subgroup a2s 
												JOIN tbl__artist_subgroup sg 
												ON sg.tbl_obj_id = a2s.subgroup_id 
												WHERE sg.parent_id='.$isGroup.') 
											AND tbl_obj_id IN (
												select artist_id 
												FROM tbl__artist2subgroup 
												WHERE subgroup_id='.$is_subGroup.')
											AND tbl_obj_id != '.$unit['tbl_obj_id'].' 
											AND active=1 
											ORDER BY rand() 
											LIMIT 4');
		
			if(!empty($similar)&&$isRecomended==0){
				$unit["similar"] = '<h4 class="detailsBlockTitle"><a name="similar">Похожие артисты</a></h4>
				<table width="100%" class="similarBlock">';
				foreach($similar as $i=>$s){
                $unit["similar"].= 
				($i%2 == 0?'<tr>':'').
                '<td><div class="logo_wrap"><img src="/upload/'.$s['logo'].'" class="logo120border" alt="" title=""></div><strong><a href="/artist/'.$s['title_url'].'">'.$s['title'].'</a></strong><br/>'.substr(strip_tags($s['description']),0,125).'</td>'.
                ($i%2 != 0?'</tr>':'');
            }
            $unit["similar"].='</table>';
        }
			$unit["similar"] = !empty($unit["similar"]) ? $unit["similar"] : '';
            $photos = $this->GetControl("photos");
            $photos->dataSource = SQLProvider::ExecuteQuery(
                "select * from `vw__artist_photos`
                  where artist_id=$this->id and hasImage>0 limit 8");
            $unit["photos"] = $photos->Render();

            //mp3 load
            $mp3s = SQLProvider::ExecuteQuery("select * from tbl__upload where tbl_obj_id in (select file_id from tbl__artist2mp3file where artist_id=$this->id) limit 5");
            $mkeys = array_keys($mp3s);
            foreach ($mkeys as $mkey) {
                $mp3s[$mkey]["jfile"] = str_replace("'", "\'", $mp3s[$mkey]["file"]);
                $mp3s[$mkey]["dfile"] = base64_encode($mp3s[$mkey]["file"]);
                $mp3s[$mkey]["jtitle"] = str_replace("\"", "\\'", str_replace("'", "\\'", $mp3s[$mkey]["title"]));
                $mp3s[$mkey]["title"] = str_replace("\"", "", $mp3s[$mkey]["title"]);
            }
            $mp3List = $this->GetControl("mp3List");
            $mp3List->dataSource = $mp3s;

            $unit["mp3List"] = $mp3List->Render();
            $unit["hasMP3"] = sizeof($mp3s) > 0 ? "visible" : "hidden";
            //video load
            $matches = array();
            if (strlen($unit['youtube_video']) > 0 && (preg_match('/^http:\/\/[w\.]*youtube\.com\/watch\?v=([A-z0-9-_]+).*$/i', $unit['youtube_video'], $matches) > 0)) {
                $unit["video_visible"] = "";
                $unit["youtubevideo"] = $matches[1];
            }
            else {
                $unit["video_visible"] = 'style="display: none;"';
                ;
            }
            $unit["logo_visible"] = IsNullOrEmpty($unit["logo"]) ? "hidden" : "visible";
			
			
			$unit["description"] = nl2br(strip_tags($unit["description"]));
			$details = $this->GetControl("details");
			$details->dataSource = $unit;
            //Remove direct
            if ($unit['tbl_obj_id'] == 6288 || $unit['tbl_obj_id'] == 4865 || $unit['direct'] == 1) {
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
            
            
            //groups
            $groups = SQLProvider::ExecuteQuery("select
            sg.`tbl_obj_id` AS `child_id`,
            sg.`parent_id`,
            sg.`title`,
            sg.`title_url`,
            sg.`order_id`,
            sg.`priority`
          from
            `tbl__artist_subgroup` sg
          union
          select
            g.`tbl_obj_id` AS `child_id`,
            0 AS `parent_id`,
            g.`title` AS `title`,
            g.`title_url`,
            g.`order_id`,
            g.`priority`
          from
            `tbl__artist_group` g
          ORDER by priority desc");
            foreach ($groups as $key => $value) {
                $groups[$key]["selected"] = "";
                $groups[$key]["red"] = "red";
                $groups[$key]["link"] = "/artist/".$value['title_url'];
            }
            $groupList = $this->GetControl("groupList");
            $groupList->dataSource = $groups;
        //filter
        $searchDS["artist_doc_name"] = "";
        $countries = SQLProvider::ExecuteQuery("select * from  `tbl__countries` ");
        array_unshift($countries, array("title" => "все", "tbl_obj_id" => ""));
        $selcountry = new CSelect();
        $selcountry->dataSource = $countries;
        $selcountry->valueName = "tbl_obj_id";
        $selcountry->titleName = "title";
        $selcountry->name = "artist_doc_country";
        $selcountry->size = 1;
        $searchDS["artist_doc_country"] = $selcountry->Render();
        $styles = SQLProvider::ExecuteQuery("select * from  `tbl__styles` ");
        array_unshift($styles, array("title" => "все", "tbl_obj_id" => ""));
        $selstyle = new CSelect();
        $selstyle->dataSource = $styles;
        $selstyle->valueName = "tbl_obj_id";
        $selstyle->titleName = "title";
        $selstyle->name = "artist_doc_style";
        $selstyle->size = 1;
        $searchDS["artist_doc_style"] = $selstyle->Render();
        $link = "/artist";
        $searchDS["postBackUrl"] = $link;
        $search = $this->GetControl("search");
        $search->dataSource = $searchDS;
		
		$submenu = $this->GetControl("submenu");
		$submenu->headerTemplate =
			'<div class="artist_btn_show submenu_controll" style="background-color: #{bgcolor}; height:30px; padding: 0 15px 0 37px; position: relative;">
			<form method="get" id="form_find_artist" action="/artist/">
			<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr valign="middle"><td>';
				$submenu->footerTemplate =
				 '</td><td><img src="/images/front/0.gif" width="1" height="30"></td><td align="right" style="padding-right: 60px;">
					<span class="submenu">Поиск по названию <input name="artist_doc_name" type="text" size="20" value="'.$artist_doc_name.'"></span>
					<span class="submenu">Поиск по стране '.$selcountry->Render().'</span>
					<span id="find_style" class="submenu" style="cursor: pointer;" onclick="FindStylesDlg()">Поиск по стилю</span>       
					</tr></table></form></div>';
		
				if (!IsNullOrEmpty($artist_doc_style_title)) {
					$this->search_styles = "<div class=\"artist\">Стили: $artist_doc_style_title</div>";
				}
		
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
