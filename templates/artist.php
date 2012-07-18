<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
		<title><?php CRenderer::RenderControl("titlefilter"); ?>Каталог артистов - EVENT КАТАЛОГ</title>
		<?php CRenderer::RenderControl("metadata"); ?>
		<script type="text/javascript" language="JavaScript" src="/js/artist_find.js"></script>
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
<tr><!--содержание-->
<td style="padding-left: 30px; padding-right: 30px; padding-top: 10px;" valign="top">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="vertical-align:top;width:230px">
                        <table cellpadding="0" cellspacing="0" border="0"  style="width: 218px; border: 1px solid #dadada;">
                       <!-- <tr><td class="ram1"></td><td class="ram2"></td><td class="ram3"></td></tr>-->
                        <tr>
							<!--<td class="ram4"></td>-->
                            <td class="ram5"><?php CRenderer::RenderControl("groupList"); ?></td> 
						<!--<td class="ram6"></td>-->
						</tr>
						
                        <!--<tr><td class="ram7"></td><td class="ram8"></td><td class="ram9"></td></tr>--> 
                        </table>
			<img src="/images/front/0.gif" width="220" height="10">
            <a class="addArtist" href="/registration/?type=artist">Добавить Артиста</a>
		</td>
		<td style="vertical-align:top">	
				<div style="padding-left: 0px;"> 
						<?php if ($this->is_main) { ?>
						<?php CRenderer::RenderControl("pro2List"); ?>
						<table border="0" cellpadding="0" cellspacing="0" width="100%">                               
								<tr style="vertical-align: top;">
									<td width="49%">      
										<div class="recomendTitle artist h3">EVENT КАТАЛОГ рекомендует</div>
										<table class="recomended"><?php CRenderer::RenderControl("RecomedList_2"); ?></table>
									</td> 
									<td  colspan="2" style="padding: 32px 0 0 10px; vertical-align: top;"> 
										<table class="recomended"><?php CRenderer::RenderControl("RecomedList_1"); ?></table>
									</td>							
								</tr>
								<tr>
									<td width="49%">
										<div class="recomendTitle common h3">
											<a class="artist" href="/added">Новые артисты</a>
										</div>
										<table class="recomended"><?php CRenderer::RenderControl("NewList_2"); ?></table>
									</td>
									<td colspan="2" style="padding: 32px 0 0 10px; vertical-align: top;">
										<table class="recomended"><?php CRenderer::RenderControl("NewList_1"); ?></table>
									</td>							
								</tr>
								</table>
								
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tr style="vertical-align: top;">
                <td rowspan="2" class="resident_news" width="50%">
                  <div class="recomendTitle artist"><a class="artist" href="/resident_news">Новости артистов</a></div>
                  <table class="news"><?php CRenderer::RenderControl("NewsList"); ?></table></td>
                </td>
                <td rowspan="2" style="width: 10px;"><img src="/images/front/0.gif" height="1" width="10" alt="" /></td>
                <td class="resident_comments" width="50%">
                  <div class="commentTitle artist">Новые комментарии</div>
                  <iframe src="/scroll/msg?res_type=artist" class="comment" frameborder="0" scrolling="no">Ваш браузер не поддерживает фреймы</iframe>
                </td>
              </tr>
              <tr style="vertical-align: top;">
                <td ><br />
                <div class="resident_news" style="height:395px;">
                  <div class="recomendTitle artist"><a class="artist" href="/artist/rating">Рейтинг артистов</a></div>
                  <table class="rate"><?php CRenderer::RenderControl("RatingList"); ?></table>
                </div>
                </td>
              </tr>
            </table>
						<?php /*<div class="commentTitle artist"><a href="/random_resident/type/artist" target="rnd"><img src="/images/refresh_artist.png" style="margin: 1px 10px 0 0;" alt="" /><span class="artist" style="vertical-align: top;">Случайные артисты</span></a></div>
						<iframe src="/random_resident/type/artist" class="random" name="rnd" frameborder="0" scrolling="no">Ваш браузер не поддерживает фреймы</iframe> */ ?>
            
                
                <div><h1 class="recomendTitle artist">Развлекательная часть вечера: артисты из эвент-каталога</h1>
								<p><a href="/artist?group=41&subgroup=43"><b>Ведущий на свадьбе</b></a>, <a href="/artist?group=51"><b>ди-джей</b></a> на вечеринке, <a href="/artist?group=44"><b>музыкальная группа</b></a> на банкете, выступление на корпоративном событии – практически ни одно эвент-мероприятие не обходится без присутствия артистов. При выборе жанра выступления обязательно должен учитываться повод празднества, возраст гостей и пожелания заказчика.</p>
								<p>Состав артистов включает в себя несколько подразделов.  В <a href="/artist?group=41"><b>каталог ведущих</b></a> записаны известные артисты телевидения и радио, которых можно пригласить на корпоративные мероприятия, свадьбы, юбилеи и тематические праздники. Контактные данные всех шоуменов доступны для просмотра зарегистрированным пользователям. В отзывах о музыканте заказчики отмечают нюансы сотрудничества, стиль работы и личные качества человека (группы). Поклонников живой музыки в эвент-каталоге ждут неожиданные открытия: у нас представлено много молодых групп и артистов с прекрасными данными. Несомненный талант, способность удержать аудиторию, добросовестность и пунктуальная точность обеспечат гостям приятный отдых в непринужденной атмосфере вашего мероприятия.</p>
								<p>Среди прочих предложений следует особо отметить <a href="/artist?group=47"><b>артистические шоу-программы</b></a> -  развлечения, которые можно заказать для костюмированных вечеринок выбранной тематики. Ваши гости точно не обойдут вниманием феерическое огненное шоу, а замысловатые кульбиты шейкера в руках бармена определенно подогреют интерес к виртуозным творениям артистов <a href="/artist?group=47&subgroup=1158"><b>бармен-шоу</b></a>. Все эвент-программы содержат подробное описание спецэффектов и контакты агентов, которые обговаривают нюансы предстоящего выступления – от требований безопасности и размера гримерной до времени выхода на сцену и особых условий (обычно прописано в райдере). У многих артистов плотный график работы, и если вы хотите пригласить, например, ведущего на свадьбу  - необходимо заранее заручиться согласием исполнителя.</p>
								<p>Хотите быть в курсе последних тенденций в мире музыки-ТВ-шоу? На закладке <a href="/eventtv"><b>Event TV</b></a> ежедневно публикуются последние обзоры и репортажи с мест событий, из которых можно составить свое мнение о артистах, ведущих мероприятия.</p></div>
                                                
						<?php } else { ?>
								<?php CRenderer::RenderControl("yaListTop"); ?>
                                <?php if(!$this->is_main) CRenderer::RenderControl("titlefilterLinks"); ?>
								<?php print($this->search_styles); ?>
                                                                <div style="overflow-x:hidden">
								<table border="0" cellpadding="0" cellspacing="0" class="tableInline" style="margin: 0 0px 0 0; width:100%;">
                                                                    <?php CRenderer::RenderControl("artistList"); ?>
                                                                </table>
                                                                </div>
								<div><?php CRenderer::RenderControl("footerText"); ?></div>
						<?php } ?>
				</div>
				<p class="text"><?php CRenderer::RenderControl("pager"); ?></p><br />
		</td>
                <?php require ROOTDIR.'templates/_leftMenuWitgets.php'; ?>
	</tr>
	</table>
</td></tr>
<tr><td class="partner_ban"><?php CRenderer::RenderControl("bottomBanners"); ?></td></tr>
<tr><td class="foot"><?php CRenderer::RenderControl("footer"); ?></td></tr>
</table>
<?php CRenderer::RenderControl("googleanalytics"); ?>
</body>
</html>
