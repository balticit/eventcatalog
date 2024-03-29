<?php
class resident_news_php extends CPageCodeHandler
{
    private $newsLimit = 25;

	public function resident_news_php()
	{
		$this->CPageCodeHandler();
	}
/*  ��� ����� �������, ����������� �����
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
    			$resident = 'all';
    		}
     
     
    if( $resident == 'all')  {
    
    $start = ($page * $limit);
    
    $res_news = SQLProvider::ExecuteQuery(
            "select rn.*, DATE_FORMAT(date,'%d.%m.%Y') as `strdate`, rn.title as title
						 from `tbl__resident_news` rn
												where rn.`active`=1
												order by rn.`date` DESC limit $start, $limit
												");
		foreach($res_news as $key => $val) {
		
		  if( $res_news[$key]["title_url"] == '') { $res_news[$key]["title_url"] = 'news'.$res_news[$key]["tbl_obj_id"];}
		  $res_news[$key]["news_url"] = $res_news[$key]["title_url"];
		
			$res = SQLProvider::ExecuteQuery("SELECT * FROM tbl__".$res_news[$key]["resident_type"]."_doc WHERE tbl_obj_id=".$res_news[$key]["resident_id"]);
			$res_news[$key]["title_url"] = $res[0]['title_url'];
			$res_news[$key]["resident_name"] = $res[0]['title'];
			

			
			if (IsNullOrEmpty($res_news[$key]["logo_image"]))
		  $res_news[$key]["logo_image"] = "/images/nologo.png";
		  else
		  $res_news[$key]["logo_image"] = "/upload/".GetFileName($res_news[$key]["logo_image"]);
			

			
			
			$res_news[$key]["title"] = CutString($res_news[$key]["title"]);
			$res_news[$key]["short_text"] = strip_tags(CutString($res_news[$key]["text"], 250));
			$res_news[$key]["color"] = getProBackgroud($val['resident_type']);
			
			
			switch($val['resident_type']) {
			case 'area': $res_news[$key]['sub'] = '������� ��������';  break;
			case 'artist': $res_news[$key]['sub'] = '������� �������'; break;
			case 'contractor': $res_news[$key]['sub'] = '������� ����������'; break;
			case 'agency': $res_news[$key]['sub'] = '������� ���������'; break;
			}
		}
		
		$resCount = SQLProvider::ExecuteQuery( "select * from tbl__resident_news	where active=1");
		$newsCount =count($resCount);

		$this->GetControl("newAreas")->dataSource = $res_news;
    


    }
    else {
		// �� ��������� ������� �������, ����� �� ������ ��� ���� ������� � ����
		$sql  = 'SELECT rn.*, DATE_FORMAT(rn.`date`,"%d.%m.%Y") as strdate, res.title_url, rn.title_url as news_url, res.title resident_name, '.
					(in_array($resident, array('contractor','agency')) ? 'rn.logo_image' : 'rn.logo_image logo_image').', '.
					'(SELECT COUNT(tbl_obj_id) FROM tbl__resident_news WHERE resident_type="'.$resident.'" AND active=1 ) c '.
				'FROM tbl__resident_news rn '.
					'LEFT JOIN tbl__'.$resident.'_doc res ON res.tbl_obj_id = rn.resident_id '.
				'WHERE rn.active = 1 AND rn.resident_type = "'.$resident.'" '.
				'ORDER BY rn.`date` DESC '.
				'LIMIT '.($page * $limit).', '.$limit;
				
        $newsData = SQLProvider::ExecuteQuery($sql);
        if(!empty($newsData)){
            foreach($newsData as &$news){
            
                if( $news["news_url"] == '') { $news["news_url"] = 'news'.$news["tbl_obj_id"];}
		            $news["news_url"] = $news["news_url"];
            
                $news['short_text'] = substr(strip_tags($news['text']),0,250);
                $news['resident_type'] = $resident;
				        $news['color'] = getProBackgroud($resident);
				        
				        if (IsNullOrEmpty($news["logo_image"]))
          		  $news["logo_image"] = "/images/nologo.png";
          		  else
          		  $news["logo_image"] = "/upload/".GetFileName($news["logo_image"]);
				        
				        
            }
            $newsCount = $newsData[0]['c'];
            
            $newAreas = $this->GetControl("newAreas");
            $newAreas->dataSource = $newsData;
        }

        }
        
        // ��������
        $pCount = floor($newsCount/$limit) + ($newsCount%$limit>0?0:1);
        $rewriteParams = $_GET;
        $pager = $this->GetControl("pager");
        $pager->currentPage = $page+1;
        $pager->totalPages = $pCount;
        $pager->rewriteParams = $rewriteParams;
        
        // �������
        $menus = array(
            array('link'=>'/','title'=>'��� �������','selected'=>($resident=='all'?'class="selected"':''),'gray'=>'gray','color'=>'000000;'),
            array('link'=>'/resident_news/contractor','title'=>'������� �����������','selected'=>($resident=='contractor'?'class="selected"':''),'gray'=>'gray','color'=>'F05620;'),
            array('link'=>'/resident_news/area','title'=>'������� ��������','parent_id'=>0,'priority'=>4,'child_id'=>4,'selected'=>($resident=='area'?'class="selected"':''),'gray'=>'gray','color'=>'3399ff;'),
            array('link'=>'/resident_news/artist','title'=>'������� ��������','parent_id'=>0,'priority'=>2,'child_id'=>2,'selected'=>($resident=='artist'?'class="selected"':''),'gray'=>'gray','color'=>'ff0066;'),
            array('link'=>'/resident_news/agency','title'=>'������� ��������','parent_id'=>0,'priority'=>1,'child_id'=>1,'selected'=>($resident=='agency'?'class="selected"':''),'gray'=>'gray','color'=>'99cc00;'),
        );
		
		
		//$news_title = '';   
		switch($resident) {
			case 'contractor' 	: $news_title = '�����������'; break;
			case 'area' 		: $news_title = '��������'; break;
			case 'artist' 		: $news_title = '��������'; break;
			case 'agency'		: $news_title = '��������'; break;
			default: $news_title = '����������';
		}
		
		
		$groupList = $this->GetControl("typeList");		
    $groupList->dataSource = $menus;
		
		/*
		$mainMenu = $this->GetControl("menu");
            $mainMenu->dataSource["redevent"] =
              array("link"=>"http://redevent.ru/",
                    "imgname"=>"redevent",
                    "title"=>"",
                    "ads_class"=>"reklama",
                    "target"=>'target="_blank"');
		*/
		// && ����� ����������
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
