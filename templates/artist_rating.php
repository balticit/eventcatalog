<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Рейтинг артистов - EVENT КАТАЛОГ</title>
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
<td style="padding-left: 30px; padding-right: 30px; padding-top: 10px;" valign="top">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="vertical-align:top;width:230px">
			<table cellpadding="0" cellspacing="0" border="0" style="width: 218px; border: 1px solid #dadada;">			
			<tr>
				<td class="ram5"><?php CRenderer::RenderControl("groupList"); ?></td>				
			</tr>			
			</table>
			<img src="/images/front/0.gif" width="220" height="10">
            <a class="addArtist" href="/registration/?type=artist">Добавить Артиста</a>
		</td>
		<td style="vertical-align:top">
				<table cellspacing="10">
					<tr>
						<td style="padding-right: 30px;"><a  class="toplinkscon" href=/contractor/rating style="font-size:12px;">Рейтинг подрядчиков</a></td>
						<td style="padding-right: 30px;"><a  class="toplinksarea" href=/area/rating style="font-size:12px;">Рейтинг площадок</a></td>
						<td style="color:#E3007B; font-size:12px; font-weight: bold;padding-right: 30px;">Рейтинг артистов</td>
						<td style="padding-right: 30px;"><a class="toplinksagent" href=/agency/rating style="font-size:12px;">Рейтинг агентств</a></td>
					</tr>
				</table>
				<div style="padding-top:5px; padding-left:5px;">
						<table border="0" cellpadding="0" cellspacing="10"><?php CRenderer::RenderControl("rateList"); ?></table>
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
