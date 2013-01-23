<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Event ��������� - EVENT �������</title>
	<?php CRenderer::RenderControl("metadata"); ?>
	<script src="/js/jquery-ui-1.8.17.custom.min.js" type="text/javascript"></script>
	<link href="/js/ui-lightness/jquery-ui-1.8.17.custom.css" type="text/css" rel="stylesheet">
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" style="width:100%; height:100%;">
<tr><td><?php CRenderer::RenderControl("topLine");?></td></tr>
<tr><!-- ���������-->
	<td style="padding-left: 30px; padding-right: 30px; padding-top: 16px;"><?php CRenderer::RenderControl("header"); ?></td>
</tr>
<tr><!--����-->
<td><?php CRenderer::RenderControl("menu"); ?>
<?php CRenderer::RenderControl("submenu"); ?>

</td>
</tr>
<tr><td style="padding-left: 30px; padding-right: 30px; padding-top: 16px; height: 100%" valign="top">
	<table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign="top" width="100%">
			<div style="padding-left:15px;">
			<?php  /*
				<div class='calendar-block'>
					<?php CRenderer::RenderControl("eventcalendar"); ?>
				</div>
			*/ ?>
				<div>  
					<div class="recomendTitle eventoteka h3">EVENT ��������� <?php  CRenderer::RenderControl("text");  ?></div>
					
<br />
<b>������������ ��������� ����������� �������, ������� ����� ���������
�������������� � ������� ����������� �����������. 
</b><br><br>
���� �� ������ � ������ �������, ������� �� ������ � ���� ������, ����������, �������� ��� �� <a href=mailto:development@eventcatalog.ru>development@eventcatalog.ru</a>  � �� � ������������� ��������� ���������� � �� � Event ���������. 
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
				case 0 : return "��"; break;
				case 1 : return "��"; break;
				case 2 : return "��"; break;
				case 3 : return "��"; break;
				case 4 : return "��"; break;
				case 5 : return "��"; break;
				case 6 : return "��"; break;
				case 7 : return "��"; break;
			}
		}
	}
    
	if(!function_exists('getMonth')) {			
		function getMonth($month) {
			switch ($month) {
				case 1: $month = "������"; break;
				case 2 : $month = "�������"; break;
				case 3 : $month = "����"; break;
				case 4 : $month = "������"; break;
				case 5: $month = "���"; break;
				case 6 : $month = "����"; break;
				case 7 : $month = "����"; break;
				case 8 : $month = "������"; break;
				case 9 : $month = "��������"; break;
				case 10 : $month = "�������"; break;
				case 11 : $month = "������"; break;
				case 12 : $month = "�������"; break;
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
  
  echo '
  <tr>
  
  <td><b>�����������</b></td>
  <td width="120"><b>����</b></td>
  <td><b>��� �����������</b></td>
  <td><b>�����</b></td>

  </tr>';
  
      $newsData = $page->controls[$id]->dataSource;
      foreach ($newsData as $year=>$yeardata) {
          foreach ($yeardata as $month=>$monthdata) {
		  
			echo '<tr><td colspan="5">'.
					'<div><h4 class="detailsBlockTitle"><a name="description">'.$month.' '.$year.'</a></h4></div>';
				 '</td></tr>';
		  
              foreach ($monthdata as $item) {
              
             // echo $idate =  $item['simple_date'].' / '.date("Y-m-d H:i:s").'<br />';
              //echo $idate =  strtotime($item['simple_date']).' / '.strtotime(date("Y-m-d H:i:s"));
              //$cdate = date("Y-m-d", date("Y-m-d"));
            //  var_dump(UNIX_TIMESTAMP(date("Y-m-d")));
            // var_dump($idate.'/'.$cdate);
                  
                  
                  //echo $item['gr_year'];
                  
                  //echo $item['gr_day'].'|'.date("d").' | '.$item['gr_month'].'|'.date("n").' | '.$item['gr_year'].'|'.date("Y");
                 // echo $item['sdate'];
               //   echo date('Y-m-d', strtotime($item['sdate']));
                  
                 /* $color = "color:#ccc";
                  if( (int)$item['gr_year'] >= (int)date("Y")   ) {

                    if( (int)$item['gr_month'] >= (int)date("n") ) {
                    $color = "color:#000";
                      if( (int)$item['gr_day'] >= (int)date("d") ) {
                        
                      }
                    }
                  }
                  */
                 // if( date('Y-m-d', strtotime($item['sdate'])) > time()  ) 
                  
                  if(strtotime(date('Y-m-d ', strtotime($item['sdate'])) . '23:59:59') > time() )
                  { $color = ";color:#000";} else { $color = ";color:#ccc"; }
              
                  echo '<tr style="height:35px'.$color.';">';
                  
                  //����� ������ �������
                  if ($ind==0&&$this->prev==0) {
		
                      $style=";color:#000";
                  }
                  else {
                      $style=";color:#ccc";
                  }
                  echo '<td><b><a style="color:black;'.$style.$color.'" target="_blank" href="'.$item['link'].'">'.$item['title'].'</a></b></td>';
                  echo '<td style="color:gray'.$color.';" >'; 
                  if($item['date'] == $item['date_end']) {
				  
					echo $item['date'].' ('.$item['weekbegin'].')';
					
					// printf('<td style="padding:2px 0 2px 10px'.$style.'">%s</td>', $item['date']);
					// echo '<td colspan="2">&nbsp;</td>';
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
                  echo '</td>';
                  
                  printf('<td nowrap style="'.$style.$color.'">%s</td>',$item['type']);
                  printf('<td style="'.$style.$color.'">%s</td>',$item['place']);
                 // printf('<td nowrap style="'.$style.'"><a style="color:gray;'.$style.'" target="_blank" href="%s">���������</a></td>',$item['link']);
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

<td width="240" style="padding-left:12px;padding-right:3px" valign="top"> 						
                        <div class="leftPanelWitgets">
                            <div class="leftPanelWitget_container"><iframe frameborder="0" scrolling="no" allowtransparency="true" style="margin-bottom:10px;border:none; overflow:hidden; width:240px; height:360px;" src="http://www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2FEventCatalog&amp;width=240&amp;colorscheme=light&amp;show_faces=true&amp;border_color=grey&amp;stream=false&amp;header=false&amp;height=360"></iframe></div>
                            <div class="leftPanelWitget_container">

                                <!--  AdRiver code START. Type:240x400 Site: EventCat PZ: 2 BN: 1 -->
						<script language="javascript" type="text/javascript"><!--
						var RndNum4NoCash = Math.round(Math.random() * 1000000000);
						var ar_Tail='unknown'; if (document.referrer) ar_Tail = escape(document.referrer);
						document.write(
						'<iframe src="http://ad.adriver.ru/cgi-bin/erle.cgi?'
						+ 'sid=112204&bn=1&target=blank&bt=22&pz=2&rnd=' + RndNum4NoCash + '&tail256=' + ar_Tail
						+ '" frameborder=0 vspace=0 hspace=0 width=240 height=400 marginwidth=0'
						+ ' marginheight=0 scrolling=no></iframe>');
						//--></script>
						<noscript>
						<a href="http://ad.adriver.ru/cgi-bin/click.cgi?sid=112204&bn=1&bt=22&pz=2&rnd=294406122" target=_blank>
						<img src="http://ad.adriver.ru/cgi-bin/rle.cgi?sid=112204&bn=1&bt=22&pz=2&rnd=294406122" alt="-AdRiver-" border=0 width=240></a>
						</noscript>
						<!--  AdRiver code END  -->
                            </div>

                            <div class="leftPanelWitget_container">
                                <div style='width: 240px;margin-bottom: 10px;'>
					<!--  AdRiver code START. Type:240x400 Site: EventCat PZ: 2 BN: 2 -->
					<script language="javascript" type="text/javascript"><!--
					var RndNum4NoCash = Math.round(Math.random() * 1000000000);
					var ar_Tail='unknown'; if (document.referrer) ar_Tail = escape(document.referrer);
					document.write(
					'<iframe src="http://ad.adriver.ru/cgi-bin/erle.cgi?'
					+ 'sid=112204&bn=2&target=blank&bt=22&pz=2&rnd=' + RndNum4NoCash + '&tail256=' + ar_Tail
					+ '" frameborder=0 vspace=0 hspace=0 width=240 height=400 marginwidth=0'
					+ ' marginheight=0 scrolling=no></iframe>');
					//--></script>
					<noscript>
					<a href="http://ad.adriver.ru/cgi-bin/click.cgi?sid=112204&bn=2&bt=22&pz=2&rnd=1701254485" target=_blank>
					<img src="http://ad.adriver.ru/cgi-bin/rle.cgi?sid=112204&bn=2&bt=22&pz=2&rnd=1701254485" alt="-AdRiver-" border=0 width=240></a>
					</noscript>

					<!--  AdRiver code END  -->
				</div>
      </div>
      
      <div class="leftPanelWitget_container">
        <h3 class="h3">���� � �����</h3>
        <div class="be_aware">
					<span style="font-size: 12px;">����������� �����������, �������� � �������, � ����� ��������� ���������� � ��������. ������ � ����� ��������� ��������!</span><br />
					<div style="padding-top:6px">
  					<noindex>
  					<a rel="nofollow" href="http://www.yandex.ru/?add=83172&amp;from=promocode" target="_blank">������ ���������� �� �������</a><br />
  					<a rel="nofollow" href="http://www.yandex.ru/?add=83173&from=promocode" target="_blank">������ ������� �� �������</a><br />
            <a rel="nofollow" href="http://vk.com/public34359442" target="_blank">�� � ��������</a><br />
            <a rel="nofollow" href="http://www.facebook.com/EventCatalog" target="_blank">�� � facebook</a> 
            </noindex>
          </div>
          
          <form method="post" onsubmit="javascript: return subscribe();">
						<input type="hidden" name="action" value="subscribe" />
						<input type="text" id="subscribe_email" style="width:150px; margin-top: 10px;" name="email" value="������� �-mail" onfocus="if (this.value=='������� �-mail') this.value=''" onblur="if (this.value=='') this.value='������� �-mail'" />
			      <input type="submit" style="margin: 6px 0 0 -1px;" value="�����������" />
					</form>
				</div>                                      
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
