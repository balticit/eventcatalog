<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>EVENT INDEX</title>
	<?php CRenderer::RenderControl("metadata"); ?>	
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
<tr><td style="padding-left: 30px; padding-right: 30px; padding-top: 16px; height: 100%" valign="top">
<table width="100%">
<tr>
	<td width="79" valign="top" style="padding-top: 15px;"><a href="/search/"><img src="/images/search/eventindex0.gif"></a></td>
	<td>
		<div class="yandexform" onclick="return {'bg': '#ffcc00', 'language': 'ru', 'encoding': '', 'suggest': false, 'tld': 'ru', 'site_suggest': false, 'webopt': false, 'fontsize': 16, 'arrow': false, 'fg': '#000000', 'logo': 'rb', 'websearch': false, 'type': 2}"><form action="http://eventcatalog.ru/search/" method="get"><input type="hidden" name="searchid" value="1840054"/><input name="text"/><input type="submit" value="?????"/></form></div><script type="text/javascript" src="http://site.yandex.net/load/form/1/form.js" charset="utf-8"></script>			
	</td>
</tr>
<tr>
</tr>
</table>
</td></tr>
<tr><td class="partner_ban"><?php CRenderer::RenderControl("bottomBanners"); ?></td></tr>
<tr><td class="foot"><?php CRenderer::RenderControl("footer"); ?></td></tr>
</table>
<?php CRenderer::RenderControl("googleanalytics"); ?>
<script>
	document.getElementById('searchinput').focus();
</script>
</body>
</html>