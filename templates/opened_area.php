<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Новые площадки Москвы - EVENT КАТАЛОГ</title>
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
	<div style="padding-left:15px;">
        <table width="100%">
        <tr>
            <td valign="top">
                    <table cellpadding="0" cellspacing="10" border="0"><?php CRenderer::RenderControl("newsList"); ?></table>
                <div style="padding-left: 20px;">                
                    <a href="/opened_area_full" style="color:#0099FF;">Архив Открытий</a><Br>
                    <!--<?php CRenderer::RenderControl("archive"); ?>-->
                    <br>
                    <a href="/resident_news" style="color:#0099FF;">Новости площадок</a>
                </div><Br><br>
            </td>
        </tr>
        </table>
    </div>
</td></tr>
<tr><td class="partner_ban"><?php CRenderer::RenderControl("bottomBanners"); ?></td></tr>
<tr><td class="foot"><?php CRenderer::RenderControl("footer"); ?></td></tr>
</table>
<?php CRenderer::RenderControl("googleanalytics"); ?>
</body>
</html>
