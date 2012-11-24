<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>EVENT КАТАЛОГ – Портал для организаторов мероприятий</title>
	<?php CRenderer::RenderControl("metadata"); ?>
  <link rel="stylesheet" type="text/css" href="/styles/index.css">
  <script type="text/javascript" language="JavaScript" src="/js/index.js"></script>
  <script src="/js/jquery.jcarousel.min.js" type="text/javascript"></script>
  <script src="/js/jquery-ui-1.8.17.custom.min.js" type="text/javascript"></script>
  <link href="/js/ui-lightness/jquery-ui-1.8.17.custom.css" type="text/css" rel="stylesheet">
  <link href="/styles/scroll_vert.css?v2" type="text/css" rel="stylesheet">
	<script>
	jQuery.fn.extend({
    disableSelection : function() {
            this.each(function() {
                    this.onselectstart = function() { return false; };
                    this.unselectable = "on";
                    jQuery(this).css('-moz-user-select', 'none');
            });
    },
    enableSelection : function() {
            this.each(function() {
                    this.onselectstart = function() {};
                    this.unselectable = "off";
                    jQuery(this).css('-moz-user-select', 'auto');
            });
		}
	});
	$(document).ready( function() {
		jQuery('#friends-carousel').jcarousel({vertical: true,scroll: 1, visible:3,auto: 3,wrap: 'circular'});
		$('.main-page-video').each( function() {
			$(this).css('height',$(this).width()*9/16);
		});
		$('.our-friends').hover( function() {
			$('.unselectable *').disableSelection();
		},function() {
			$('.unselectable *').enableSelection();
		});
		$(window).resize( function() {
			$('.main-page-video').each( function() {
				$(this).css('height',$(this).width()*9/16);
			});
		});
	});
	</script>
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" style="width:100%; height:100%;">
<tr><td><?php CRenderer::RenderControl("topLine");?></td></tr>
<tr><!-- Заголовок-->
	<td style="padding: 16px 30px 0px 30px;"><?php CRenderer::RenderControl("header"); ?></td>
</tr>
<tr><!--Меню-->
<td style='padding-bottom:15px'><?php CRenderer::RenderControl("menu"); ?></td>
</tr>

<tr>
<td>
<div class="menu-all-category">
  <div class="category-contractor category-item"><?php CRenderer::RenderControl("typeList1"); ?></div>
  <div class="category-area category-item"><?php CRenderer::RenderControl("typeList2"); ?></div>
  <div class="category-artist category-item"><?php CRenderer::RenderControl("typeList3"); ?></div>
  <div class="category-agency category-item"><?php CRenderer::RenderControl("typeList4"); ?></div>
  <div class="clear"></div>
</div>

<script language="JavaScript" type="text/javascript">
	$(document).ready(function(){
	  var max_height = 0;
    $("div.category-item").each(function(){
       if ($(this).height() > max_height) { max_height = $(this).height(); }
    });
    $("div.category-item").height(max_height);
    
    $(".category-item").each(function(){
      var left = 0;
      
      $(this).find('.parent-item').each(function(){
        var countChild = $(this).find('.child-item').length
        if(countChild < 10) { $(this).find('.child-item').css("width", "70%" ); }
      });
      
      
      $(this).find('.parents').each(function(){
        if( $(this).width() > left) { left = $(this).width(); }
      });
      
      $(this).find(".level2").css("left",left+20+"px");
      
    });
    
	
    $(".parent-item a.parents").mouseenter(function(){
      $(".parent-item").removeClass("active");
      $(this).parents("div:first").addClass("active");
      //var left = $(this).width()+20;
      $(".level2").hide();
      $(this).next(".level2:first").show();
    });
    
    $(".parent-item.active").live('mouseleave', function(){
      $(".parent-item").removeClass("active");
      $(".level2").hide();
    });
    
	});
</script>

</td>
</tr>
<tr>
    <td>
        <table border="0" cellpadding="0" cellspacing="0" width="100%" >
        <tr> 
            <td width="" rowspan="10" style="vertical-align: top;"><?php CRenderer::RenderControl("carousel")?></td>
            <td width='240' align="right" rowspan="3" style="padding-right:30px; vertical-align:middle; height:385px">
            <div style='height:360px; display:inline-block'>
              <iframe frameborder="0" scrolling="no" allowtransparency="true" style="margin-bottom:10px;border:none; overflow:hidden; width:240px; height:360px;" src="http://www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2FEventCatalog&amp;width=240&amp;colorscheme=light&amp;show_faces=true&amp;border_color=grey&amp;stream=false&amp;header=false&amp;height=360"></iframe>
            </div>
            </td>  
        </tr>
        </table>
    </td>
</tr>
<tr>
	<td height='10px' class='separate-line'></td>
</tr>
<tr><!-- Eventoteka-->
	<td>
    <table border="0" cellpadding="0" cellspacing="0" width="100%" >
      <tr>
        <td style="padding: 0 30px 10px 30px;">
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr valign="top">
				<td width="" rowspan="10" colspan="2">
				<div class='sub-title-block'><a href='/eventoteka/' class='sub-title widget'>Эвентотека</span><a target='_blank' href='http://www.yandex.ru/?add=83172&from=promocode' class='gold-btn'><p>Виджет Эвентотеки на Яндексе</p></a></div>
        <table border="0" cellpadding="0" cellspacing="0" width="100%"><tr>
          <td width="50%"><?php CRenderer::RenderControl("event_news1"); ?></td>
          <td width="50%"><?php CRenderer::RenderControl("event_news2"); ?></td>
          </tr></table>
				</td>
				<td width='240' align="right" rowspan="3" style="vertical-align:middle">
					
					<div style='height:400px;width: 240px; display:inline-block'>
					<noindex>
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
						<img src="http://ad.adriver.ru/cgi-bin/rle.cgi?sid=112204&bn=1&bt=22&pz=2&rnd=294406122" alt="-AdRiver-" border=0 width=240 height=400></a>
						</noscript>
						<!--  AdRiver code END  -->
					</noindex>
					</div>
				</td>
            </tr>
          </tbody>
		</table>
        </td>
      </tr>
    </table>
    </td>
</tr>
<tr>
	<td height='10px' class='separate-line'></td>
</tr>
<!-- События, Новости, Календарь -->
<tr>
<td>
    <table border="0" cellpadding="0" cellspacing="0" width="100%" >
      <tr>
        <td style="padding: 0 30px 10px 30px;">
          <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr valign="top">
            <td width="" rowspan="10" colspan="2">
				<div class='first-col'>
					<div class='sub-title-block'><a href='/news/' class='sub-title widget'>События индустрии</a></div>
					<?php CRenderer::RenderControl("news"); ?>
				</div>
				<div class='first-col'>
					<div class='sub-title-block'><a href='/resident_news/' class='sub-title widget'>Новости Резидентов</a><a href='/add_res_news' class='grey-btn'><p>Добавить новость</p></a></div>
					<?php CRenderer::RenderControl("newAreas"); ?>
				</div>
			</td>
            <td width='240' align="right" rowspan="3">
            
          

				
					<?php CRenderer::RenderControl("eventcalendar"); ?> 
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
				
				<div style='height:600px;width: 240px;margin-bottom: 10px;'>
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
					<img src="http://ad.adriver.ru/cgi-bin/rle.cgi?sid=112204&bn=2&bt=22&pz=2&rnd=1701254485" alt="-AdRiver-" border=0 width=240 height=400></a>
					</noscript>

					<!--  AdRiver code END  -->
				</div>
				<div class="sub-title-block"><span class="sub-title widget">Быть в курсе</span></div>
				
				
				<div class="be_aware">
					<span style="font-size: 12px;">Предстоящие мероприятия, выставки и события, а также последние добавления в каталоге. Будьте в курсе последних новостей!</span><br />
					<div style="padding-top:6px">
  					<noindex>
  					<a rel="nofollow" href="http://www.yandex.ru/?add=83172&amp;from=promocode" target="_blank">Виджет Эвентотеки на Яндексе</a><br />
  					<a rel="nofollow" href="http://www.yandex.ru/?add=83173&from=promocode" target="_blank">Виджет Новинок на Яндексе</a><br />
  					
            <a rel="nofollow" href="http://vk.com/public34359442" target="_blank">Мы в контакте</a>  <br />
            <a rel="nofollow" href="http://www.facebook.com/EventCatalog" target="_blank">Мы в facebook</a> 
            </noindex>
          </div>
          
          <form method="post" onsubmit="javascript: return subscribe();">
						<input type="hidden" name="action" value="subscribe" />
						<input type="text" id="subscribe_email" style="width:150px; margin-top: 10px;" name="email" value="введите е-mail" onfocus="if (this.value=='введите е-mail') this.value=''" onblur="if (this.value=='') this.value='введите е-mail'" />
			      <input type="submit" style="margin: 6px 0 0 -1px;" value="подписаться" />
					</form>

				</div>

				
				
				
				
            </td>
            </tr>
          </tbody></table>
        </td>
      </tr>
          </table>
        </td>
</tr>
<tr>
	<td height='10px' class='separate-line'></td>
</tr>
<!-- Последние добавления, Рейтинг, Всего резидентов -->
<tr>
<td>
    <table border="0" cellpadding="0" cellspacing="0" width="100%" >
      <tr>
        <td style="padding: 0 30px 10px 30px;">
          <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr valign="top">
            <td width="" rowspan="10" colspan="2">
            
            <div class='first-col'>
					<div class='sub-title-block'><span class='sub-title widget'>Эвенторы в эфире</span></div>
					<div style="vertical-align: top; border: 1px solid #e4e4e4; padding: 5px 11px 11px 26px; background-color:#f8f8f8;">
						<div style="position: relative; padding-top:6px;">
							<div class="item_header" style="position: absolute; top:0; padding-bottom: 10px;">Новые комментарии:</div>
							<iframe name="scroll_msg" src="/scroll/msg/" width="100%" height="305" frameborder="0" scrolling="no">Ваш браузер не поддерживает фреймы</iframe>
						</div>
					</div>
				</div>
            
				<div class='first-col'>
					<div class='sub-title-block'><a href='/added/' class='sub-title widget'>Последние добавления</a><a target='_blank' href='http://www.yandex.ru/?add=83173&from=promocode' class='gold-btn'><p>Виджет Новинок на Яндексе</p></a></div>
					<div style="vertical-align: top; height: 309px; border: 1px solid #e4e4e4; padding: 5px 11px 11px 26px; ">
          <table width="100%">
					<tr>
						<td width="50%"><div style="overflow:hidden; margin-right:2px"><?php CRenderer::RenderControl("newRegistered"); ?></div></td>
						<td><div style="overflow:hidden;margin-right:2px"><?php CRenderer::RenderControl("newRegistered2"); ?></div></td>
					</tr>
					</table>
					</div>
				</div>
				<?php /*
				<div class='first-col'>
					<div class='sub-title-block'><span class='sub-title'>Рейтинг</span></div>
					<table width='100%'>
						<tr>
							<td style='padding-bottom:10px'>
								<div>
									<div class="list_header"><a href="/contractor/rating" class="item_header" style="color:#f05620;">Рейтинг подрядчиков:</a></div>
									<table width='100%' cellspacing="0" cellpadding="0"><?php CRenderer::RenderControl("rateContractors"); ?></table>
								</div>
							</td>
							<td style='padding-bottom:10px'>
								<div>
									<div class="list_header"><a href="/area/rating" class="item_header" style="color:#3399ff;">Рейтинг площадок: </a></div>
									<table width='100%' cellspacing="0" cellpadding="0"><?php CRenderer::RenderControl("rateAreas"); ?></table>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div>
									<div class="list_header"><a href="/artist/rating" class="item_header" style="color:#ff0066;">Рейтинг артистов: </a></div>
									<table width='100%' cellspacing="0" cellpadding="0"><?php CRenderer::RenderControl("rateArtists"); ?></table>
								</div>
							</td>
							<td>
								<div>
									<div class="list_header"><a href="/agency/rating" class="item_header" style="color:#99cc00;">Рейтинг агентств:</a></div>
									<table width='100%' cellspacing="0" cellpadding="0"><?php CRenderer::RenderControl("rateAgencies"); ?></table>
								</div>
							</td>
						</tr>
					</table>
				</div>
				*/ ?>
				
				
			</td>
            <td width='240' align="right" rowspan="3">
				<div class='sub-title-block'><span class='sub-title widget'>Всего резидентов</span></div>
				<?php CRenderer::RenderControl("chart"); ?>
            </td>
            </tr>
          </tbody></table>
        </td>
      </tr>
          </table>
        </td>
</tr>
<?php /*
<tr>
	<td height='10px' class='separate-line'></td>
</tr>
<!-- Эвенторы в эфире, Работа, Наши друзья -->
<tr>
<td class='unselectable'>
    <table border="0" cellpadding="0" cellspacing="0" width="100%" >
      <tr>
        <td style="padding: 0 30px 10px 30px;">
          <table border="0" cellpadding="0" cellspacing="0" width="100%">
          
            <tr valign="top">
            <td width="" rowspan="10" colspan="2">
				<div class='first-col'>
					<div class='sub-title-block'><span class='sub-title widget'>Эвенторы в эфире</span></div>
					<div style="vertical-align: top; border: 1px solid #e4e4e4; padding: 5px 11px 11px 26px; background-color:#f8f8f8;">
						<div style="position: relative; padding-top:6px;">
							<div class="item_header" style="position: absolute; top:0; padding-bottom: 10px;">Новые комментарии:</div>
							<iframe name="scroll_msg" src="/scroll/msg/" width="100%" height="305" frameborder="0" scrolling="no">Ваш браузер не поддерживает фреймы</iframe>
						</div>
					</div>
				</div>

		
				<div class='first-col'>
					<div class='sub-title-block'><span class='sub-title'>Работа в EVENT-Индустрии</span></div>
					<div style='border: 1px dashed #999; height: 326px;'><p style="display: block; text-align: center; text-transform: uppercase; color: rgb(204, 204, 204); line-height: 320px; margin: 0pt; font-size: 32px;">Скоро</p></div>
				</div>
		
			</td>
            <td width='240' align="right" rowspan="3">
            <?php /*
				<div class='sub-title-block'><span class='sub-title widget'>Наши друзья</span></div>
				<noindex>
				<div class='our-friends jcarousel-skin-tango'>
					<ul id='friends-carousel'>
						<li><a rel="nofollow" href='http://www.raapa.ru/' target="_blank"><img src='/images/partners/rappa.jpg' /></a></li>
						<li><a rel="nofollow" href='http://micediscount.ru/' target="_blank"><img src='/images/partners/micedisc.jpg' /></a></li>
						<li><a rel="nofollow" href='http://eventist.ru/' target="_blank"><img src='/images/partners/eventistlogo.jpg' /></a></li>
						<li><a rel="nofollow" href='http://www.prazdnikmedia.ru/' target="_blank"><img src='/images/partners/prazdnik_izd.jpg' /></a></li>
						<li><a rel="nofollow" href='http://eventros.ru' target="_blank"><img src='/images/partners/naom.jpg' /></a></li>
						<li><a rel="nofollow" href='http://event.ru/' target="_blank"><img src='/images/partners/eventru.jpg' /></a></li>
						<li><a rel="nofollow" href='http://4banket.ru/' target="_blank"><img src='/images/partners/4banket.jpg' /></a></li>
						<li><a rel="nofollow" href='http://art4you.ru/' target="_blank"><img src='/images/partners/art4you.jpg' /></a></li>
						<li><a rel="nofollow" href='http://eventmarket.ru' target="_blank"><img src='/images/partners/emarket.jpg' /></a></li>
						<li><a rel="nofollow" href='http://event-forum.ru' target="_blank"><img src='/images/partners/evforum.jpg' /></a></li>
						<li><a rel="nofollow" href='http://vklube.ru' target="_blank"><img src='/images/partners/vklube.jpg' /></a></li>
						<li><a rel="nofollow" href='http://www.corpmedia.ru/' target="_blank"><img src='/images/partners/akmr.jpg' /></a></li>
						<li><a rel="nofollow" href='http://rusartgroup.ru/' target="_blank"><img src='/images/partners/rusartgroup.jpg' /></a></li>
						<li><a rel="nofollow" href='http://mf-group.com/' target="_blank"><img src='/images/partners/mfgroup.jpg' /></a></li>
						<li><a rel="nofollow" href='http://cateringconsulting.ru/' target="_blank"><img src='/images/partners/catcons.jpg' /></a></li>
						<li><a rel="nofollow" href='http://tabriz.ru/' target="_blank"><img src='/images/partners/imperiamuzk.jpg' /></a></li>
						<li><a rel="nofollow" href='/contractor/avtoprazdnik' target="_blank"><img src='/images/partners/avtoprazdnik.jpg' /></a></li>
						<li><a rel="nofollow" href='http://teatrodelgusto.biz/' target="_blank"><img src='/images/partners/teatrodg.jpg' /></a></li>
						<li><a rel="nofollow" href='http://www.ru.tv/' target="_blank"><img src='/images/partners/ru-tv.jpg' /></a></li>
						<li><a rel="nofollow" href='http://et-cetera.ru/' target="_blank"><img src='/images/partners/etceteralogo.jpg' /></a></li>
						<li><a rel="nofollow" href='http://www.idr.ru/' target="_blank"><img src='/images/partners/logo_IDR.jpg' /></a></li>
						<li><a rel="nofollow" href='http://constellation.msk.ru/' target="_blank"><img src='/images/partners/constellation.jpg' /></a></li>
						<li><a rel="nofollow" href='http://pstgrafika.ru/' target="_blank"><img src='/images/partners/graphika_logo.jpg' /></a></li>
						<li><a rel="nofollow" href='http://www.marketingone.ru/conference/index.htm' target="_blank"><img src='/images/partners/marketingone_logo.jpg' /></a></li>
						<li><a rel="nofollow" href='http://www.vedomosti.ru/events/' target="_blank"><img src='/images/partners/vedomosti.jpg' /></a></li>
						<li><a rel="nofollow" href='http://ashar.ru/' target="_blank"><img src='/images/partners/ashar.jpg' /></a></li>
						<li><a rel="nofollow" href='http://www.ardisprint.ru' target='_blank'><img src='/images/partners/ardis_print.jpg'/></a></li>
					
					</ul>
				</div>
				</noindex>
				
            </td>
            </tr>
            
          </tbody></table>
        </td>
      </tr>
          </table>
        </td>
</tr>
*/ ?>
<tr><td class="partner_ban"><div style="margin-top:10px;margin-bottom:-5px;" class="sub-title-block h3"><span class="sub-title widget">Партнеры Event каталога</span></div><?php CRenderer::RenderControl("bottomBanners"); ?></td></tr>
<tr><td class="foot unselectable"><?php CRenderer::RenderControl("footer"); ?></td></tr>
</table>
<?php CRenderer::RenderControl("googleanalytics"); ?>
</body>
</html>
