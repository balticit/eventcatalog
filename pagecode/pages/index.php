<?php
class index_php extends CPageCodeHandler
{
	public $mag;
	
	public $ad_blocks = array();

  public $etv_count;
  public $etv_photo;
  public $etv_small_vis = "display: none";
  public $etv_big_vis = "";
  public $etv_comment = "";
	
	public function index_php()
	{
		$this->CPageCodeHandler();
	}

	public function PreRender()
	{
		/*Провека адреса*/
		$av_rwParams = array();
		CURLHandler::CheckRewriteParams($av_rwParams);

		/* рекламные блоки */
		$this->ad_blocks = array('contractor'=>array(),
													   'area'=>array(),
														 'artist'=>array(),
														 'agency'=>array(),
														 'book'=>array());
		foreach ( $this->ad_blocks as $key => &$val) {
			$data = SQLProvider::ExecuteQuery("
			  select 
				  ad.title, 
					ad.ad_text, 
					if(ad.user_id > 0,
					  case when ad.user_type = 'book' then 
						  CONCAT('/',ad.user_type,'/details',ad.user_id) 
						else 
						  CONCAT('/',ad.user_type,'/',COALESCE(contractor.title_url,area.title_url,artist.title_url, agency.title_url))
						end, 
					ad.other_link) link
				from tbl__ad_blocks ad
				left join tbl__contractor_doc contractor on ad.user_type = 'contractor' and contractor.tbl_obj_id = ad.user_id
				left join tbl__area_doc area on ad.user_type = 'area' and area.tbl_obj_id = ad.user_id
				left join tbl__artist_doc artist on ad.user_type = 'artist' and artist.tbl_obj_id = ad.user_id
				left join tbl__agency_doc agency on ad.user_type = 'agency' and agency.tbl_obj_id = ad.user_id
				where date_end >= CURRENT_DATE
				  and user_type = '$key' limit 3"); 
			if (sizeof($data)>0) {
				$num = rand(0,min(2,sizeof($data)-1));
				$val=$data[$num];
			}
			else
			  unset($this->ad_blocks[$key]);
		}
		
		/*  рейтинг  */
    $rates = SQLProvider::ExecuteQuery(
      "select ul.to_resident_type resident_type, 
              ul.to_resident_id tbl_obj_id, 
              r.title, 
							r.title_url,
              count(ul.tbl_obj_id) marked
       from tbl__userlike ul
         join tbl__contractor_doc r on r.tbl_obj_id = ul.to_resident_id
			 where ul.to_resident_type = 'contractor'
			   and r.active = 1
       group by ul.to_resident_type, ul.to_resident_id, r.title
			 order by marked DESC,tbl_obj_id limit 5");
    foreach ($rates as $key => &$r)
      $r['num'] = $key+1;
    $this->GetControl("rateContractors")->dataSource = $rates;
    $rates = SQLProvider::ExecuteQuery(
      "select ul.to_resident_type resident_type,
              ul.to_resident_id tbl_obj_id,
              r.title,
							r.title_url,
              count(ul.tbl_obj_id) marked
       from tbl__userlike ul
         join tbl__area_doc r on r.tbl_obj_id = ul.to_resident_id
			 where ul.to_resident_type = 'area'
			   and r.active = 1
       group by ul.to_resident_type, ul.to_resident_id, r.title
			 order by marked DESC,tbl_obj_id limit 5");
    foreach ($rates as $key => &$r)
      $r['num'] = $key+1;
    $this->GetControl("rateAreas")->dataSource = $rates;

    $rates = SQLProvider::ExecuteQuery(
      "select ul.to_resident_type resident_type,
              ul.to_resident_id tbl_obj_id,
              r.title,
							r.title_url,
              count(ul.tbl_obj_id) marked
       from tbl__userlike ul
         join tbl__artist_doc r on r.tbl_obj_id = ul.to_resident_id
			 where ul.to_resident_type = 'artist'
			   and r.active = 1
       group by ul.to_resident_type, ul.to_resident_id, r.title
			 order by marked DESC,tbl_obj_id limit 5");
    foreach ($rates as $key => &$r)
      $r['num'] = $key+1;
    $this->GetControl("rateArtists")->dataSource = $rates;

    $rates = SQLProvider::ExecuteQuery(
      "select ul.to_resident_type resident_type,
              ul.to_resident_id tbl_obj_id,
              r.title,
							r.title_url,
              count(ul.tbl_obj_id) marked
       from tbl__userlike ul
         join tbl__agency_doc r on r.tbl_obj_id = ul.to_resident_id
			 where ul.to_resident_type = 'agency'
			   and r.active = 1
       group by ul.to_resident_type, ul.to_resident_id, r.title
			 order by marked DESC,tbl_obj_id limit 5");
    foreach ($rates as $key => &$r)
      $r['num'] = $key+1;
    $this->GetControl("rateAgencies")->dataSource = $rates;
    /*  новые  */
		$this->GetControl("addedAreas")->dataSource = SQLProvider::ExecuteQuery(
            "select `tbl_obj_id`,title,title_url,'area' `t`, 
							  case 
								  when TIMESTAMPDIFF(HOUR,registration_date,CURRENT_TIMESTAMP)<24 
									  then DATE_FORMAT(registration_date,'%H:%i')
								  when TIMESTAMPDIFF(YEAR,registration_date,CURRENT_TIMESTAMP)<1
								    then DATE_FORMAT(registration_date,'%d.%m')
								end added_date
						from `tbl__area_doc`
						where `active`=1
						order by `registration_date` DESC limit 0,7");

		$this->GetControl("addedContractors")->dataSource = SQLProvider::ExecuteQuery("
							select `tbl_obj_id`,title,title_url,'contractor' `t`,
							  case 
								  when TIMESTAMPDIFF(HOUR,registration_date,CURRENT_TIMESTAMP)<24 
									  then DATE_FORMAT(registration_date,'%H:%i')
								  when TIMESTAMPDIFF(YEAR,registration_date,CURRENT_TIMESTAMP)<1
								    then DATE_FORMAT(registration_date,'%d.%m')
								end added_date
						  from `tbl__contractor_doc`
							where `active`=1
							order by `registration_date` DESC limit 0,7
							
							
							");

		$this->GetControl("addedArtists")->dataSource = SQLProvider::ExecuteQuery(
            "select `tbl_obj_id`,title,title_url,'artist' `t`,
							  case 
								  when TIMESTAMPDIFF(HOUR,registration_date,CURRENT_TIMESTAMP)<24 
									  then DATE_FORMAT(registration_date,'%H:%i')
								  when TIMESTAMPDIFF(YEAR,registration_date,CURRENT_TIMESTAMP)<1
								    then DATE_FORMAT(registration_date,'%d.%m')
								end added_date
						from `tbl__artist_doc`
						where `active`=1
						order by `registration_date` DESC limit 0,7");
		
		$this->GetControl("addedAgencies")->dataSource = SQLProvider::ExecuteQuery(
            "select `tbl_obj_id`,title,title_url,'agency' `t`,
							  case 
								  when TIMESTAMPDIFF(HOUR,registration_date,CURRENT_TIMESTAMP)<24 
									  then DATE_FORMAT(registration_date,'%H:%i')
								  when TIMESTAMPDIFF(YEAR,registration_date,CURRENT_TIMESTAMP)<1
								    then DATE_FORMAT(registration_date,'%d.%m')
								end added_date
						from `tbl__agency_doc`
						where `active`=1
						order by `registration_date` DESC limit 0,7");
		
		/*  новости  */
		$res_news = SQLProvider::ExecuteQuery(
            "select rn.*, DATE_FORMAT(date,'%d.%m.%y') as `strdate`
						 from `tbl__resident_news` rn
												where rn.`active`=1
												order by rn.`date` DESC limit 0,8
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
			case 'contractor': $res_news[$key]['sub'] = 'Новость подрядчика'; break;
			case 'agency': $res_news[$key]['sub'] = 'Новость агентства'; break;
			}
		}
		$this->GetControl("newAreas")->dataSource = $res_news;

		$this->GetControl("newContractors")->dataSource = SQLProvider::ExecuteQuery(
            "select rn.*, DATE_FORMAT(date,'%d.%m.%y') as `strdate`, res.title_url
						 from `tbl__resident_news` rn
						 left join tbl__contractor_doc res on res.tbl_obj_id = rn.resident_id
												where rn.`active`=1 and rn.`resident_type`='contractor' 
												order by rn.`date` DESC limit 0,3
												");

    $this->GetControl("newArtists")->dataSource = SQLProvider::ExecuteQuery(
            "select rn.*, DATE_FORMAT(date,'%d.%m.%y') as `strdate`, res.title_url
						 from `tbl__resident_news` rn
						 left join tbl__artist_doc res on res.tbl_obj_id = rn.resident_id
												where rn.`active`=1 and rn.`resident_type`='artist' 
												order by rn.`date` DESC limit 0,3
												");
		$this->GetControl("newAgencies")->dataSource = SQLProvider::ExecuteQuery(
            "select rn.*, DATE_FORMAT(date,'%d.%m.%y') as `strdate`, res.title_url
						 from `tbl__resident_news` rn
						 left join tbl__agency_doc res on res.tbl_obj_id = rn.resident_id
												where rn.`active`=1 and rn.`resident_type`='agency' 
												order by rn.`date` DESC limit 0,3
												");
		
		$ev_teka = SQLProvider::ExecuteQuery("
				select 
					tbl_obj_id, title,title_url, annotation, text, registration_date, is_new, logo_image, link,
					DATE_FORMAT(registration_date,'%d.%m.%y') formatted_date, topics
				from (
					select 
						tbl_obj_id, title,title_url, annotation, text, registration_date, is_new, logo_image, 'eventtv' link,
						(select GROUP_CONCAT(pt.title SEPARATOR ', ')
									from tbl__eventtv2topic p2t 
									join tbl__eventtv_topics pt on p2t.parent_id = pt.tbl_obj_id
									where p2t.child_id = pd.tbl_obj_id
									order by pt.order_num) topics
					from tbl__eventtv_doc pd where active = 1 
					union all
					select 
						tbl_obj_id, title,title_url, annotation, text, registration_date, is_new, logo_image, 'book' link,
						(select GROUP_CONCAT(pt.title SEPARATOR ', ')
									from tbl__public2topic p2t 
									join tbl__public_topics pt on p2t.parent_id = pt.tbl_obj_id
									where p2t.child_id = pd.tbl_obj_id
									order by pt.order_num) topics
					from tbl__public_doc pd where active = 1
					) t
				order by registration_date desc limit 8");
    $ev_cnt = sizeof($ev_teka);
		$ev_cnt_per_col = floor($ev_cnt/2);
    $ev_teka1 = array();
    $ev_teka2 = array();
    for ($i=0;$i<$ev_cnt;$i++)
		{
			$ev_teka[$i]["annotation"] = CutString($ev_teka[$i]["annotation"], 200);
      if ($i < $ev_cnt_per_col)
        array_push($ev_teka1, $ev_teka[$i]);
      else
        array_push($ev_teka2, $ev_teka[$i]);
		}
    
		$this->GetControl("event_news1")->dataSource = $ev_teka1;
    $this->GetControl("event_news2")->dataSource = $ev_teka2;
		
		$ev_news = SQLProvider::ExecuteQuery(
            "select `date`, DATE_FORMAT(date,'%d.%m.%y') as `strdate` ,`title`,`creation_date`, 'news' as t, `active_day`,`display_order`,`tbl_obj_id`, s_image, annotation  from `tbl__news` where `active`=1 and `fp`=1
												union all
												select `date`, DATE_FORMAT(date,'%d.%m.%y') as `strdate`,`creation_date` ,`title`, 'events', `active_day`,`display_order`,`tbl_obj_id`, s_image, annotation from `tbl__events` where `active`=1 and `fp`=1
												order by `creation_date` DESC,`active_day` DESC,`date` DESC limit 8");
		for ($i=0;$i<sizeof($ev_news);$i++)
		{
			$ev_news[$i]["annotation"] = CutString($ev_news[$i]["annotation"], 200);
		}
		$this->GetControl("news")->dataSource = $ev_news;

		
		//$sql  = '(SELECT t.title, t.date, t.date_end, t.link '.
		//			'FROM `tbl__event_calendar` t WHERE t.active = 1) '.
		//		' UNION '.
		//		'(SELECT n.title, DATE_FORMAT(n.date, "%Y-%m-%d") date, DATE_FORMAT(n.date, "%Y-%m-%d") date_end, '.
		//			'CONCAT("/news/details", n.tbl_obj_id) link '.
		//			'FROM `tbl__news` n WHERE n.active = 1 AND n.in_calendar = 1) '.
		//		'ORDER BY date';
		//
		//$cal_arr = SQLProvider::ExecuteQuery($sql);
		//foreach($cal_arr as $i => $c) {
		//	if($c['date_end']!=null && $c['date']!=$c['date_end']) {
		//		$ds = $c['date'];
		//		$de =$c['date_end'];
		//		$dn = date('Y-m-d', strtotime('+1 days', strtotime($ds)));
		//		$diff = (strtotime($de)-strtotime($ds))/(3600*24);
		//		//$cal_arr[$i]['link'] = $diff;
		//		for($k=0;$k<$diff;$k++) {
		//			$cal_arr[] = array('date' => $dn, 'title' => $c['title'], 'link' => $c['link']);
		//			$dn = date('Y-m-d', strtotime('+1 days', strtotime($dn)));
		//		}
		//		/*while ($dn!=$de) {
		//			//$cal_arr[$ci] = array('date' => $dn, 'title' => $c['title'], 'link' => $c['link']);
		//			/*$cal_arr[$ci]['date'] = $dn;
		//			$cal_arr[$ci]['title'] = $c['title'];
		//			$cal_arr[$ci]['link'] = $c['link'];
		//			$ci++;
		//		}*/
		//	}
		//}
		//$near_ev = SQLProvider::ExecuteQuery("select t.*,DATE_FORMAT(t.date,'%d') day, DATE_FORMAT(t.date,'%m') month,WEEKDAY(t.date) week from `tbl__event_calendar` t
		//										where `date` >= CURDATE() and `active`=1
		//										order by `date`, `date_end` limit 1");
		//$Month_r = array(
		//"01" => "января",
		//"02" => "февраля",
		//"03" => "марта",
		//"04" => "апреля",
		//"05" => "мая",
		//"06" => "июня",
		//"07" => "июля",
		//"08" => "августа",
		//"09" => "сентября",
		//"10" => "октября",
		//"11" => "ноября",
		//"12" => "декабря");
		//$Week_r = array(
		//'0' => 'ПН',
		//'1' => 'ВТ',
		//'2' => 'СР',
		//'3' => 'ЧТ',
		//'4' => 'ПТ',
		//'5' => 'СБ',
		//'6' => 'ВС',
		//);
		//if (sizeof($near_ev) && $near_ev[0]['link']) {
		//	$calendar['near']='<b>Ближайшее: '.$near_ev[0]['day'].' '.$Month_r[$near_ev[0]['month']].' ('.$Week_r[$near_ev[0]['week']].')</b><br><a href="'.$near_ev[0]["link"].'"  target="_blank" style="color: black;">'.$near_ev[0]["title"].'</a><br /><br />';
		//}
		//function ToUTF($n) {
		//	return array_map('ToUTF2', $n);
		//}
		//function ToUTF2($n) {
		//	return iconv("windows-1251","UTF-8", $n);
		//}
		//$calendar['arr'] = json_encode(array_map('ToUTF', $cal_arr));
		//$this->GetControl("eventcalendar")->dataSource = $calendar;
		
		$lastBookItems = SQLProvider::ExecuteQuery("select * from tbl__public_doc
														where `active`=1
														order by `registration_date` desc limit 15");
		$last_book = $this->GetControl("last_book");
		for ($i=0;$i<sizeof($lastBookItems);$i++)
		{
			$lastBookItems[$i]["title"] = CutString($lastBookItems[$i]["title"], 200);
		}
		$last_book->dataSource = $lastBookItems;
				
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

		$newRegs = SQLProvider::ExecuteQuery("select * from `vw__all_users` vm
							where vm.`active`=1 and vm.`login_type`<>'user'
							order by vm.`registration_date` DESC limit 0,12");
		$newRegistered = $this->GetControl("newRegistered");
		$newRegistered->dataSource = $newRegs;
		
		
		$newRegs2 = SQLProvider::ExecuteQuery("select * from `vw__all_users` vm
							where vm.`active`=1 and vm.`login_type`<>'user'
							order by vm.`registration_date` DESC limit 12,12");
		$newRegistered2 = $this->GetControl("newRegistered2");
		$newRegistered2->dataSource = $newRegs2;
		
		
		$newRegs3 = SQLProvider::ExecuteQuery("select * from `vw__all_users` vm
							where vm.`active`=1 and vm.`login_type`<>'user'
							order by vm.`registration_date` DESC limit 12,6");
		$newRegistered3 = $this->GetControl("newRegistered3");
		$newRegistered3->dataSource = $newRegs3;
		
		//magazine middle
		$magazine = SQLProvider::ExecuteQuery("select *, if(is_sales_active=1,'Заказать доставку','Узнать подробнее') dost_title,
				                               if(is_sales_active=1,'delivery','details') dost_link,
				                               if(is_sales_active=1,'FF0066','000000') dost_color
				                               from tbl__magazines where active=1 order by order_num,tbl_obj_id desc limit 1");
		if (sizeof($magazine)>0) $this->mag = $magazine[0];

	$mainMenu = $this->GetControl("menu");
	switch(rand(1,8)) {
	case 1:
    /*$mainMenu->dataSource["museum"] =
      array("link"=>"http://15kop.ru/",
            "imgname"=>"museum",
            "title"=>"",
            "target"=>'target="_blank"');
            */
    $mainMenu->dataSource["museum"] =
      array("link"=>"http://www.valet-parking.ru/",
            "imgname"=>"valet",
            "title"=>"",
            "target"=>'target="_blank"');
            
	break;
	case 2:
	$mainMenu->dataSource["polymedia"] =
      array("link"=>"http://www.polymedia.ru/",
            "imgname"=>"polymedia",
            "title"=>"",
            "target"=>'target="_blank"');
	break;
	case 3:
    $mainMenu->dataSource["kinodoktor"] =
      array("link"=>"http://www.kinodoctor.ru/",
            "imgname"=>"kinodoktor",
            "title"=>"",
            "target"=>'target="_blank"');
	break;
	case 4:
	$mainMenu->dataSource["axiom"] = 
	   array("link"=>"http://www.aksioma.me/",
	   "imgname"=>"axiom",
	   "title"=>"",
	   "target"=>"target='_blank'");
	break;
	case 5:
	$mainMenu->dataSource["energy"] = 
	   array("link"=>"http://energy-pro.org/",
	   "imgname"=>"energy",
	   "title"=>"",
	   "target"=>"target='_blank'");
	break;
	case 6:
	$mainMenu->dataSource["spin"] = 
	   array("link"=>"http://www.spinmusic.ru/",
	   "imgname"=>"spin",
	   "title"=>"",
	   "target"=>"target='_blank'");
	break;
	case 7:
	$mainMenu->dataSource["great"] = 
	   array("link"=>"http://greatgroup.ru/","imgname"=>"creative","title"=>"","target"=>"target='_blank'");
	break;
	case 8:
	$mainMenu->dataSource["midas"] = 
	   array("link"=>"http://midas.ru/?id=144","imgname"=>"midas","title"=>"","target"=>"target='_blank'");
	break;
	}
    //EVENT TV
    $etv_res = SQLProvider::ExecuteQuery("select video_id,doc_id from tbl__eventtv_main");
    $vimeo_video_id = '';
    $doc_id = '';
    if (sizeof($etv_res)) {
      $vimeo_video_id = $etv_res[0]['video_id'];
      $doc_id = $etv_res[0]['doc_id'];
    } 
    if ($doc_id > 0) {
      $this->etv_comment = '<a href="/eventtv/details'.$doc_id.'/" class="common">Написать комментарий</a>';
    }
    else
      $this->etv_comment = "&nbsp;";
    
    //player vimeo
    $this->GetControl("eventtv_video")->dataSource = array("video_id"=>$vimeo_video_id);
    //number of playes video from vimeo       
    try {       
      $curl = curl_init("http://vimeo.com/$vimeo_video_id");
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_TIMEOUT, 30);
      $html_data = curl_exec($curl);
      curl_close($curl);         
      if ($html_data) {
        $html_data = StripBadUTF8($html_data);
        /*$video_info = simplexml_load_string($xml_data);      
        $this->etv_count = $video_info->video->stats_number_of_plays;*/
				$i = strpos($html_data,'clip_stats');
				$html_data = substr($html_data,$i,strpos($html_data,'</table>')-$i);
				$html_data = substr($html_data,strpos($html_data,'<td>Totals</td>')+15);
				//$html_data = substr($html_data,0,strpos($html_data,'</td>'));
				if (preg_match('/(?<=<td>)([0-9,]+)(?=<\/td>)/',$html_data,$finded)) {
				  $this->etv_count = preg_replace('/\,/','',$finded[0]);
				}
				else
				  $this->etv_count = "";
      }
    } catch (Exception $e) {
      $this->etv_count = "";
    }
    //links   
		$this->GetControl("eventtv_links_contr")->dataSource = array(
		  array("link"=>"/contractor/foxbrand","class"=>"contractor","title"=>"FoxBrand","next"=>"")
		);
		$this->GetControl("eventtv_links_artist")->dataSource = array(
		  array("link"=>"/artist/kot_i_pes","class"=>"artist","title"=>"Дуэт Кот и Пес","next"=>","),
		  array("link"=>"/artist/tarasov_nikita","class"=>"artist","title"=>"Тарасов Никита","next"=>"")
		);
    //rubrics        
    $rubs1 = SQLProvider::ExecuteQuery("
      select
        title,
        CONCAT('/eventtv/?topic=',tbl_obj_id) link,
        color
      from tbl__eventtv_topics
			where group_num = 1
      order by order_num
      ");    
    $this->GetControl("eventtv_rubrics1")->dataSource = $rubs1;
    $rubs2 = SQLProvider::ExecuteQuery("
      select
        title,
        CONCAT('/eventtv/?topic=',tbl_obj_id) link,
        color
      from tbl__eventtv_topics
			where group_num = 2
      order by order_num
      ");    
    $this->GetControl("eventtv_rubrics2")->dataSource = $rubs2;
    $this->GetControl("eventtv_rubrics_small")->dataSource = array_merge($rubs1,$rubs2);
    $eventtv_photos = SQLProvider::ExecuteQuery("
      select file_name
      from tbl__eventtv_photos
      order by rand() limit 1");
    $eventtv_photo = $this->GetControl("eventtv_photo");
    if (sizeof($eventtv_photos)) {
      $eventtv_photo->dataSource = array("file_name"=>"/files/".$eventtv_photos[0]["file_name"]);
      $this->etv_photo = "/files/".$eventtv_photos[0]["file_name"];
    }
    else {
      $eventtv_photo->dataSource = array("file_name"=>"");
      $this->etv_photo = "";
    }        
    if (isset($_SESSION['etv_type']) && $_SESSION['etv_type'] == 1) {
			$this->etv_small_vis = "";
			$this->etv_big_vis = "display: none";			
		}
			//die('!');
			// сам календарь
			$sql  = '(SELECT t.title, t.date, t.date_end, t.link '.
						'FROM `tbl__event_calendar` t WHERE t.active = 1) '.
					' UNION '.
					'(SELECT n.title, DATE_FORMAT(n.date, "%Y-%m-%d") date, DATE_FORMAT(n.date, "%Y-%m-%d") date_end, '.
						'CONCAT("/news/details", n.tbl_obj_id) link '.
						'FROM `tbl__news` n WHERE n.active = 1 AND n.in_calendar = 1) '.
					'ORDER BY date';
			
			$cal_arr = SQLProvider::ExecuteQuery($sql);
			foreach($cal_arr as $i => $c) {
				if($c['date_end']!=null && $c['date']!=$c['date_end']) {
					$ds = $c['date'];
					$de =$c['date_end'];
					$dn = date('Y-m-d', strtotime('+1 days', strtotime($ds)));
					$diff = (strtotime($de)-strtotime($ds))/(3600*24);
					for($k=0;$k<$diff;$k++) {
						$cal_arr[] = array('date' => $dn, 'title' => $c['title'], 'link' => $c['link']);
						$dn = date('Y-m-d', strtotime('+1 days', strtotime($dn)));
					}
				}
			}
			$near_ev = SQLProvider::ExecuteQuery("select t.*,DATE_FORMAT(t.date,'%d') day, DATE_FORMAT(t.date,'%m') month,WEEKDAY(t.date) week from `tbl__event_calendar` t
													where `date` >= CURDATE() and `active`=1
													order by `date`, `date_end` limit 1");
			$Month_r = array(
				"01" => "января",
				"02" => "февраля",
				"03" => "марта",
				"04" => "апреля",
				"05" => "мая",
				"06" => "июня",
				"07" => "июля",
				"08" => "августа",
				"09" => "сентября",
				"10" => "октября",
				"11" => "ноября",
				"12" => "декабря"
			);
			$Week_r = array(
				'0' => 'ПН',
				'1' => 'ВТ',
				'2' => 'СР',
				'3' => 'ЧТ',
				'4' => 'ПТ',
				'5' => 'СБ',
				'6' => 'ВС',
			);
			if (sizeof($near_ev) && $near_ev[0]['link']) {
				$calendar['near']='<b>Ближайшее: '.$near_ev[0]['day'].' '.$Month_r[$near_ev[0]['month']].' ('.$Week_r[$near_ev[0]['week']].')</b><br><a href="'.$near_ev[0]["link"].'"  target="_blank" style="color: black;">'.$near_ev[0]["title"].'</a><br /><br />';
			}
			function ToUTF($n) {
				return array_map('ToUTF2', $n);
			}
			function ToUTF2($n) {
				return iconv("windows-1251","UTF-8", $n);
			}
			$calendar['arr'] = json_encode(array_map('ToUTF', $cal_arr));
			
			
			// LIST 5 last news
			$sql2  = '(SELECT t.title, t.date, t.date_end, t.link '.
				'FROM `tbl__event_calendar` t WHERE t.active = 1) '.
				' UNION '.
				'(SELECT n.title, DATE_FORMAT(n.date, "%Y-%m-%d") date, DATE_FORMAT(n.date, "%Y-%m-%d") date_end, '.
				'CONCAT("/news/details", n.tbl_obj_id) link '.
				'FROM `tbl__news` n WHERE n.active = 1 AND n.in_calendar = 1) '.
				'WHERE date > NOW() '.
				'ORDER BY date ASC LIMIT 5';
			$cal_arr2 = SQLProvider::ExecuteQuery($sql2);
			
			
			$act_list = 'noact';
      foreach($cal_arr2 as $i => $c) {
        if($c['date'] > Date('Y-m-d')) { $act_list = ''; break; }
      }
			
				$calendar['last_list'] = '<div class="sub-title-block">
				  <div class="calendar_tab">
          <span class="calendar_ico active" id="calendar_table"></span>
          <span class="calendar_ico '.$act_list.'" id="calendar_list"></span>
          </div>
          <a href="/event_calendar/"" class="sub-title widget">EVENT Календарь</a> 

        </div>';
			
			$calendar['last_list'] .= '<div class="calendar-list">';
			
      foreach($cal_arr2 as $i => $c) {
        if($c['date'] > Date('Y-m-d')) {
          $calendar['last_list'] .= '<div class="last_calendar">';
          $calendar['last_list'] .= '<div class="date">'.Date_Ru($c['date']).'</div>';
          $calendar['last_list'] .= '<div class="name"><a href="'.$c['link'].'">'.$c['title'].'</a></div>';
          $calendar['last_list'] .= '</div>';
        }
      }       
      
      $calendar['last_list'] .= '</div>';
			
			
			$this->GetControl("eventcalendar")->dataSource = $calendar;	
	
	
	
	    /* BALTICIT MENU IN MAIN */
	     //AREA
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

            $groups[$key]["link"] = "/area/".$value['title_url'] . CURLHandler::BuildQueryParams($cpars);

        }
        $groupList = $this->GetControl("typeList2");
        $groupList->dataSource = $groups;
        
        //CONTRACTOR
        $activities = SQLProvider::ExecuteQueryIndexed("select *, tbl_obj_id as child_id from `tbl__activity_type` order by priority desc", "child_id");
        $titlefilter = array();
        $titlefilterLinks = array();
        foreach ($activities as &$value) {
            $cpars = array();

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
        $actList = $this->GetControl("typeList1");
        $actList->dataSource = $activities;
        
        
        
        //ARTIST
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
            $cpars = array();
            $groups[$key]["link"] = "/artist/".$value['title_url']. CURLHandler::BuildQueryParams($cpars);
        }
        $groupList = $this->GetControl("typeList3");
        $groupList->dataSource = $groups;
        
        
        //AGENCY
        @$groups = SQLProvider::ExecuteQuery("select * from `tbl__agency_type` ORDER by priority desc");
        foreach ($groups as $key => $value) {
            $cpars = array();
            $groups[$key]["link"] = '/agency/'.$value['title_url'] . CURLHandler::BuildQueryParams($cpars);
        }
        @$groupList = $this->GetControl("typeList4");
        @$groupList->dataSource = $groups;
        
        /* END BALTICIT MENU IN MAIN */
		
	
	}

}
?>
