<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Рейтинг подрядчиков - EVENT КАТАЛОГ</title>
	<?php CRenderer::RenderControl("metadata"); ?>
</head>
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
<tr><!--содержание-->
<td style="padding-left: 30px; padding-right: 30px; padding-top: 10px;">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="vertical-align:top; padding-left:5px; width:230px">
			<table width="218" cellpadding="0" cellspacing="0" border="0" style="width: 218px; border: 1px solid #dadada;">
			<!-- <tr><td class="ram1"></td><td class="ram2"></td><td class="ram3"></td></tr> -->
			<tr>
				<!-- <td class="ram4"></td> -->
				<td class="ram5"><?php CRenderer::RenderControl("actList"); ?></td>
				<!-- <td class="ram6"></td> -->
			</tr>
			<!-- <tr><td class="ram7"></td><td class="ram8"></td><td class="ram9"></td></tr> -->
			</table>
			<img src="/images/front/0.gif" width="220" height="10">
             <a class="addConstractor" href="/registration/?type=contractor">Добавить Подрядчика</a>
		</td>
		<td style="vertical-align:top">
			<div class="h3">Рейтинг</div>
			<table cellspacing="10">
			<tr>
				<td style="color:#f05620; font-size: 12px; font-weight: bold;padding-right: 30px;">Рейтинг подрядчиков</td>
				<td style="padding-right: 30px;"><a class="toplinksarea" href="/area/rating" style="font-size:12px;">Рейтинг площадок</a></td>
				<td style="padding-right: 30px;"><a class="toplinksartist" href="/artist/rating" style="font-size:12px;">Рейтинг артистов</a></td>
				<td style="padding-right: 30px;"><a class="toplinksagent" href="/agency/rating" style="font-size:12px;">Рейтинг агентств</a></td>
			</tr>
			</table>
			<div style="padding-top:5px; padding-left:5px;">
				<table border="0" cellpadding="0" cellspacing="10"><?php CRenderer::RenderControl("rateList"); ?></table>
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
