<?php
    function getDayOfWeek($sourcedate) {
        switch (date("w",strtotime($sourcedate))) {
            case 0 : return "вс"; break;
            case 1 : return "пн"; break;
            case 2 : return "вт"; break;
            case 3 : return "ср"; break;
            case 4 : return "чт"; break;
            case 5 : return "пт"; break;
            case 6 : return "сб"; break;
            case 7 : return "вс"; break;
        }
    }
    
    function getMonth($month) {
        switch ($month) {
            case 1: $month = "январь"; break;
            case 2 : $month = "февраль"; break;
            case 3 : $month = "март"; break;
            case 4 : $month = "апрель"; break;
            case 5: $month = "май"; break;
            case 6 : $month = "июнь"; break;
            case 7 : $month = "июль"; break;
            case 8 : $month = "август"; break;
            case 9 : $month = "сентябрь"; break;
            case 10 : $month = "октябрь"; break;
            case 11 : $month = "ноябрь"; break;
            case 12 : $month = "декабрь"; break;
        }
        return strtoupper($month);
    }    

	class event_calendar_php extends CPageCodeHandler 
	{
		public $max_per_page = 10;
		public $prev = 0;
		
		public function event_calendar_php()
		{
			$this->CPageCodeHandler();
		}
		
		public function PreRender() {
			$av_rwParams = array("prev");
			CURLHandler::CheckRewriteParams($av_rwParams);  
			
			$this->prev = GP("prev",strtotime(date("Y-m-d")) );
			$prev = $this->prev;
			$sort=" DESC";
			if($prev==0){$sort="";}
			$sql = 	
				'(SELECT t.tbl_obj_id, t.date, t.title, t.type, t.link, t.place, t.date as sdate, '. 
					'YEAR(t.date) as gr_year, MONTH(t.date) as gr_month, '.
					'UNIX_TIMESTAMP(t.date) as unixdate, a.title_url '.
				'FROM `tbl__event_calendar` t '.
				'LEFT JOIN `tbl__area_doc` a ON a.tbl_obj_id = t.area_id '.
				'WHERE '.
					/* (($prev == 0) ? 
						'UNIX_TIMESTAMP(t.date) >= '.strtotime(date('Y-m-d')) :
						'UNIX_TIMESTAMP(t.date) <= '.$prev).' AND '. */
					't.active = 1) '.
				'UNION '.
				'(SELECT n.tbl_obj_id, n.date, n.title, n.type, CONCAT("/news/details", n.tbl_obj_id) link, n.place, n.date as sdate, '.
					'YEAR(n.date) gr_year, MONTH(n.date) gr_month, '.
					'UNIX_TIMESTAMP(n.date) as unixdate, "!!!" title_url '.
				'FROM `tbl__news` n '.
				'LEFT JOIN `tbl__area_doc` a ON a.tbl_obj_id = n.area_id '.
				'WHERE '.
				/*	(($prev == 0) ? 
						'UNIX_TIMESTAMP(n.date) >= '.strtotime(date('Y-m-d')) :
						'UNIX_TIMESTAMP(n.date) <= '.$prev).' AND '. */
					'n.active = 1 AND n.in_calendar = 1) '.
				'ORDER BY UNIX_TIMESTAMP(date)'.$sort;
			$news = SQLProvider::ExecuteQuery($sql);
            
            $result = array();
            $year = null;
            $year_res = null;
            $month = null;
            $month_res = null;
            
            foreach ($news as $item) {
                if ($item['gr_year'] != $year) {
                    $year = $item['gr_year'];
                    $year_res = array();
                    $month = null;
                    $month_res = null;
                }
                if (getMonth($item['gr_month']) != $month) {
                    $month = getMonth($item['gr_month']);
                    $month_res = array();
                }
                /* Add */
                if (isset($item['is_area'])&&$item["is_area"]) {
                    $item["place"] = "<a style=\"color: black;\" href=/area/".$item["title_url"].">".$item["place"]."</a>";
                }

				 $item["weekbegin"]=getDayOfWeek($item['date']);
                 $item['date'] = date('d.m',strtotime($item['date']));
                 if (isset($item['date_end'])&&(!is_null($item['date_end']))) {
                     $item["weekend"]=getDayOfWeek($item['date_end']);
                     $item['date_end'] = date('d.m',strtotime($item['date_end']));
                 }
                 else {
                     $item['date_end'] = $item['date'];
                 }
                $month_res[] = $item;
                $year_res[$month] = $month_res;
                $result[$year] = $year_res;
            }
            
            $newsList = $this->GetControl("newsList");
            $newsList->dataSource = $result;
            
            $arch["text"] = "";
            if ($prev==0) {
                $count = SQLProvider::ExecuteQuery("select count(*) as 'count' from `tbl__event_calendar` t where UNIX_TIMESTAMP(t.date)<=".strtotime(date("Y-m-d"))." and active=1");
			    if ($count[0]["count"]>0) {
				    $arch["text"] = "<a id=\"header_link\" href=\"/event_calendar/?prev=".strtotime(date("Y-m-d"))."\">Архив</a>";
			    }
            }
				
			$newsList = $this->GetControl("text");
			$newsList->dataSource = $arch;
			
			/*
			$mainMenu = $this->GetControl("menu");
            $mainMenu->dataSource["redevent"] =
              array("link"=>"http://redevent.ru/",
                    "imgname"=>"redevent",
                    "title"=>"",
                    "ads_class"=>"reklama",
                    "target"=>'target="_blank"');
			*/
			
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
			$this->GetControl("eventcalendar")->dataSource = $calendar;
			
			
		}
	}
?>
