<?php
	// календарь
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
			function ToUTF($n) {
				return array_map('ToUTF2', $n);
			}
			function ToUTF2($n) {
				return iconv("windows-1251","UTF-8", $n);
			}
			$calendar['arr'] = json_encode(array_map('ToUTF', $cal_arr));
			// die('!');
			

			// LIST 5 last news
			$sql2  = '(SELECT t.title, t.date, t.date_end, t.link '.
				'FROM `tbl__event_calendar` t WHERE t.active = 1 AND t.date > NOW()) '.
			' UNION '.
			'(SELECT n.title, DATE_FORMAT(n.date, "%Y-%m-%d") date, DATE_FORMAT(n.date, "%Y-%m-%d") date_end, '.
				'CONCAT("/news/details", n.tbl_obj_id) link '.
				'FROM `tbl__news` n WHERE n.active = 1 AND n.in_calendar = 1 AND date > NOW()  ) '.
			'ORDER BY date ASC LIMIT 5';
			$cal_arr2 = SQLProvider::ExecuteQuery($sql2);
			
			
	
?>
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
                                <h3 class="h3">
                                  <div class="calendar_tab">
                                  <span class="calendar_ico active" id="calendar_table"></span>
                                  <?php
                                  $act_list = 'noact';
                                  foreach($cal_arr2 as $i => $c) {
                                    if($c['date'] > Date('Y-m-d')) { $act_list = ''; break; }
                                  }
                                  ?>
                                  
                                  <span class="calendar_ico <?php  echo $act_list; ?>" id="calendar_list"></span>
                                  </div>
                                  <a href="/event_calendar/" class="sub-title widget" style="color:#000">EVENT Календарь</a>
                                </h3>
                                <script language="javascript" type="text/javascript">
                                $("#calendar_table").click(function(){
                                  $(this).addClass("active");
                                  $("#calendar_list").removeClass("active");
                                  $(".calendar-list").hide();
                                  $(".calendar-block").show();
                                });
                                $("#calendar_list").click(function(){
                                  if($(this).hasClass("noact")) { return false; }
                                  $(this).addClass("active");
                                  $("#calendar_table").removeClass("active");
                                  $(".calendar-list").show();
                                  $(".calendar-block").hide();
                                });
                                </script>
                                
                                <div class="calendar-list">
                                <?php
                                
                                foreach($cal_arr2 as $i => $c) {
                                  if($c['date'] > Date('Y-m-d')) {
                                    echo '<div class="last_calendar">';
                                    echo '<div class="date">'.Date_Ru($c['date']).'</div>';
                                    echo '<div class="name"><a href="'.$c['link'].'">'.$c['title'].'</a></div>';
                                    echo '</div>';
                                  }
                                }
                                
                                ?>
                                </div>
                                
                                <div class='calendar-block'>									
									<script>
									$(document).ready( function() {
										//{arr};
										var evArr = <?php echo $calendar['arr']; ?>;
										$('#calendar').datepicker({
											closeText: '',
											prevText: '',
											nextText: '',
											currentText: 'Сегодня',
											monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
											'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
											monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
											'Июл','Авг','Сен','Окт','Ноя','Дек'],
											dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
											dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
											dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
											weekHeader: 'Нед',
											dateFormat: 'dd.mm.yy',
											firstDay: 1,
											isRTL: false,
											showMonthAfterYear: false,
											yearSuffix: '',
											beforeShowDay: function(date) {
												//alert('!');
												var dateString = 'date-'+date.getFullYear()+'-'+((date.getMonth()+1)<10 ? '0'+(date.getMonth()+1) : date.getMonth()+1)+'-'+(date.getDate()<10 ? '0'+date.getDate() : date.getDate());  
												for (i=0;i<evArr.length;i++) {
													if (dateString=='date-'+evArr[i].date) {
														dateString += ' active';
														var cur = new Date();
														if(date.getTime()<cur.getTime()){
															dateString += ' datepassive'
														}
														break;
													}
												}
												return [true, dateString, ''];
											}
										});
										$('.ui-datepicker td.active').live('mouseover', function() {
												var d = $(this).attr('class').substr($(this).attr('class').search('date-')+5,10);
												var text = '';
												for (i=0;i<evArr.length;i++) {
													if (evArr[i].date==d) {
														text += '<a href="'+evArr[i].link+'"  target="_blank" style="color: black;"><b>'+evArr[i].title+'</b></a><br /><br />';
													}
												}
												var leftTt = Math.round($(this).position().left)-169;
												if ($(this).position().left<90) {
													leftTt = Math.round($(this).position().left)+1;
												}
												if(($.browser.msie && $.browser.version>7)||$.browser.mozilla) {
													var cH = 23;
												}
												else {
													var cH = 22;
												}
												$('.cal-tooltip').css({'display':'block', 'top':Math.round($(this).position().top+cH),'left': leftTt,'background-color':'#ffcd00'});
												if($(this).hasClass('datepassive')){
													$('.cal-tooltip').css({'display':'block', 'top':Math.round($(this).position().top+cH),'left': leftTt,'background-color':'#AAAAAA'});
												}
												$('.cal-tooltip').html(text);
										});
										$('.ui-datepicker td.active').live('mouseout', function() {
												$('.cal-tooltip').css('display','none');
										});
										$('.cal-tooltip').hover( function() {
											$('.cal-tooltip').css('display','block');
										}, function() {
											$('.cal-tooltip').css('display','none');
										});
									});
									</script>
        
								<div id='calendar'>
									<div class='cal-tooltip'></div>
								</div>
									
								</div>
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
        <h3 class="h3">Быть в курсе</h3>
        <div class="be_aware">
					<span style="font-size: 12px;">Предстоящие мероприятия, выставки и события, а также последние добавления в каталоге. Будьте в курсе последних новостей!</span><br />
					<div style="padding-top:6px">
  					<noindex>
  					<a rel="nofollow" href="http://www.yandex.ru/?add=83172&amp;from=promocode" target="_blank">Виджет Эвентотеки на Яндексе</a><br />
  					<a rel="nofollow" href="http://www.yandex.ru/?add=83173&from=promocode" target="_blank">Виджет Новинок на Яндексе</a><br />
            <a rel="nofollow" href="http://vk.com/public34359442" target="_blank">Мы в контакте</a><br />
            <a rel="nofollow" href="http://www.facebook.com/EventCatalog" target="_blank">Мы в facebook</a> 
            </noindex>
          </div>
          
          <form method="post" onsubmit="javascript: return subscribe();">
						<input type="hidden" name="action" value="subscribe" />
						<input type="text" id="subscribe_email" style="width:150px; margin-top: 10px;" name="email" value="введите е-mail" onfocus="if (this.value=='введите е-mail') this.value=''" onblur="if (this.value=='') this.value='введите е-mail'" />
			      <input type="submit" style="margin: 6px 0 0 -1px;" value="подписаться" />
					</form>
				</div>                                      
      </div>
      
  </div>
</td>