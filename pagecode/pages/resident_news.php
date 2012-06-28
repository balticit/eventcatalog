<?php
class resident_news_php extends CPageCodeHandler
{
    private $newsLimit = 10;

	public function resident_news_php()
	{
		$this->CPageCodeHandler();
	}
/*  это писал больной, прокаженный индус
	public function PreRender()
	{

		$year = GP('year',date("Y"));

		$newAreas = SQLProvider::ExecuteQuery("
		                    select rn.*, DATE_FORMAT(rn.date,'%d.%m.%y') as `strdate`, res.title_url
		                    from `tbl__resident_news` rn
		                    join `tbl__area_doc` res on res.tbl_obj_id = rn.resident_id
												where YEAR(rn.date)='".$year."' and rn.`active`=1 and rn.`resident_type`='area'
												order by rn.`date` DESC
												");
		$newAreas2 = $this->GetControl("newAreas");
		$newAreas2->dataSource = $newAreas;

		$newContractor = SQLProvider::ExecuteQuery("
		                    select rn.*, DATE_FORMAT(rn.date,'%d.%m.%y') as `strdate`, res.title_url
		                    from `tbl__resident_news` rn
		                    join `tbl__contractor_doc` res on res.tbl_obj_id = rn.resident_id
												where YEAR(rn.date)='".$year."' and rn.`active`=1 and rn.`resident_type`='contractor'
												order by rn.`date` DESC
												");
		$newContractors = $this->GetControl("newContractors");
		$newContractors->dataSource = $newContractor;

		$newArtist = SQLProvider::ExecuteQuery("
		                    select rn.*, DATE_FORMAT(rn.date,'%d.%m.%y') as `strdate`, res.title_url
		                    from `tbl__resident_news` rn
		                    join `tbl__artist_doc` res on res.tbl_obj_id = rn.resident_id
												where YEAR(rn.date)='".$year."' and rn.`active`=1 and rn.`resident_type`='artist'
												order by rn.`date` DESC
												");
		$newArtists = $this->GetControl("newArtists");
		$newArtists->dataSource = $newArtist;

		$newAgency = SQLProvider::ExecuteQuery("
		                    select rn.*, DATE_FORMAT(rn.date,'%d.%m.%y') as `strdate`, res.title_url
		                    from `tbl__resident_news` rn
		                    join `tbl__agency_doc` res on res.tbl_obj_id = rn.resident_id
												where YEAR(rn.date)='".$year."' and rn.`active`=1 and rn.`resident_type`='agency'
												order by rn.`date` DESC
												");
		$newAgencies = $this->GetControl("newAgencies");
		$newAgencies->dataSource = $newAgency;

		$years = array();
		$yy = array();
		if ((int)date("Y")>2008) {
			for ($y = 2008; $y<(int)date("Y");$y++) {
				$yy["year"] = $y;
				$years[] = $yy;
			}
		}

		//print_r($years);
		$yearss = $this->GetControl("archive");
		$yearss->dataSource = $years;

	}
 * 
 */
    public function PreRender(){
        $page = (int)GP('page',0);
        if($page>0) $page--;
        $limit = $this->newsLimit;
        $resident = GP('resident','');
        $newsCount = 0;
		
        if(!in_array($resident, array('contractor','area','agency','artist'), true)) {
			$resident = 'contractor';
		}
        		
		// во вложенном запросе счетчик, чтобы не делать еще один коннект к базе
		$sql  = 'SELECT rn.*, DATE_FORMAT(rn.`date`,"%d.%m.%Y") as strdate, res.title_url, res.title resident_name, '.
					(in_array($resident, array('contractor','agency')) ? 'res.logo_image' : 'res.logo logo_image').', '.
					'(SELECT COUNT(tbl_obj_id) FROM tbl__resident_news WHERE resident_type="'.$resident.'" AND active=1 ) c '.
				'FROM tbl__resident_news rn '.
					'LEFT JOIN tbl__'.$resident.'_doc res ON res.tbl_obj_id = rn.resident_id '.
				'WHERE rn.active = 1 AND rn.resident_type = "'.$resident.'" '.
				'ORDER BY rn.`date` DESC '.
				'LIMIT '.($page * $limit).', '.$limit;
				
        $newsData = SQLProvider::ExecuteQuery($sql);
        if(!empty($newsData)){
            foreach($newsData as &$news){
                $news['short_text'] = substr(strip_tags($news['text']),0,250);
                $news['resident_type'] = $resident;
				$news['color'] = getProBackgroud($resident);
            }
            $newsCount = $newsData[0]['c'];
            
            $newAreas = $this->GetControl("newAreas");
            $newAreas->dataSource = $newsData;
        }
        
        
        // страницы
        $pCount = floor($newsCount/$limit) + ($newsCount%$limit>0?0:1);
        $rewriteParams = $_GET;
        $pager = $this->GetControl("pager");
        $pager->currentPage = $page+1;
        $pager->totalPages = $pCount;
        $pager->rewriteParams = $rewriteParams;
        
        // менюшка
        $menus = array(
            array('link'=>CURLHandler::$currentPath.CURLHandler::BuildQueryParams(array('resident'=>'contractor')),'title'=>'Новости подрядчиков','selected'=>($resident=='contractor'?'class="selected"':''),'gray'=>'gray','color'=>'F05620;'),
            array('link'=>CURLHandler::$currentPath.CURLHandler::BuildQueryParams(array('resident'=>'area')),'title'=>'Новости площадок','parent_id'=>0,'priority'=>4,'child_id'=>4,'selected'=>($resident=='area'?'class="selected"':''),'gray'=>'gray','color'=>'3399ff;'),
            array('link'=>CURLHandler::$currentPath.CURLHandler::BuildQueryParams(array('resident'=>'artist')),'title'=>'Новости артистов','parent_id'=>0,'priority'=>2,'child_id'=>2,'selected'=>($resident=='artist'?'class="selected"':''),'gray'=>'gray','color'=>'ff0066;'),
            array('link'=>CURLHandler::$currentPath.CURLHandler::BuildQueryParams(array('resident'=>'agency')),'title'=>'Новости агентств','parent_id'=>0,'priority'=>1,'child_id'=>1,'selected'=>($resident=='agency'?'class="selected"':''),'gray'=>'gray','color'=>'99cc00;'),
        );
		
		
		//$news_title = '';   
		switch($resident) {
			case 'contractor' 	: $news_title = 'подрядчиков'; break;
			case 'area' 		: $news_title = 'площадок'; break;
			case 'artist' 		: $news_title = 'артистов'; break;
			case 'agency'		: $news_title = 'агентств'; break;
			default: $news_title = 'резидентов';
		}
		
		
		$groupList = $this->GetControl("typeList");		
        $groupList->dataSource = $menus;
		
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
