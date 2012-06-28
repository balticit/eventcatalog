<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Event календарь - EVENT КАТАЛОГ</title>
	<?php CRenderer::RenderControl("metadata"); ?>
	<script src="/js/jquery-ui-1.8.17.custom.min.js" type="text/javascript"></script>
	<link href="/js/ui-lightness/jquery-ui-1.8.17.custom.css" type="text/css" rel="stylesheet">
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" style="width:100%; height:100%;">
<tr><td><?php CRenderer::RenderControl("topLine");?></td></tr>
<tr><!-- Заголовок-->
	<td style="padding-left: 30px; padding-right: 30px; padding-top: 16px;"><?php CRenderer::RenderControl("header"); ?></td>
</tr>
<tr><!--Меню-->
<td><?php CRenderer::RenderControl("menu"); ?></td>
</tr>
<tr>
	<td><?php CRenderer::RenderControl("submenu"); ?></td>
</tr>
<tr><td style="padding-left: 30px; padding-right: 30px; padding-top: 16px; height: 100%" valign="top">
	<table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign="top" width="100%">
			<div style="padding-left:15px;">
				<div class='calendar-block'>
					<?php CRenderer::RenderControl("eventcalendar"); ?>
				</div>
				<div style=" width: 700px; margin-left: 254px;">  
					<div class="recomendTitle eventoteka h3">EVENT КАЛЕНДАРЬ <?php CRenderer::RenderControl("text"); ?></div>
					
<br/>
<b>Тематический календарь предстоящих событий, которые будут интересны
профессионалам в области организации мероприятий. 
</b><br><br>
Если Вы знаете о важном событии, которое не попало в этот список, пожалуйста, напишите нам на <a href=mailto:development@eventcatalog.ru>development@eventcatalog.ru</a>  и мы с удовольствием разместим информацию о нём в Event Календаре. 
<br><br>

<table cellpadding="2" cellspacing="0" border="0" width="100%">  
<?php /*
<tr>
	<td colspan="4"><b><?php echo GP("y",date("Y")); ?></b></td>
</tr>
						CRenderer::RenderControl("newsList"); 
                        
                        */
						
	if(!function_exists('getDayOfWeek')) {			
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
	}
    
	if(!function_exists('getMonth')) {			
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
	}
						
  $app = CApplicationContext::GetInstance();
  $page = $app->page;
  $id = $page->pageId.CONTROL_SEMISECTOR."newsList";
  $ind = 0;
  if (isset($page->controls[$id]))
  {
      $newsData = $page->controls[$id]->dataSource;
      foreach ($newsData as $year=>$yeardata) {
          foreach ($yeardata as $month=>$monthdata) {
		  
			echo '<tr><td colspan="5">'.
					'<div><h4 class="detailsBlockTitle"><a name="description">'.$month.' '.$year.'</a></h4></div>';
				 '</td></tr>';
		  
              foreach ($monthdata as $item) {
                  echo '<tr style="height:35px">';
                  
                  //Самое первое событие
                  if ($ind==0&&$this->prev==0) {
		
                      $style=";color:red";
                  }
                  else {
                      $style="";
                  }
                  
                  echo '<td style="color:gray;" width="1%"><nobr>';
                  if($item['date'] == $item['date_end']) {
				  
					echo $item['date'].' ('.$item['weekbegin'].')';
					
					// printf('<td style="padding:2px 0 2px 10px'.$style.'">%s</td>', $item['date']);
					// echo '<td colspan=2>&nbsp;</td>';
					// printf('<td style="text-transform:uppercase;'.$style.'">%s</td>',$item['weekbegin']);
					// echo '<td colspan=2>&nbsp;</td>';
                  } else {
					
					echo $item['date'].'-'.$item['date_end'].' ('.$item['weekbegin'].'-'.$item['weekend'].')';
					
					// printf('<td style="padding:2px 2px 2px 10px'.$style.'">%s</td>', $item['date']);
					// echo '<td style="'.$style.'">-</td>';
					// printf('<td style="padding-right:10px'.$style.'">%s</td>',$item['date_end']);

					// printf('<td style="text-transform:uppercase;'.$style.'">%s</td>',$item['weekbegin']);
					// echo '<td style="'.$style.'">-</td>';
					// printf('<td style="text-transform:uppercase;'.$style.'">%s</td>',$item['weekend']);
                  }
                  echo '</nobr></td>';
                  echo '<td style="width:100%;padding-left:10px"><b><a style="color:black;'.$style.'" target="_blank" href="'.$item['link'].'">'.$item['title'].'</a></b></td>';
                  printf('<td nowrap style="'.$style.'">%s</td>',$item['type']);
                  printf('<td style="'.$style.'">%s</td>',$item['place']);
                  printf('<td nowrap style="'.$style.'"><a style="color:gray;'.$style.'" target="_blank" href="%s">подробнее</a></td>',$item['link']);
                  echo '</tr>';
                  $ind++;
              }
          }
      }
  }
?>          
</table>

<br><br>


</div>

		</div>	
				
		
		</td>
	</tr>
	
	
	</table>
	
</td></tr>
<tr><td class="partner_ban"><?php CRenderer::RenderControl("bottomBanners"); ?></td></tr>
<tr><td class="foot"><?php CRenderer::RenderControl("footer"); ?></td></tr>
</table>
<?php CRenderer::RenderControl("googleanalytics"); ?>
</body>
</html>
