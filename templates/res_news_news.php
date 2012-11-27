<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Новости резидентов - EVENT КАТАЛОГ</title>
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
<?php CRenderer::RenderControl("submenu5"); ?>
</td>
</tr>
<tr><td style="padding-left: 30px; padding-right: 30px; padding-top: 16px; height: 100%" valign="top">
	<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td width="230" style="vertical-align:top;width:230px">
				 <table cellpadding="0" cellspacing="0" border="0"  style="width: 218px; border: 1px solid #dadada;">				  
					<tr><td class="ram5"><div class="eventotekaMenu_title" style="margin: 0 0 0 0;"><a class="black" href="/resident_news">Новости резидентов</a></div><?php CRenderer::RenderControl("typeList"); ?></td></tr>					
				</table>
				<img src="/images/front/0.gif" width="220" height="10">
				<a id="witgetAddResident" href="/add_res_news">Добавить свою новость</a>
			</td>
			<td valign="top">
				<div style="margin-top:-8px">	
					<?php CRenderer::RenderControl("details"); ?>					
					<h4 class="detailsBlockTitle"><a name="photos">Другие новости этого резидента</a></h4>
					<div><?php CRenderer::RenderControl("otherNews"); ?></div>
				</div>
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
