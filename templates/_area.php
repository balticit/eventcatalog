<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title><?php CRenderer::RenderControl("titlefilter"); ?>Каталог площадок - EVENT КАТАЛОГ</title>
		<?php CRenderer::RenderControl("metadata"); ?>
		<script language="javascript"><?php CRenderer::RenderControl("jsArea"); ?></script>
		<link rel="stylesheet" type="text/css" href="/styles/fm.css">
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
		<td style="vertical-align:top; padding-left:5px;  width:230px">
			<table cellpadding="0" cellspacing="0" border="0" style="width: 218px; border: 1px solid #dadada;">
			<!--<tr><td class="ram1"></td><td class="ram2"></td><td class="ram3"></td></tr>-->
			<tr>
				<!--<td class="ram4"></td>-->
				<td class="ram5"><?php CRenderer::RenderControl("typeList"); ?></td>
				<!--<td class="ram6"></td></tr>-->
			<!--<tr><td class="ram7"></td><td class="ram8"></td><td class="ram9"></td></tr>-->
			</table>
			<img src="/images/front/0.gif" width="220" height="10">
                        <a id="witgetAddResident" href="/registration/?type=user">Добавить резидента</a>
		</td>
		<td style="vertical-align:top">
				<?php if(!$this->is_main) //CRenderer::RenderControl("titlefilterLinks"); ?>
				<!--<table cellpadding="1" cellspacing="0" class="letterFilter" style="width: 100%;" height="25">
				<tr><?php CRenderer::RenderControl("letterFilter"); ?><td style="width:80px;"></td></tr></table>-->
				<div>
                                <form method="get" style="display: none" id="find_params"></form>
                                <?php CRenderer::RenderControl("currentFind"); ?></div>
				<div>
					<?php CRenderer::RenderControl("currentCapacity"); ?>
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr><?php CRenderer::RenderControl("capacityFilter"); ?></tr>
					</table></div>
				<div style="padding-top:15px; padding-left: 0px;"> 
						<?php if ($this->is_main) { ?>
						<!--<div style="margin-bottom: 10px;"><?php //CRenderer::RenderControl("yaResTop"); ?></div>-->
						<?php CRenderer::RenderControl("pro2List"); ?>
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr style="vertical-align: top;">
								<td width="50%"><div class="recomendTitle area h3">EVENT КАТАЛОГ рекомендует</div>
								<table class="recomended"><?php CRenderer::RenderControl("RecomedList"); ?></table></td>
								<td style="width: 10px;"><img src="/images/front/0.gif" height="1" width="10"></td>
								<td width="50%"><div class="recomendTitle common h3"><a class="common" href="/added">Новые площадки</a></div>
								<table class="recomended"><?php CRenderer::RenderControl("NewList"); ?></table></td></tr>
						<tr style="vertical-align: top;">
								<td class="resident_news"><div class="recomendTitle area h3"><a class="area" href="/resident_news">Новости площадок</a></div>
								<table class="news"><?php CRenderer::RenderControl("NewsList"); ?></table></td>
								<td style="width: 10px;"><img src="/images/front/0.gif" height="1" width="10"></td>
								<td class="resident_comments"><div class="commentTitle area h3">Новые комментарии</div>
								<iframe src="/scroll/msg?res_type=area" class="comment" frameborder="0" scrolling="no">Ваш браузер не поддерживает фреймы</iframe></td></tr>
						<tr><td colspan="3"><img src="/images/front/0.gif" height="10" width="1"></td></tr>
						<tr style="vertical-align: top;">
								<td><div class="recomendTitle area h3"><a class="area" href="/area/rating">Рейтинг площадок</a></div>
								<table class="rate"><?php CRenderer::RenderControl("RatingList"); ?></table></td>
								<td style="width: 10px;"><img src="/images/front/0.gif" height="1" width="10"></td>
								<td><div class="commentTitle area h3"><a href="/random_resident/type/area" target="rnd"><img src="/images/refresh_area.png" style="margin: 1px 10px 0 0;"><span class="area" style="vertical-align: top;">Случайные площадки</span></a></div>
								<iframe src="/random_resident/type/area" class="random" name="rnd" frameborder="0" scrolling="no">Ваш браузер не поддерживает фреймы</iframe></td></tr></table>
								<div>
									<div class="recomendTitle area h3">Место для праздника – аренда площадки</div>
									<p>Залог успеха отлично проведенного мероприятия – это удачное сочетание работы ответственного эвент-агентства, надежных подрядчиков, и хорошо подобранного места. Для того чтобы выбрать и арендовать  площадку, соответствующую характеру предстоящего события, необходимо определиться с рядом факторов. Обычно учитывают цель эвент-события, бюджет на аренду, количество присутствующих, прогноз погоды (если площадка находится не в помещении) и пожелания заказчика. В нашем каталоге эвент площадок находится наиболее полный перечень мест, предлагаемых предпринимателями для аренды. Можно подобрать открытые площади или закрытые особняки,  загородные парки или ночные клубы, банкетные залы или детские центры – каждая площадка сопровождается подробным описанием размеров, оформления и местоположения. Особо указывается наличие крытой территории, охраны и стоянки для автомобилей. Например, к банкетным площадкам предъявляются самые высокие требования, и степень комфорта и сервиса должна быть оптимальной для посетителей. Пригородные зоны для эвент-отдыха, напротив, должны соответствовать критериям чистоты и эстетичности природы, а удобство присутствующих на арендуемой площадке обеспечивается временными средствами, предоставляемыми организаторами. В картинг-центрах важное значение имеет обеспечение безопасности, в выставочных залах – вместительность, в ночных клубах – стилистика помещения и так далее, список можно продолжать практически бесконечно, а вывод следует один – каждое мероприятие имеет свою специфику выбора места его проведения.</p>
									<p>Заказчики, которые арендовали ту или иную эвент-площадку часто оценивают степень удобства расположения места и впечатление, оставшееся у гостей после посещения мероприятия. Это напрямую отражается на рейтинге, и облегчает поиск следующим организаторам. Кроме обычных отзывов и комментариев эвент-каталог публикует также последние <a href="/resident_news"><b>новости о площадках</b></a> и проводимых на них мероприятиях, а в разделе <a href="/eventoteka"><b>эвентотека</b></a> появляются обзоры новых мест для аренды.</p>
								</div>
						<?php } else { ?>
								<?php CRenderer::RenderControl("yaListTop"); ?>
                                                                <?php CRenderer::RenderControl("titlefilterLinks"); ?>
								<table border="0" cellpadding="0" cellspacing="0" class="tableInline" style="margin: 0 0px 0 0; width:auto;"><?php CRenderer::RenderControl("areaList"); ?></table><br>
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
