<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>EVENT TV - EVENT КАТАЛОГ</title>
    <?php CRenderer::RenderControl("metadata"); ?>
<body>
<table border="0" cellpadding="0" cellspacing="0" style="width:100%; height:100%;">
<tr><td><?php CRenderer::RenderControl("topLine");?></td></tr>
<tr><!-- Заголовок-->
	<td style="padding-left: 30px; padding-right: 30px; padding-top: 16px;"><?php CRenderer::RenderControl("header"); ?></td>
</tr>
<tr><!--Меню-->
<td><?php CRenderer::RenderControl("menu"); ?>
<?php CRenderer::RenderControl("submenu"); ?>
<?php CRenderer::RenderControl("submenu1"); ?>
<?php CRenderer::RenderControl("submenu2"); ?>
<?php CRenderer::RenderControl("submenu3"); ?>
<?php CRenderer::RenderControl("submenu4"); ?>
</td>
</tr>
<tr><td style="padding-left: 30px; padding-right: 30px; padding-top: 10px; height: 100%" valign="top">
	<table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="vertical-align:top;width:230px">
			<table cellpadding="0" cellspacing="0" border="0" style="width: 218px; border: 1px solid #dadada;">
			
			<tr>

				<td class="ram5"> 
				<div style="margin: 0 0 3px 0;" class="eventotekaMenu_title"><a href="/eventtv" class="black">EVENT TV</a></div><?php CRenderer::RenderControl("topicList"); ?>
				
				<div style="margin: 0 0 3px 0;" class="eventotekaMenu_title"><a href="/book" class="black">Event энциклопедия</a></div><?php CRenderer::RenderControl("bookTopicsList"); ?>
				</td>

			</tr>
			
			</table>
			<img src="/images/front/0.gif" width="220" height="10">
		</td>
		<td valign="top">
                        <div class="recomendTitle eventoteka h3">Эвентотека / Эвент ТВ <a target="_blank" class="yaWitgetLink" id="eventotekaYaWitget" href="http://www.yandex.ru/?add=83172&from=promocode">Виджет Эвентотеки на Яндексе</a></div>
			<div class="bookListDir"><table cellpadding="0" cellspacing="0" border="0"><?php CRenderer::RenderControl("publicList"); ?></table></div>
			<div style="padding-top:15px;"><?php CRenderer::RenderControl("details"); ?></div>
		</td>
                <?php require ROOTDIR.'templates/_leftMenuWitgets.php'; ?>
	</tr>
	</table>
<tr><td class="partner_ban"><?php CRenderer::RenderControl("bottomBanners"); ?></td></tr>
<tr><td class="foot"><?php CRenderer::RenderControl("footer"); ?></td></tr>
</table>
<?php CRenderer::RenderControl("googleanalytics"); ?>
</body>
</html>
