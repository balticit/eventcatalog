<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title><?php CRenderer::RenderControl("titlefilter"); ?>Каталог подрядчиков - EVENT КАТАЛОГ</title>
	<?php CRenderer::RenderControl("metadata"); ?>
	<link href="/styles/dont_remove.css" rel="stylesheet" type="text/css"></link>
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" style="width:100%; height:100%;">
<tr><td><?php CRenderer::RenderControl("topLine"); ?></td></tr>
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
<td style="padding-left: 30px; padding-right: 30px; padding-top: 10px;">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="vertical-align:top;width:230px"> 
			<table cellpadding="0" cellspacing="0" border="0" style="width: 218px; border: 1px solid #dadada;">
			<!--<tr><td class="ram1"></td><td class="ram2"></td><td class="ram3"></td></tr>-->
			<tr>
				<!--<td class="ram4"></td>-->
				<td class="ram5"><?php CRenderer::RenderControl("actList"); ?></td>
				<!--<td class="ram6"></td>-->
				</tr>
			<!--<tr><td class="ram7"></td><td class="ram8"></td><td class="ram9"></td></tr>-->
			</table>
			<img src="/images/front/0.gif" width="220" height="10">
			<a class="addConstractor" href="/registration/?type=contractor">Добавить Подрядчика</a>
		</td>
		<td style="vertical-align:top">
				<div style="padding-left:0px;"> 
						<?php if ($this->is_main) { ?>
						<!--<div style="margin-bottom: 10px;"><?php //CRenderer::RenderControl("yaResTop"); ?></div>-->
						<?php CRenderer::RenderControl("pro2List"); ?>
						<table  border="0" cellpadding="0" cellspacing="0" width="100%"> 
						<tr style="vertical-align: top;">
							<td width="49%">      
								<div class="recomendTitle contractor h3">EVENT КАТАЛОГ рекомендует</div>
								<table class="recomended"><?php CRenderer::RenderControl("RecomedList_2"); ?></table>
							</td> 
							<td  colspan="2" style="padding: 32px 0 0 10px; vertical-align: top;"> 
								<table class="recomended"><?php CRenderer::RenderControl("RecomedList_1"); ?></table>
							</td>							
						</tr>
						<tr>
							<td width="49%">
								<div class="recomendTitle common h3">
									<a class="contractor" href="/added">Новые подрядчики</a>
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
                  <div class="recomendTitle contractor"><a class="contractor" href="/resident_news">Новости подрядчиков</a></div>
                  <table class="news"><?php CRenderer::RenderControl("NewsList"); ?></table></td>
                </td>
                <td rowspan="2" style="width: 10px;"><img src="/images/front/0.gif" height="1" width="10" alt="" /></td>
                <td class="resident_comments" width="50%">
                  <div class="commentTitle contractor">Новые комментарии</div>
                  <iframe src="/scroll/msg?res_type=contractor" class="comment" frameborder="0" scrolling="no">Ваш браузер не поддерживает фреймы</iframe>
                </td>
              </tr>
              <tr style="vertical-align: top;">
                <td><br />
                <div class="resident_news">
                  <div class="recomendTitle contractor"><a class="contractor" href="/contractor/rating">Рейтинг подрядчиков</a></div>
                  <table class="rate"><?php CRenderer::RenderControl("RatingList"); ?></table>
                </div>
                </td>
              </tr>
            </table>
            
						<?php /*
            <div class="commentTitle contractor"><a href="/random_resident/type/contractor" target="rnd"><img src="/images/refresh_contractor.png" style="margin: 1px 10px 0 0;"><span class="contractor" style="vertical-align: top;">Случайные компании</span></a></div>
						<iframe src="/random_resident/type/contractor" class="random" name="rnd" frameborder="0" scrolling="no">Ваш браузер не поддерживает фреймы</iframe>
						*/ ?>

						<div>
							<h1 class="recomendTitle contractor">Важный этап – поиск подрядчика</h1>
							<p>Каждое мероприятие, будь это банкет по случаю юбилея или научный симпозиум, становится своеобразной проверкой эвент организаторов на компетентность и способность соединить все части процесса в единое целое. Существует огромное количество вопросов, для решения которых менеджерам приходится тратить массу усилий и времени, и наиболее сложная часть в этом процессе – это поиск адекватных и надежных подрядчиков, способных выполнять свои обязательства и оказывать услуги высокого качества в организации мероприятий. Где найти хорошего осветителя, как правильно составить меню для нескольких сотен гостей, каким образом всех разместить и как спланировать вечер? Для быстроты поиска и возможности сравнения фирм, предлагающих однотипный сервис, и был создан эвент-каталог, в котором  мы предлагаем вам полную энциклопедию всего, что касается организации мероприятий. Важное место в ней занимают подрядчики: для вашего удобства и простоты поиска сформирована разбивка по профилям деятельности. У нас реализована рейтинговая систем, а это значит, что честная фирма, качественно устроившая проведение нескольких мероприятий, автоматически поднимается в рейтинге благодаря рекомендациям довольных заказчиков. Эвент-каталог также содержит комментарии и отзывы организаторов мероприятий, помогающие сформировать объективную оценку деятельности того или иного подрядчика. Поиск и выбор соответствующей компании в эвент-каталоге – это своеобразная страховка четкости организации мероприятия. Представьте, что вам срочно нужны гелиевые шары, и вы заказываете их у случайно выбранного подрядчика. Делаете подробный заказ: «Нужны зеленые шары в виде арки, а по бокам - звезды», затем оплачиваете, и получаете на утро помещение, оформленное подвесными гирляндами красного цвета. Чтобы в принципе исключить подобную ситуацию и не получить нежданный «сюрприз», в эвент-каталоге содержится много информации о каждом подрядчике, а мнения заказчиков являются хорошим подспорьем в выборе надежной компании.</p>
							<p>Если вы ищете что-то новое и необычное – советуем вам познакомиться с интересными решениями сезона на страницах <a href="/eventoteka"><b>эвентотеки</b></a>. Довольно часто подрядчики участвуют в нестандартных мероприятиях и экспериментальных отработках. Полученный опыт они успешно применяют при организации мероприятий, в числе которых может быть и ваш проект. Например, новинка <a href="/book/details932/"><b>аудиокупол</b></a> нашла свое применение на выставках и презентациях,  а 3D голограмма используется в качестве альтернативы видео-ведущему.</p>
						</div>
						<?php } else { ?>
								<?php CRenderer::RenderControl("yaListTop"); ?>
								<table border="0" cellpadding="0" cellspacing="0" class="tableInline" style="margin: 0 0px 0 0; width:auto;"><?php CRenderer::RenderControl("contList"); ?></table>
								<div><?php CRenderer::RenderControl("footerText"); ?></div>
						<?php } ?>
				</div>
				<p class="text"><?php CRenderer::RenderControl("pager"); ?></p><br>
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
