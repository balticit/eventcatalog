<?php
	class star_calendar_php extends CPageCodeHandler 
	{
		public function star_calendar_php()
		{
			$this->CPageCodeHandler();
		}
		
		public function PreRender() {
			
			$year = GP("y",date("Y"));
			$month = GP("m",date("m"));
			$today = strtotime(date("Y-m-d"));
			
			$month_title = "";
			switch ($month) {
				case "01" : 
					$month_title = "ﬂÕ¬¿–‹";
				break;
				case "02" : 
					$month_title = "‘≈¬–¿À‹";
				break;
				case "03" : 
					$month_title = "Ã¿–“";
				break;
				case "04" : 
					$month_title = "¿œ–≈À‹";
				break;
				case "05" : 
					$month_title = "Ã¿…";
				break;
				case "06" : 
					$month_title = "»ﬁÕ‹";
				break;
				case "07" : 
					$month_title = "»ﬁÀ‹";
				break;
				case "08" : 
					$month_title = "¿¬√”—“";
				break;
				case "09" : 
					$month_title = "—≈Õ“ﬂ¡–‹";
				break;
				case "10" : 
					$month_title = "Œ “ﬂ¡–‹";
				break;
				case "11" : 
					$month_title = "ÕŒﬂ¡–‹";
				break;
				case "12" : 
					$month_title = "ƒ≈ ¿¡–‹";
				break;

			}
			
			$calendar = array();
			$calendar["year"] = $year;
			$calendar["month_title"] = $month_title;
			$calendar["month"] = $month;
			
			$prev_month = "";
			if ($month==1) {
				$prev_month = "/star_calendar/y/".((int)$year-1)."/m/12";
			}
			else 
				$prev_month = "/star_calendar/y/".$year."/m/".((int)$month-1);
			$calendar["prev_month"] = $prev_month;
			
			$next_month = "";
			if ($month==12) {
				$next_month = "/star_calendar/y/".((int)$year+1)."/m/1";
			}
			else 
				$next_month = "/star_calendar/y/".$year."/m/".((int)$month+1);
			$calendar["next_month"] = $next_month;			
			
			$firstday = strtotime("$year-$month-1");
			
			while (date("w",$firstday)!="1") {
				$firstday -= 86400;
			}
			$lastday = strtotime(($month<12?($year):($year+1))."-".($month<12?($month+1):("1"))."-1")-86400;

			while (date("w",$lastday)!='0') {
				$lastday += 86400;
			}

			$day = $firstday;
			
			$calendar["calendar"] = "";
			while ($day<=$lastday) {
				if (date("w",$day)=="1")
					$calendar["calendar"] .= "<tr>";
					

				if (date("m",$day)!=$month)
					$calendar["calendar"] .= "<td class=calendar_not_active><div style=\"text-align: right; font-size: 10px;\">".date("d",$day)."</div>";
				else {
					if ($day==$today)
						$calendar["calendar"] .= "<td class=calendar_today id=\"d$day\" onclick=\"location.href='/star_calendar/day/d/$day';\" onmouseover=\"document.getElementById('d$day').style.border='1px #ff0000 solid';\" onmouseout=\"document.getElementById('d$day').style.border='1px #999999 solid';\">";
					else
						$calendar["calendar"] .= "<td class=calendar_active id=\"d$day\" onclick=\"location.href='/star_calendar/day/d/$day';\" onmouseover=\"document.getElementById('d$day').style.border='1px #ff0000 solid';\" onmouseout=\"document.getElementById('d$day').style.border='1px #999999 solid';\">";

					$calendar["calendar"] .= "<div style=\"text-align: right; font-size: 10px;\">".date("d",$day)."</div>";
					$calendar["calendar"] .= "<div style=\"height: 50px;\">";
					$k = SQLProvider::ExecuteQuery("select COUNT(*) as count from `vw__star_calendar` where DATE_FORMAT(date,'%y-%m-%d')='".date("y-m-d",$day)."'");
//					echo "select COUNT(*) as count from `vw__star_calendar` where DATE_FORMAT(date,'%y-%m-%d')='".date("y-m-d",$day)."'<br>";
					if (($k[0]["count"]!=0)) {
						$gastr = SQLProvider::ExecuteQuery("select title,city, is_resident, artist_id, date from `vw__star_calendar` where DATE_FORMAT(date,'%y-%m-%d')='".date("y-m-d",$day)."' order by tbl_obj_id desc limit 0,1");
						if ($gastr[0]["is_resident"] == 1) 
						$calendar["calendar"] .= "<a href=/artist/details/id/".$gastr[0]["artist_id"]." style=\"font-size: 10px; color: red;\">".$gastr[0]["title"]."</a><br><span style=\"font-size: 10px;\">".$gastr[0]["city"]."</span>";
						else
							$calendar["calendar"] .= "<span style=\"font-size: 10px; color: red;\">".$gastr[0]["title"]."</span><br><span style=\"font-size: 10px;\">".$gastr[0]["city"]."</span>";
						if ($k[0]['count']>1) {
							$calendar["calendar"] .= "<br><br><a href=/star_calendar/day/d/$day style=\"font-size: 10px; color: blue; text-decoration: none;\">œÓÍ‡Á‡Ú¸ ‚ÒÂ</a>";
						}
					}
					$calendar["calendar"] .= "</div>";
				}
				

				
				$calendar["calendar"] .= "</td>\n";
				if (date("w",$day)=="0")
					$calendar["calendar"] .= "</tr>\n";
				$day += 86400;
			}
			
	
			$month_calendar = $this->GetControl("month_calendar");
			$month_calendar->dataSource = $calendar;
			
		}
		
	} 
?>