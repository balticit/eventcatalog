<?php
class artist_php extends CPageCodeHandler
{
    public $pagesize = 25;
    public $recommendedLimit = 20;
    public $newLimit = 6;
    public $newsLimit = 7;
    public $ratedLimit = 15;
    public $is_main = true;
    public $descriptionSize = 256;
	public $search_styles = "";

    public function artist_php()
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
    
    
        $metadata = $this->GetControl("metadata");
        $metadata->keywords = "артисты, эвент-каталог, ведущий на свадьбу";
        $metadata->description = "Эвент-каталог содержит контактные данные артистов и музыкальных групп, выступающих на банкетах, вечеринках и корпоративных мероприятиях: ведущие на свадьбу, юбилей и семейное торжество.";

        $searchDS = array();
        /*Провека адреса*/
        $av_rwParams = array(
						"group", "subgroup", "page", "letter","artist_doc_name",
						"artist_doc_country", "artist_doc_style");
        CURLHandler::CheckRewriteParams($av_rwParams);
        $rewriteParams = $_GET;
        $first = "NULL";
        $page = GP("page", 1);
        $group = GP("group");
        $subgroup = GP("subgroup");
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
        if (is_numeric($group)) {
            $rewriteParams["group"] = $group;
        }
        if (is_numeric($subgroup)) {
            $rewriteParams["subgroup"] = $subgroup;
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
            $artists = SQLProvider::ExecuteQuery(
                "select * from  vw__artist_list_pro ad $filter
				order by if(tbl_obj_id=$first,0,1), pro_type desc, pro_cost desc, pro_date_pay desc, title limit $sp,$this->pagesize");
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
				
				// && присоединяем города
				if (!empty($artist['city'])) $sql = SQLProvider::ExecuteQuery('SELECT title FROM `tbl__city` WHERE tbl_obj_id = '.(int)$artist['city']);				
				$artist["city_item"] = (!empty($sql)) ? (!empty($sql[0]['title'])) ? '<span style="color: #000;">('.$sql[0]['title'].')</span>' : '' : '';				
				
                $gr = SQLProvider::ExecuteQuery("select * from tbl__artist_subgroup, tbl__artist2subgroup where artist_id=" . $artist["tbl_obj_id"] . " and subgroup_id=tbl_obj_id");
                $artist['category'] = "";
                foreach ($gr as $gkey => $value) {
                    if ($artist['category'] != "")
                        $artist['category'] .= " / ";
                    $artist['category'] .= '<a class="common" href="/artist'.$value['title_url']. '">' . $value['title'] . '</a>';
                }

                $artist["info"] = CutString(strip_tags($artist["description"]), $this->descriptionSize);

                $artist['links'] = "";
                /*$audio = SQLProvider::ExecuteQuery("select file_id from tbl__artist2mp3file where artist_id=".$artist['tbl_obj_id']);
                    if (sizeof($audio)) {
                        $artist['links'] .= '<a class="common" href="/artist/'.$artist['title_url'].'" style="margin: 0 10px 0 0">Аудио</a>';
                    }
                    $photos = SQLProvider::ExecuteQuery("select 1 from tbl__artist2photos where parent_id = ".$artist['tbl_obj_id']);
                    if (sizeof($photos)) {
                        $artist['links'] .= '<a class="common" href="/artist/'.$artist['title_url'].'?page=photos" style="margin: 0 10px 0 0">Фото</a>';
                    }
                    if ($artist["youtube_video"]) {
                        $artist['links'] .= '<a class="common" href="/artist/'.$artist['title_url'].'?page=video" style="margin: 0 10px 0 0">Видео</a>';
                    }*/
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
        }
        else
        {
            /*recommended list*/
            $recommended_conts = SQLProvider::ExecuteQuery("select tbl_obj_id, title,recommended, description, logo as logo_image, 'artist' resident_type, title_url
																										 from `tbl__artist_doc`
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
            $recomedList_1->dataSource = $this->PrepareArtistsMain($recommended_conts_1); 
			
			$recomedList_2 = $this->GetControl("RecomedList_2");
            $recomedList_2->dataSource = $this->PrepareArtistsMain($recommended_conts_2);
			
			
			// $recomedList = $this->GetControl("RecomedList");
            // $recomedList->dataSource = $this->PrepareArtistsMain($recommended);

            /*end recommended list*/

            /*new list*/
            $new_conts = SQLProvider::ExecuteQuery("select tbl_obj_id, title, description, DATE_FORMAT(registration_date,'%d.%m.%y') as formatted_date,
                                        'artist' resident_type, title_url
																				from  `tbl__artist_doc`
																				where active = 1
																				order by registration_date desc limit $this->newLimit");
          
			$new_conts_1 = array_slice($new_conts, 0, floor(count($new_conts)/2));
			$new_conts_2 = array_slice($new_conts, floor(count($new_conts)/2), count($new_conts));

            $newList_1 = $this->GetControl("NewList_1");
            $newList_1->dataSource = $this->PrepareArtistsMain($new_conts_1); 
			
			      $newList_2 = $this->GetControl("NewList_2");
            $newList_2->dataSource = $this->PrepareArtistsMain($new_conts_2);

			      // $newList = $this->GetControl("NewList");
            // $newList->dataSource = $this->PrepareArtistsMain($new);
            /*end new list*/

            /*news list*/
            /*
            $news = SQLProvider::ExecuteQuery("select rn.tbl_obj_id, rn.title, DATE_FORMAT(rn.date,'%d.%m.%y') as `strdate`, rn.resident_type, rn.resident_id,
                                                res.title_url
																						 from `tbl__resident_news` rn
                                             join `tbl__artist_doc` res on res.tbl_obj_id = rn.resident_id
																						 where rn.`active`=1 and rn.`resident_type`='artist'
																						 order by rn.`date` DESC limit $this->newsLimit");
            $newsList = $this->GetControl("NewsList");
            $newsList->dataSource = $news;
            */
            
        		$res_news = SQLProvider::ExecuteQuery(
                    "select rn.*, DATE_FORMAT(date,'%d.%m.%y') as `strdate`
        						 from `tbl__resident_news` rn
        												where rn.`active`=1 and rn.`resident_type`='artist'
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
        			case 'area': $res_news[$key]['sub'] = 'Новость площадки'; break;
        			case 'artist': $res_news[$key]['sub'] = 'Артист'; break;
        			case 'contractor': $res_news[$key]['sub'] = 'Новость подрядчика'; break;
        			case 'agency': $res_news[$key]['sub'] = 'Агентство'; break;
        			}
        		}
        		$this->GetControl("NewsList")->dataSource = $res_news;
            
            /*end news list*/

            /*rate list*/
            $ratings = SQLProvider::ExecuteQuery("select ul.to_resident_id as tbl_obj_id, c.title ,count(ul.tbl_obj_id) as voted, c.title_url
																		from tbl__userlike ul
																		join tbl__artist_doc c on c.tbl_obj_id = ul.to_resident_id and ul.to_resident_type='artist'
																		where c.active = 1
																		group by ul.to_resident_id, c.title
																		order by voted desc, tbl_obj_id limit $this->ratedLimit");
            $user = new CSessionUser("user");
            CAuthorizer::AuthentificateUserFromCookie(&$user);
            CAuthorizer::RestoreUserFromSession(&$user);
            foreach ($ratings as $key => &$rating) {
                $rating["index"] = $key + 1;
                $rating["resident_type"] = 'artist';
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
            $pro2_list->dataSource = getPro2List('artist');
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
            $groups[$key]["link"] = "/artist/".$value['title_url']. CURLHandler::BuildQueryParams($cpars);
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
                    //$urlparams["group"] = $gr["child_id"];
                    $link = "/artist/".$gr['title_url'] . CURLHandler::BuildQueryParams($urlparams);
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

?>
