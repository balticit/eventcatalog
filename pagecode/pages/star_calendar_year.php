<?php
	class star_calendar_year_php extends CPageCodeHandler 
	{
		public function star_calendar_year_php()
		{
			$this->CPageCodeHandler();
		}
		
		public function PreRender() {
			
			$year = GP("y",date("Y",date("Y")));
			
			$calendar = array();
			$calendar["year"] = $year;
			$calendar["next_year"] = (int)$year+1;
			$calendar["prev_year"] = (int)$year-1;
			

			$months = array();
			$months[1] = "ﬂÕ¬¿–‹";
			$months[2] = "‘≈¬–¿À‹";
			$months[3] = "Ã¿–“";
			$months[4] = "¿œ–≈À‹";
			$months[5] = "Ã¿…";
			$months[6] = "»ﬁÕ‹";
			$months[7] = "»ﬁÀ‹";
			$months[8] = "¿¬√”—“";
			$months[9] = "—≈Õ“ﬂ¡–‹";
			$months[10] = "Œ “ﬂ¡–‹";
			$months[11] = "ÕŒﬂ¡–‹";
			$months[12] = "ƒ≈ ¿¡–‹";
			
			$day = strtotime("$year-1-1");
			
			$calendar["calendar"] = "";
			
			while (date("Y",$day)==$year) {
				
				if (date("d",$day)=="01") {
					$calendar["calendar"] .= "<tr><td".(date("m",$day)==date("m")?" style=\"color: magenta;\"":"").">".$months[(int)date("m",$day)]."</td><td>";
				}
				
				$k =  SQLProvider::ExecuteQuery("select COUNT(*) as count from `vw__star_calendar` where DATE_FORMAT(date,'%y-%m-%d')='".date("y-m-d",$day)."'");
				if ($k[0]["count"]==0)
					$calendar["calendar"] .= date("d",$day)."&nbsp;";
				else
					$calendar["calendar"] .= "<a href=/star_calendar/day/d/$day class=day_link>".date("d",$day)."</a>&nbsp;";
				
				$day += 86400;
				
				if (date("d",$day)=="01")
					$calendar["calendar"] .= "</td></tr>";
			}
			
			$calendar["month"] = date("m");
			
			$year_calendar = $this->GetControl("year_calendar");
			$year_calendar->dataSource = $calendar;
			
		}
		
	} 
?>