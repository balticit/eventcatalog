<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Добавилось - EVENT КАТАЛОГ</title>
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
	<div class="h3">Последние добавления</div>
	<p>&nbsp;</p>
	<div><?php CRenderer::RenderControl("chart"); ?></div>
	<br><br><br><br><br><br><br>
					
					<table>
						<tr>
							<td valign="top">
								<span class="newreg_contractor"><b>Новое в подрядчиках</b></span>
								<div style="margin-top:15px; margin-bottom:3px;">Добавилось за последнюю неделю:&nbsp;<span style="font-weight:bold"><?php CRenderer::RenderControl("contractorcount"); ?></span>
								<br><br><?php CRenderer::RenderControl("newContractorsweek"); ?></div>	
							</td>
							<td style="padding-left: 40px;" valign="top">
								<span class="newreg_area"><b>Новое в площадках</b></span>
								<div style="margin-top:15px; margin-bottom:3px;">Добавилось за последнюю неделю:&nbsp;<span style="font-weight:bold"><?php CRenderer::RenderControl("areacount"); ?></span>
								<br><br><?php CRenderer::RenderControl("newAreasweek"); ?></div>	
							</td>
							<td style="padding-left: 40px;" valign="top">
								<span class="newreg_artist"><b>Новое в артистах</b></span>
								<div style="margin-top:15px; margin-bottom:3px;">Добавилось за последнюю неделю:&nbsp;<span style="font-weight:bold"><?php CRenderer::RenderControl("artistcount"); ?></span>
								<br><br><?php CRenderer::RenderControl("newArtistsweek"); ?></div>
							</td>
							<td style="padding-left: 40px;" valign="top">
								<span class="newreg_agency"><b>Новое в Агентствах</b></span>
								<div style="margin-top:15px; margin-bottom:3px;">Добавилось за последнюю неделю:&nbsp;<span style="font-weight:bold"><?php CRenderer::RenderControl("agencycount"); ?></span>
								<br><br><?php CRenderer::RenderControl("newAgenciesweek"); ?></div>	
							</td>
						</tr>
						<tr>
							<td valign="top"><Br>Добавилось за месяц всего:&nbsp;<span style="font-weight:bold"><?php CRenderer::RenderControl("contractorcountmonth"); ?></span> </td>
							<td style="padding-left: 40px;" valign="top"><Br>Добавилось за месяц всего:&nbsp;<span style="font-weight:bold"><?php CRenderer::RenderControl("areacountmonth"); ?></span></td>
							<td style="padding-left: 40px;" valign="top"><Br>Добавилось за месяц всего:&nbsp;<span style="font-weight:bold"><?php CRenderer::RenderControl("artistcountmonth"); ?></span></td>
							<td style="padding-left: 40px;" valign="top"><Br>Добавилось за месяц всего:&nbsp;<span style="font-weight:bold"><?php CRenderer::RenderControl("agencycountmonth"); ?></span></td>
						</tr>
						<tr>
							<td valign="top"><script type="text/javascript">
									function show_contractors_month() {
										document.getElementById('contractormonth').style.display = 'block';
										document.getElementById('contractormonth_count').style.display = 'none';									
									}
									function off_contractors_month() {
										document.getElementById('contractormonth').style.display = 'none';
										document.getElementById('contractormonth_count').style.display = 'block';									
									}
								</script>
								<div id="contractormonth_count"><a href="/added" class="newreg_contractor" onClick="show_contractors_month(); return false;">открыть список</a></div>
								<div id="contractormonth" style="display:none;">
								<a href="/added" class="newreg_contractor" onClick="off_contractors_month(); return false;">свернуть список</a><Br><br>
								<?php CRenderer::RenderControl("newContractorsmonth"); ?></div>
								</td>
							<td style="padding-left: 40px;" valign="top"><script type="text/javascript">
									function show_areas_month() {
										document.getElementById('areamonth').style.display = 'block';
										document.getElementById('areamonth_count').style.display = 'none';									
									}
									function off_areas_month() {
										document.getElementById('areamonth').style.display = 'none';
										document.getElementById('areamonth_count').style.display = 'block';									
									}
								</script>
								<div id="areamonth_count"><a href="/added" class="newreg_area" onClick="show_areas_month(); return false;">открыть список</a></div>
								<div id="areamonth" style="display:none;">
									<a href="/added" class="newreg_area" onClick="off_areas_month(); return false;">свернуть список</a><Br><br>
								<?php CRenderer::RenderControl("newAreasmonth"); ?></div></td>
							<td style="padding-left: 40px;" valign="top"><script type="text/javascript">
									function show_artists_month() {
										document.getElementById('artistmonth').style.display = 'block';
										document.getElementById('artistmonth_count').style.display = 'none';									
									}
									function off_artists_month() {
										document.getElementById('artistmonth').style.display = 'none';
										document.getElementById('artistmonth_count').style.display = 'block';									
									}
								</script>
								<div id="artistmonth_count"><a href="/added" class="newreg_artist" onClick="show_artists_month(); return false;">открыть список</a></div>
								<div id="artistmonth" style="display:none;">
								<a href="/added" class="newreg_artist" onClick="off_artists_month(); return false;">свернуть список</a><Br><br>
								<?php CRenderer::RenderControl("newArtistsmonth"); ?></div></td>
							<td style="padding-left: 40px;" valign="top"><script type="text/javascript">
									function show_agencies_month() {
										document.getElementById('agencymonth').style.display = 'block';
										document.getElementById('agencymonth_count').style.display = 'none';									
									}
									function off_agencies_month() {
										document.getElementById('agencymonth').style.display = 'none';
										document.getElementById('agencymonth_count').style.display = 'block';									
									}
								</script>
								<div id="agencymonth_count"><a href="/added" class="newreg_agency" onClick="show_agencies_month(); return false;">открыть список</a></div>
								<div id="agencymonth" style="display:none;">
								<a href="/added" class="newreg_agency" onClick="off_agencies_month(); return false;">свернуть список</a><Br><br>
								<?php CRenderer::RenderControl("newAgenciesmonth"); ?></div></td>
						</tr>
					</table>
</td></tr>
<tr><td class="partner_ban"><?php CRenderer::RenderControl("bottomBanners"); ?></td></tr>
<tr><td class="foot"><?php CRenderer::RenderControl("footer"); ?></td></tr>
</table>
<?php CRenderer::RenderControl("googleanalytics"); ?>
</body>
</html>
