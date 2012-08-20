<?php
class area_details_php extends CPageCodeHandler
{
	private $id = 0;
	public $is_type = false;
	public $type_id = null;
	public $subtype_id = null;
	public $pageSize = 25;
	public $recommendedLimit = 10;
	public $newLimit = 7;
	public $newsLimit = 9;
	public $ratedLimit = 15;
	public $is_main = true;
	public $descriptionSize = 256;

	public function area_details_php()
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

	public function PreRender()
	{
		$id_str = GP("id");
		if (!is_null($id_str)) {
			//can be type or subtype
			$this->type_id = SQLProvider::ExecuteScalar("select tbl_obj_id from tbl__area_types where title_url = '" . mysql_real_escape_string($id_str) . "'");
			if ($this->type_id == null) {
				//subgroup
				$this->subtype_id = SQLProvider::ExecuteScalar("select tbl_obj_id from tbl__area_subtypes where title_url = '" . mysql_real_escape_string($id_str) . "'");
			}
			if (is_null($this->type_id) && is_null($this->subtype_id)) {
				$this->is_type = false;
				$this->id = SQLProvider::ExecuteScalar("select tbl_obj_id from tbl__area_doc where title_url = '" . mysql_real_escape_string($id_str) . "'");
			}
			else {
				$this->is_type = true;
				$stp = $this->subtype_id;
				if (!is_null($stp)) {
					$this->type_id = SQLProvider::ExecuteScalar("select parent_id from tbl__area_subtypes where tbl_obj_id=$stp");
				}
			}
		}
		if ($this->is_type) {
			$metadata = $this->GetControl("metadata");
			$metadata->keywords = "эвент, площадки, аренда площадки";
			$metadata->description = "Условия аренды площадок и их рейтинг по количеству отзывов. Описание площадки, включающее размеры, расположение и удобства для клиентов.";

			$app = CApplicationContext::GetInstance();
			/*Провека адреса*/
			$av_rwParams = array("page", "letter",
			"area_doc_place", "area_doc_location",
			"area_doc_cost_from", "area_doc_cost_to",
			"area_doc_banquet_from", "area_doc_banquet_to",
			"area_doc_buffet_from", "area_doc_buffet_to",
			"metro", "category", "mdist", "mhighway", "mcity","capacity","cost","invite_catering","city",
			"car_into","my_catering");
			CURLHandler::CheckRewriteParams($av_rwParams);
			$rewriteParams = $_GET;
			unset($rewriteParams['type']);
			unset($rewriteParams['subtype']);
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




			$type = $this->type_id;
			$page = GP("page", 1);
			$subtype = $this->subtype_id;


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
				$rp = ($page - 1) * $this->pageSize;
				$areas = SQLProvider::ExecuteQuery(
				"select * from `vw__area_list_pro` $filter
				order by  priority desc, if(tbl_obj_id=$first,0,1), pro_type desc, pro_cost desc, pro_date_pay desc, title limit " . (($page - 1) * $this->pageSize) . "," . $this->pageSize);
				$areaList = $this->GetControl("areaList");
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
						$area['category'] .= '<a class="common" href="/area/' . $value['title_url'] . '">' . $value['title'] . '</a>';
					}


					$area['links'] = "";
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
							$link = "/area/".$gr['title_url'];
							array_unshift($titlefilter, CStringFormatter::buildCategoryLinks($gr['title'], null));
							array_unshift($titlefilterLinks, CStringFormatter::buildCategoryLinks($gr['title'], $link, "area"));
							$type_finded = true;
						}
						else if ($gr["child_id"] == $subtype) {
							$link = "/area/" . $gr['title_url'];
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

				$mainMenu = $this->GetControl("menu");
				switch(rand(1,2)){
				case 1:
					$mainMenu->dataSource["museum"] =array("link" => "http://15kop.ru/","imgname" => "museum","title"=>"","target" => 'target="_blank"');
					break;
					
				case 2:$mainMenu->dataSource["midas"] =
					array("link" => "http://midas.ru/?id=144",
					"imgname" => "midas",
					"title"=>"",
					"target" => "target='_blank'");
					break;
				}
				$submenu = $this->GetControl("submenu");
				$submenu->headerTemplate =
				'<div style="background: #{bgcolor}; height:30px; padding: 0px 15px 0 37px;">
		<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr valign="middle"><td nowrap>';
				$submenu->footerTemplate =
				'</td><td><img src="/images/front/0.gif" width="1" height="30"></td><td nowrap align="right" style="padding-right:60px">
		<a href="" class="submenu" onclick="javascript: DlgByPlace(); return false;">Поиск по местоположению</a>
		<a href="" class="submenu" onclick="javascript: DlgByCapacity(); return false;">Поиск по вместимости</a>
		<a href="" class="submenu" onclick="javascript: DlgByCost(); return false;">Поиск по стоимости</a>
		<a href="" class="submenu" onclick="javascript: DlgAdditional(); return false;">Расширенный поиск</a></td></tr></table></div>';		
				
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

		}
		else {
			/*Провека адреса*/
			$av_rwParams = array("id", "add", "delete");
			CURLHandler::CheckRewriteParams($av_rwParams);
			if (is_numeric($this->id))
			SQLProvider::ExecuteNonReturnQuery("update tbl__area_doc set visited = ifnull(visited,0)+1 where tbl_obj_id=$this->id");
			else
			CURLHandler::ErrorPage();

			$unit = SQLProvider::ExecuteQuery("select
	`a`.`tbl_obj_id` AS `tbl_obj_id`,
	replace(replace(`a`.`title`,_cp1251'\"',_cp1251''),_cp1251'\'',_cp1251'') AS `title`,
	`a`.`description` AS `description`,
	`a`.`menu` AS `menu`,
	`a`.`city` AS `city`,
	`a`.`city_location` AS `city_location`,
	`a`.`address` AS `address`,
	`a`.`phone` AS `phone`,
	`a`.`rent` AS `rent`,
	`a`.`email` AS `email`,
	`a`.`site_address` AS `site_address`,
	`a`.`selection` AS `selection`,
	`a`.`max_count_man` AS `max_count_man`,
	`a`.`max_sitting_man` AS `max_sitting_man`,
	`a`.`cost_service` AS `cost_service`,
	`a`.`cost_banquet` AS `cost_banquet`,
	`a`.`cost_official_buffet` AS `cost_official_buffet`,
	`a`.`cost_rent` AS `cost_rent`,
	`a`.`cost_service` AS `cost_service`,
	`a`.`service_entrance` AS `service_entrance`,
	`a`.`wardrobe` AS `wardrobe`,
	`a`.`stage` AS `stage`,
	`a`.`makeup_rooms` AS `makeup_rooms`,
	`a`.`sound` AS `sound`,
	`a`.`light` AS `light`,
	`a`.`panels` AS `panels`,
	`a`.`projector` AS `projector`,
	`a`.`car_into` AS `car_into`,
	`a`.`date_open` AS `date_open`,
	`a`.`kitchen_features` AS `kitchen_features`,
	`a`.`kitchen` AS `kitchen`,
	`a`.`invite_catering` AS `invite_catering`,
	`a`.`parking` AS `parking`,
	`a`.`equipment` AS `equipment`,
	`a`.`dancing` AS `dancing`,
	`a`.`style` AS `style`,
	`a`.`plus` AS `plus`,
	`a`.`active` AS `active`,
	`a`.`logo` AS `logo`,
	`a`.`priority` AS `priority`,
	`a`.`direct` AS `direct`,
	`a`.`location_scheme`,
	`a`.`registration_date`,
	`a`.`coords`,
	`a`.`youtube_video`,
	IF(`a`.`city`>0,`c`.`title`,`a`.other_city) AS `city_name`,
	count(`ah`.`tbl_obj_id`) AS `halls_count`
from
	(select * from `tbl__area_doc` where tbl_obj_id=$this->id) `a` left join `tbl__city` `c` on `a`.`city` = `c`.`tbl_obj_id` left join `tbl__area_halls` `ah` on `ah`.`area_id` = `a`.`tbl_obj_id`
where
	(`a`.`active` = 1)
group by
	`a`.`tbl_obj_id`,`a`.`youtube_video`,`a`.`location_scheme`,`a`.`title`,`a`.`description`,`a`.`menu`,`a`.`city`,`a`.`address`,`a`.`phone`,`a`.`rent`,`a`.`email`,`a`.`site_address`,`a`.`selection`,`a`.`max_count_man`,`a`.`max_sitting_man`,`a`.`date_open`,`a`.`kitchen`,`a`.`parking`,`a`.`equipment`,`a`.`dancing`,`a`.`style`,`a`.`plus`,`a`.`active`,`a`.`logo`,`a`.`location_scheme`,`c`.`title`");

			if (sizeof($unit)==1)
			$unit = $unit[0];
			else
			CURLHandler::ErrorPage();
			$unit['reg_date'] = onSiteTime($unit['registration_date']);
			$unit['description'] = (!empty($unit['description'])?'<h4 class="detailsBlockTitle"><a name="description">Описание</a></h4>'.$unit['description']:'');
			
			$pro_type = getProType('area',$this->id);
			$unit['pro_logo_prew'] = '';
			if($pro_type == 1 || $pro_type == 2){
				$unit['pro_logo_prew'] = getProLogoForPreview('area');
			}
			
			// cost_banquet
			if($unit["cost_banquet"]!='' && $unit["cost_banquet"]!='0'){
			  if(is_numeric($unit["cost_banquet"])) { 
          $val = ' р.'; $cost = number_format($unit["cost_banquet"], 0, ' ', ' '); 
        } 
        else { $val = ''; $cost = $unit["cost_banquet"]; }
				$unit["cost_banquet"]=	
				'<tr><td class="table_row">
          <div class="blue_button">Стоимость проведения банкета на 1 персону:</div>
        	<span>'.$cost.$val.'</span>
      	</td></tr>
        ';
			}else{
				$unit["cost_banquet"]="";
			}
			
			//cost_official_buffet
			if($unit["cost_official_buffet"]!='' && $unit["cost_official_buffet"]!='0'){
			  if(is_numeric($unit["cost_official_buffet"])) { 
          $val = ' р.'; $cost = number_format($unit["cost_official_buffet"], 0, ' ', ' '); 
        } 
        else { $val = ''; $cost = $unit["cost_official_buffet"]; }
				$unit["cost_official_buffet"]=	
				'<tr><td class="table_row">
          <div class="blue_button">Стоимость проведения фуршета на 1 персону:</div>
        	<span>'.$cost.$val.'</span>
      	</td></tr>
        ';
			}else{
				$unit["cost_official_buffet"]="";
			}
			
			// cost_rent
			if($unit["cost_rent"]!='' && $unit["cost_rent"]!='0'){
			  if(is_numeric($unit["cost_rent"])) { 
          $val = ' р.'; $cost = number_format($unit["cost_rent"], 0, ' ', ' '); 
        } 
        else { $val = ''; $cost = $unit["cost_rent"]; }
				$unit["cost_rent"]=	
				'<tr><td class="table_row">
          <div class="blue_button">Дополнительная арендная плата за проведение мероприятия в случае закрытия площадки:</div>
        	<span>'.$cost.$val.'</span>
      	</td></tr>
        ';
			}else{
				$unit["cost_rent"]="";
			}
			
			
			//cost_service
			if($unit["cost_service"]!=0){
				$unit["cost_service"]=	
				'<tr><td class="table_row">
          <div class="blue_button">Обслуживание:</div>
        	<span>Оплачивается дополнительно</span>
      	</td></tr>';
			}else{
				$unit["cost_service"]="";
			}
			
      //parking
      if($unit["parking"]!='' && $unit["parking"]!='0'){
      
        if(is_numeric($unit["parking"])) { $val = ' мест'; } else { $val = ''; }
				$unit["parking"]=	
				'<tr><td class="table_row">
          <div class="blue_button">Парковка:</div>
        	<span>'.$unit["parking"].$val.'</span>
      	</td></tr>
        ';
			}else{
				$unit["parking"]="";
			}
			
			//service_entrance
      if($unit["service_entrance"]!='' && $unit["service_entrance"]!='0'){
				$unit["service_entrance"]=	
				'<tr><td class="table_row">
          <div class="blue_button">Техничекий служебный вход:</div>
        	<span>Есть</span>
      	</td></tr>
        ';
			}else{
				$unit["service_entrance"]="";
			}
			
			//service_entrance
      if($unit["wardrobe"]!='' && $unit["wardrobe"]!='0'){
				$unit["wardrobe"]=	
				'<tr><td class="table_row">
          <div class="blue_button">Гардероб:</div>
        	<span>'.$unit["wardrobe"].'</span>
      	</td></tr>
        ';	
			}else{
				$unit["wardrobe"]="";
			}
			
			//stage
      if($unit["stage"]!='' && $unit["stage"]!='0'){
				$unit["stage"]=	
				'<tr><td class="table_row">
          <div class="blue_button">Сцена:</div>
        	<span>'.$unit["stage"].'</span>
      	</td></tr>
        ';
				
			}else{
				$unit["stage"]="";
			}
			
			//dancing
      if($unit["dancing"]!='' && $unit["dancing"]!='0'){
        if(is_numeric($unit["dancing"])) { $val = ' м2'; } else { $val = ''; }
				$unit["dancing"]=	
				'<tr><td class="table_row">
          <div class="blue_button">Танцпол:</div>
        	<span>'.$unit["dancing"].$val.'</span>
      	</td></tr>
        ';
			}else{
				$unit["dancing"]="";
			}
			
			//makeup_rooms
      if($unit["makeup_rooms"]!='' && $unit["makeup_rooms"]!='0'){
        if(is_numeric($unit["makeup_rooms"])) { $val = ' шт.'; } else { $val = ''; }
				$unit["makeup_rooms"]=	
				'<tr><td class="table_row">
          <div class="blue_button">Гримерки:</div>
        	<span>'.$unit["makeup_rooms"].$val.'</span>
      	</td></tr>
        ';
			}else{
				$unit["makeup_rooms"]="";
			}
			
			//sound
      if($unit["sound"]!='' && $unit["sound"]!='0'){
        if(is_numeric($unit["sound"])) { $val = ' кВт.'; } else { $val = ''; }
				$unit["sound"]=	
				'<tr><td class="table_row">
          <div class="blue_button">Звук:</div>
        	<span>'.$unit["sound"].$val.'</span>
      	</td></tr>
        ';
			}else{
				$unit["sound"]="";
			}
			
			//light
      if($unit["light"]!='0'){
        
				$unit["light"]=	
				'<tr><td class="table_row">
          <div class="blue_button">Свет:</div>
        	<span>Есть</span>
      	</td></tr>
        ';
			}else{
				$unit["light"]="";
			}
			
			
			//panels
      if($unit["panels"]!='' && $unit["panels"]!='0'){
				$unit["panels"]=	
				'<tr><td class="table_row">
          <div class="blue_button">Плазменые панели:</div>
        	<span>'.$unit["panels"].'</span>
      	</td></tr>
        ';
			}else{
				$unit["panels"]="";
			}
			
			//projector
      if($unit["projector"]!='' && $unit["projector"]!='0'){
				$unit["projector"]=	
				'<tr><td class="table_row">
          <div class="blue_button">Проэкционный экран/Проектор:</div>
        	<span>'.$unit["projector"].'</span>
      	</td></tr>
        ';
			}else{
				$unit["projector"]="";
			}
			
			
			//car_into
      if($unit["car_into"]!='0'){
				$unit["car_into"]=	
				'<tr><td class="table_row">
          <div class="blue_button">Возможность установки автомобиля внутри площадки:</div>
        	<span>Есть</span>
      	</td></tr>';
			}else{
				$unit["car_into"]="";
			}
			
			//invite_catering
      if($unit["invite_catering"]!='0'){
				$unit["invite_catering"]=	
				'<tr><td class="table_row">
          <div class="blue_button">Возможность приглашения стороннего кейтеринга:</div>
        	<span>Есть</span>
      	</td></tr>';
			}else{
				$unit["invite_catering"]="";
			}
			
			//kitchen
      if($unit["kitchen"]!='' && $unit["kitchen"]!='0'){
				$unit["kitchen"]=	
				'<tr><td class="table_row">
          <div class="blue_button">Кухня:</div>
        	<span>'.$unit["kitchen"].'</span>
      	</td></tr>
        ';
			}else{
				$unit["kitchen"]="";
			}
			
			//kitchen_features
      if($unit["kitchen_features"]!='' && $unit["kitchen_features"]!='0'){
				$unit["kitchen_features"]=	
				'<tr><td class="table_row">
          <div class="blue_button">Особенности кухни:</div>
        	<span>'.$unit["kitchen_features"].'</span>
      	</td></tr>
        ';
			}else{
				$unit["kitchen_features"]="";
			}
			
			


			$unit["u_link"] = "";
			$u_links = SQLProvider::ExecuteQuery("select ru.tbl_obj_id, IF(ru.nikname is NULL or ru.nikname = '',ru.title,ru.nikname) title from tbl__registered_user_link_resident rl left join tbl__registered_user ru on ru.tbl_obj_id = rl.user_id
										where rl.resident_type = 'area' and rl.resident_id = $this->id");
			if (sizeof($u_links)>0 && !empty($pro_type))
			{
				$unit["u_link"] = '<h4 class="detailsBlockTitle"><a name="video">Представители площадки</a></h4>';
				foreach ($u_links as $num=>$u_link)
				{
					$unit["u_link"] .= "<a href=\"/u_profile/?type=user&id=".$u_link["tbl_obj_id"]."\">".$u_link["title"]."</a><br />";
				}
				$unit["u_link"] .= "</div>";
			}
			
			
			/* Baltic IT */
      $subtypes = SQLProvider::ExecuteQuery("select distinct title,tbl_obj_id from tbl__area_types where tbl_obj_id in (select type_id from tbl__area2type where area_id=$this->id) ");
      $areaListArray = array();
      foreach ($subtypes as $type) {
          if (isset($type["title"])) {
              $title = $type["title"];
              array_push($areaListArray,$title); // balticit, массив категорий
          }
      }
      
      //subtypes
      $subtypes = SQLProvider::ExecuteQuery("select distinct title,tbl_obj_id,parent_id from tbl__area_subtypes where tbl_obj_id in (select subtype_id from tbl__area2subtype where area_id=$this->id) ");
      $areaSubListArray = array();
      foreach ($subtypes as $type) {
          if (isset($type["title"])) {
              $title = $type["title"];
              array_push($areaSubListArray,$title); // balticit, массив подкатегорий
          }
      }
      $area_list = implode(", ",$areaListArray).' '.implode(", ",$areaSubListArray); // balticit, implode категорий подкатегорий
      /* End Baltic It*/ 
			
			$title = $this->GetControl("title");
			$title->text = $unit["title"].' - '.$area_list;
			
			
			$halls = SQLProvider::ExecuteQuery("select * from `tbl__area_halls` where area_id=$this->id");
			for ($i=0;$i<sizeof($halls);$i++)
			{
				$halls[$i]["number"] = $i+1;
			}
			
	//		$hallList = $this->GetControl("hallList");
	//		$hallList->dataSource = $halls;
	//		$unit["halls_list"] = $hallList->Render();
			

			
			// Костыль вывод залов
			$col_1 = false;
			$col_2 = false;
			$col_3 = false;
			$col_4 = false;
			
			for ($i=0;$i<sizeof($halls);$i++){
        if(!empty($halls[$i]["max_places_banquet"])) { $col_1 = true;};
        if(!empty($halls[$i]["max_places_official_buffet"])) { $col_2 = true;};
        if(!empty($halls[$i]["max_places_conference"])) { $col_3 = true;};
        if(!empty($halls[$i]["cost_conference"])) { $col_4 = true;};
      }
			
			$unit["halls"] ='<tr>';
			if($col_1 or $col_2 or $col_3 or $col_4) { $unit["halls"] .= '<td style="width: 140px; text-align:left; border-bottom:1px #ccc solid;" >&nbsp;<b>Название зала</b>&nbsp;</td>'; }
			if($col_1) {
      $unit["halls"] .='<td style="width: 70px; height:25px; text-align:center; border-bottom:1px #ccc solid;" >&nbsp;<b>Банкет</b>&nbsp;</td>';
      }
      if($col_2) {
      $unit["halls"] .='<td style="height:25px;text-align:center;border-bottom:1px #ccc solid;" >&nbsp;<b>Фуршет</b>&nbsp;</td>';
      }
      if($col_3) {
      $unit["halls"] .='<td style="height:25px;text-align:center;border-bottom:1px #ccc solid;" >&nbsp;<b>Конференция</b>&nbsp;</td>';
      }
      if($col_4) {
      $unit["halls"] .='<td style="height:25px;text-align:center;border-bottom:1px #ccc solid;" >&nbsp;<b>Стоимость аренды зала</b>&nbsp;</td>';
      }
      $unit["halls"] .='</tr>';
			
			for ($i=0;$i<sizeof($halls);$i++)
			{
			 $unit["halls"] .='<tr>';
			  $unit["halls"] .='<td style="text-align:left;border-bottom:1px #ccc solid;" height="25">'.$halls[$i]["title"].'</td>';
  			if($col_1) {
        $unit["halls"] .='<td style="text-align:center;border-bottom:1px #ccc solid;" height="25">'.$halls[$i]["max_places_banquet"].'</td>';
        }
        if($col_2) {
        $unit["halls"] .='<td style="text-align:center;border-bottom:1px #ccc solid;" height="25">'.$halls[$i]["max_places_official_buffet"].'</td>';
        }
        if($col_3) {
        $unit["halls"] .='<td style="text-align:center;border-bottom:1px #ccc solid;" height="25">'.$halls[$i]["max_places_conference"].'</td>';
        }
        if($col_4) {
        $unit["halls"] .='<td style="text-align:center;border-bottom:1px #ccc solid;" height="25">'.$halls[$i]["cost_conference"].'</td>';
        }
       $unit["halls"] .='</tr>';
			}
			
			

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
											and resident_type = 'area'
											and resident_id=$this->id");
				if (sizeof($fav))
				$fav_add = false;

				if (GP("add")=="favorite") {
					SQLProvider::ExecuteNonReturnQuery("insert into tbl__user_favorites (user_type,user_id,resident_type,resident_id)
							values ('".$user->type."',".$user->id.",'area',$this->id)");
					CURLHandler::Redirect("/area/$id_str");
				}
				if (GP("delete")=="favorite") {
					SQLProvider::ExecuteNonReturnQuery("delete from tbl__user_favorites
					where tbl__user_favorites.user_type='".$user->type."' and tbl__user_favorites.user_id=".$user->id." and
					tbl__user_favorites.resident_type='area' and tbl__user_favorites.resident_id=".$this->id);
					CURLHandler::Redirect("/area/$id_str");
				}
				$currentmark = SQLProvider::ExecuteScalar("select count(1) from tbl__userlike where
			to_resident_type='area' and to_resident_id='$this->id' and from_resident_type='".$user->type."' and from_resident_id='".$user->id."'");
				$show_ilike = $currentmark==0;
				if ($show_ilike and GP('action')=='ilike') {
					SQLProvider::ExecuteQuery("
						insert into tbl__userlike
						(to_resident_id,to_resident_type,from_resident_id,from_resident_type,date)
						values
						($this->id,'area',".$user->id.",'".$user->type."',FROM_UNIXTIME(".time()."))
						");
					//отправка сообщения резиденту об оценке
					$residend_info = SQLProvider::ExecuteQuery(
					"SELECT u.tbl_obj_id, u.login_type, u.email
						FROM `vw__all_users` u\n
						where u.login_type = 'area' and u.tbl_obj_id = $this->id");
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
					CURLHandler::Redirect("/area/$id_str");
				}
				if (!$show_ilike and GP('action')=='unlike') {
					SQLProvider::ExecuteQuery("
								delete from tbl__userlike
								where to_resident_id = $this->id
									and to_resident_type = 'area'
									and from_resident_id = ".$user->id."
									and from_resident_type = '".$user->type."'");

					CURLHandler::Redirect("/area/$id_str");
				}
			}
			if ($show_ilike) {
				$unit["btn_i_like"]='<form method="post"><input type="hidden" name="action" value="ilike">
			<img class="btn_ilike" src="/images/rating/btn_ilike.png" onmouseover="javascript: this.src=\'/images/rating/btn_area.png\';" onmouseout="javascript: this.src=\'/images/rating/btn_ilike.png\';" onclick="javascript: '.$btn_action.'"></form>';
			}
			else {
				$unit["btn_i_like"]='</td><td><form method="post"><input type="hidden" name="action" value="unlike"><a href="" class="black" onclick="javascript: $(this).parent().submit(); return false;"><img onmouseover="javascript: this.src=\'/images/rating/unlike_area.png\';" onmouseout="javascript: this.src=\'/images/rating/btn_unlike.png\';" src="/images/rating/btn_unlike.png" alt="Больше не рекомендую" /></a></form>';
			}
			$details=$this->GetControl("details");
			$photos = $this->GetControl("photos");
			$photos->dataSource = SQLProvider::ExecuteQuery(
			"select p.*
		from `tbl__area_photos`  ap
			join `tbl__photo` p on ap.child_id = p.tbl_obj_id
			where parent_id=$this->id limit 8");
			$unit["photos"] = $photos->Render();
			if ($unit["date_open"]!="")
			$unit["date_open"] =  '<div class="nnm_doc"><b>Дата открытия/последней реконструкции: </b>'.date("d.m.Y", strtotime($unit["date_open"])).'</div>';

			$unit["ifrent"] = ($unit["rent"]==1)?"да":"нет";




			$mark = SQLProvider::ExecuteQuery("select au.user_id,au.type,au.title from tbl__userlike ul
										join vw__all_users_full au
											on au.type = ul.from_resident_type and
												au.user_id = ul.from_resident_id
										where to_resident_type='area' and to_resident_id='$this->id'");
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
				$unit["fav_link"] = '<a class="area in_favorite" href="/area/'.$id_str.'?add=favorite" '.$msg.'><span>Добавить в избранное</span></a>';
			}
			else {
				$msg = "";
				if (!$user->authorized) $msg = 'onclick="javascript: ShowFavMessage(); return false;"';
				$unit["fav_link"] = '<a class="area out_favorite" href="/area/'.$id_str.'?delete=favorite" '.$msg.'><span>Убрать из избранного</span></a>';
			}

			//groups
			$groups =  SQLProvider::ExecuteQuery("select
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
				$groups[$key]["selected"] = "";
				$groups[$key]["blue"] = "blue";
				$groups[$key]["link"] = "/area/".$value['title_url'];
			}
			$groupList= $this->GetControl("typeList");
			$groupList->dataSource = $groups;


			$openareas = SQLProvider::ExecuteQuery("select * from vw__opened_areas where area_id=$this->id");
			if (sizeof($openareas) ==0 || $openareas[0]["tbl_obj_id"]==Null)
			{
				$unit['link_area'] = "";
			}
			else {
				$unit["link_area"] = "<a href=/opened_area/details".$openareas[0]["tbl_obj_id"]." style=\"color: red;\">Репортаж с открытия<Br> площадки</a>";
			}


			$urlpars = array();

			//types
			$subtypes = SQLProvider::ExecuteQuery("select distinct title,tbl_obj_id from tbl__area_types where tbl_obj_id in (select type_id from tbl__area2type where area_id=$this->id) ");
			$typenames = array();
			foreach ($subtypes as $type) {
				if (isset($type["title"])) {
					$urlpars["type"] = $type["tbl_obj_id"];				
					$title = $type["title"];
					$link = "/area/".CURLHandler::BuildQueryParams($urlpars);
					array_push($typenames,CStringFormatter::buildCategoryLinks($title,$link));
				}
			}
			$unit["area_type_name"] = CStringFormatter::FromArray(isset($typenames)?$typenames:array(),", ");
			
			$subtypes = SQLProvider::ExecuteQuery("select distinct title,tbl_obj_id,parent_id from tbl__area_subtypes where tbl_obj_id in (select subtype_id from tbl__area2subtype where area_id=$this->id) ");
			$typenames = array();
			foreach ($subtypes as $type) {
				if (isset($type["title"])) {
					$urlpars["type"] = $type["parent_id"];
					$urlpars["subtype"] = $type["tbl_obj_id"];				
					$title = $type["title"];
					$link = "/area/".CURLHandler::BuildQueryParams($urlpars);
					array_push($typenames,CStringFormatter::buildCategoryLinks($title,$link));
				}
			}

			$unit["area_subtype_name"] = CStringFormatter::FromArray(isset($typenames)?$typenames:array(),", ");
			$unit["logo_visible"] = IsNullOrEmpty( $unit["logo"])?"hidden":"visible";
			$unit["location_scheme_visible"] = IsNullOrEmpty( $unit["location_scheme"])?"hidden":"visible";
			$unit["menu_visible"] = IsNullOrEmpty( $unit["menu"])?"hidden":"visible";
			//news
			$news = SQLProvider::ExecuteQuery("select rn.*,DATE_FORMAT(date,'%d.%m.%y') as `strdate` from tbl__resident_news rn where active=1
									and resident_id=$this->id and resident_type='area' order by rn.date desc");
			$unit["news_list"]='';
			if(!empty($news)){
				$unit["news_list"] = '<h4 class="detailsBlockTitle"><a name="news">Новости</a></h4>';
				foreach($news as $item) {
					$item["title"] = CutString($item["title"]);
					$item["text"] = strip_tags(CutString($item["text"], 150));
					$unit["news_list"] .= getNewsItem($item);
				}
			}
			else $unit["news_list"] .= '';

			
			$is_type = SQLProvider::ExecuteQuery('SELECT type_id FROM `tbl__area2type` WHERE area_id = '.$unit['tbl_obj_id'].' ORDER BY rand() LIMIT 1');
			$is_subtype = SQLProvider::ExecuteQuery('SELECT subtype_id FROM `tbl__area2subtype` WHERE area_id = '.$unit['tbl_obj_id'].' ORDER BY rand() LIMIT 1');

			$is_type = 		!empty($is_type[0]['type_id'])	? $is_type[0]['type_id'] : 0;   
			$is_subtype = 	!empty($is_subtype[0]['subtype_id']) ? $is_subtype[0]['subtype_id'] : 0; 
			
			// var_dump($is_type);
			// var_dump($is_subtype);
			// die('!');

			/* похожие подрядчики */
			//$similar = SQLProvider::ExecuteQuery('select * from `tbl__area_doc` where active=1 order by rand() LIMIT 4');
			$isRecomended = SQLProvider::ExecuteScalar("SELECT recommended from tbl__area_doc where tbl_obj_id=".$unit['tbl_obj_id']);
			$similar = SQLProvider::ExecuteQuery('SELECT * FROM `tbl__area_doc` 
											where tbl_obj_id IN (
												SELECT area_id 
												FROM tbl__area2subtype a2s 
												JOIN tbl__area_subtypes s 
												ON s.tbl_obj_id = a2s.subtype_id 
												WHERE s.parent_id='.$is_type.') 
											and tbl_obj_id IN (
												SELECT area_id 
												FROM tbl__area2subtype 
												WHERE subtype_id='.$is_subtype.') 
											AND tbl_obj_id != '.$unit['tbl_obj_id'].' 
											AND active=1 
											ORDER BY rand() 
											LIMIT 4');
			if(!empty($similar)&&$isRecomended==0){
				$unit["similar"] = '<h4 class="detailsBlockTitle"><a name="similar">Похожие площадки</a></h4>
				<table width="100%" class="similarBlock">';
				foreach($similar as $i=>$s){
					$unit["similar"].= ($i%2 == 0?'<tr>':'').
					'<td><div class="logo_wrap"><img src="/upload/'.$s['logo'].'" class="logo120border" alt="" title=""></div><strong><a href="/area/'.$s['title_url'].'">'.$s['title'].'</a></strong><br/>'.substr(strip_tags($s['description']),0,125).'</td>'.
					($i%2!= 0?'</tr>':'');
				}
				$unit["similar"].='</table>';
			}
			else{
				$unit["similar"] = "";
			}

			if (IsNullOrEmpty( $unit["coords"]))
			$unit["map"] = " ";
			else {
				$map_data = array(
				"key"=>GOOGLEMAPKEY,
				"coords"=>$unit['coords'],
				"text"=>$unit['title']
				);
				$map = $this->GetControl("googlemap");
				$map->dataSource = $map_data;
				$unit["map"] = $map->RenderHTML();
			}
			$unit["video_block"] = "";
			$video = $this->GetControl("video");
			if ($unit["youtube_video"]!="" &&
					preg_match('/^http:\/\/[w\.]*youtube\.com\/watch\?v=([A-z0-9-_]+).*$/i', $unit['youtube_video'], $matches) > 0) {
				$video->dataSource = array("youtube_id"=>$matches[1]);
				$unit["video_block"] = $video->Render();
			}

			$details->dataSource = $unit;

			$details->dataSource = $unit;
			if ($unit['tbl_obj_id'] == 3621 or $unit['direct'] == 1  ) {
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

			/*setting filter*/
			$search = $this->GetControl("search");
			$searchDS = array();
			$subtypes = SQLProvider::ExecuteQuery("SELECT
							`tbl_obj_id`,
							`title`
							FROM
							`tbl__area_subtypes` st
							inner join  `tbl__area_types2subtypes` t2s
							on st.tbl_obj_id = t2s.child_id");
			array_unshift($subtypes,array("tbl_obj_id"=>"","title"=>""));
			$SearchSelect = new CSelect();
			$SearchSelect->dataSource = $subtypes;
			$SearchSelect->valueName = "tbl_obj_id";
			$SearchSelect->titleName = "title";
			$SearchSelect->name = "area_doc_subtype";
			$SearchSelect->class = "filter";
			$SearchSelect->size = 1;
			$searchDS["subtypes_list"]	= $SearchSelect->Render();

			$locs = SQLProvider::ExecuteQuery("select * from vw__city_location ");
			array_unshift($locs,array("tbl_obj_id"=>"","title"=>""));
			$locSelect = new CSelect();
			$locSelect->dataSource = $locs;
			$locSelect->valueName = "tbl_obj_id";
			$locSelect->titleName = "title";
			$locSelect->name = "area_doc_location";
			$locSelect->class = "filter";
			$locSelect->size = 1;
			$searchDS["area_doc_location"]	= $locSelect->Render();
			$link = "/area/";
			$searchDS["postBackUrl"] = $link;
			$searchDS["area_doc_place"] = "";
			$searchDS["area_doc_cost_from"] = "";
			$searchDS["area_doc_cost_to"] = "";
			$searchDS["area_doc_banquet_from"] = "";
			$searchDS["area_doc_banquet_to"] = "";
			$searchDS["area_doc_buffet_from"] = "";
			$searchDS["area_doc_buffet_to"] = "";
			$search->dataSource = $searchDS;
			
			//Extended search
			$submenu = $this->GetControl("submenu");
			$submenu->headerTemplate =
			'<div style="background: #{bgcolor}; height:30px; padding: 0px 15px 0 37px;">
		<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr valign="middle"><td nowrap>';
			$submenu->footerTemplate =
			'</td><td><img src="/images/front/0.gif" width="1" height="30"></td><td nowrap align="right" style="padding-right:60px">
		<a href="" class="submenu" onclick="javascript: DlgByPlace(); return false;">Поиск по местоположению</a>
		<a href="" class="submenu" onclick="javascript: DlgByCapacity(); return false;">Поиск по вместимости</a>
		<a href="" class="submenu" onclick="javascript: DlgByCost(); return false;">Поиск по стоимости</a>
		<a href="" class="submenu" onclick="javascript: DlgAdditional(); return false;">Расширенный поиск</a></td></tr></table></div>';		
			
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
			$rewriteParams = array();
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
	}
}
?>