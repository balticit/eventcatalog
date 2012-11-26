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
	<div class="h3">Последние добавления</div>
	
	<div><?php // CRenderer::RenderControl("chart"); ?></div>

					<br />
					<table width="100%" cellspacing="0" cellpadding="0" border="0">
						<tr>
							<td width="25%" valign="top" style="padding-right:8px">
								<span class="newreg_contractor" style="font-size: 14px; text-decoration: none;"><b>Новое в подрядчиках</b></span><br /><br />
								<div>Добавилось за последнюю неделю:&nbsp;<b><?php CRenderer::RenderControl("contractorcount"); ?></b><br /><br />
								<table class="recomended"><?php CRenderer::RenderControl("newContractorsweek"); ?></table></div>	
							</td>
							<td width="25%" valign="top" style="padding-right:8px">
								<span class="newreg_area" style="font-size: 14px; text-decoration: none;"><b>Новое в площадках</b></span><br /><br />
								<div>Добавилось за последнюю неделю:&nbsp;<b><?php CRenderer::RenderControl("areacount"); ?></b><br /><br />
								<table class="recomended"><?php CRenderer::RenderControl("newAreasweek"); ?></table></div>
							</td>
							<td width="25%" valign="top" style="padding-right:8px">
								<span class="newreg_artist" style="font-size: 14px; text-decoration: none;"><b>Новое в артистах</b></span><br /><br />
								<div>Добавилось за последнюю неделю:&nbsp;<b><?php CRenderer::RenderControl("artistcount"); ?></b><br /><br />
								<table class="recomended"><?php CRenderer::RenderControl("newArtistsweek"); ?></table></div>
							</td>
							<td width="25%" valign="top" style="padding-right:8px">
								<span class="newreg_agency" style="font-size: 14px; text-decoration: none; "><b>Новое в Агентствах</b></span><br /><br />
								<div>Добавилось за последнюю неделю:&nbsp;<b><?php CRenderer::RenderControl("agencycount"); ?></b><br /><br />
								<table class="recomended"><?php CRenderer::RenderControl("newAgenciesweek"); ?></table></div>
							</td>
						</tr>
						
						<tr>
							<td valign="top">Добавилось подрядчиков за месяц:&nbsp;<b><?php CRenderer::RenderControl("contractorcountmonth"); ?></b> </td>
							<td valign="top">Добавилось площадок за месяц:&nbsp;<b><?php CRenderer::RenderControl("areacountmonth"); ?></b></td>
							<td valign="top">Добавилось артистов за месяц:&nbsp;<b><?php CRenderer::RenderControl("artistcountmonth"); ?></b></td>
							<td valign="top">Добавилось агентств за месяц:&nbsp;<b><?php CRenderer::RenderControl("agencycountmonth"); ?></b></td>
						</tr>
						<tr>
						  <td colspan="4"><br />
              <div id="contractormonth_count" >
                <a style="color:#000; font-size:14px;" href="/added" class="newreg_contractor" onClick="show_month(); return false;">Открыть список добавленных за последний месяц</a>
              </div>
              <div id="contractormonth1" style="display:none;">
								<a href="/added" class="newreg_contractor" style="color:#000; font-size:14px;" onClick="off_month(); return false;">Свернуть список добавленных за последний месяц</a>
							</div>
              
						</tr>
						<tr>
							<td valign="top" style="padding-right:8px">

								<div id="contractormonth" style="display:none;">
								<br>
								<table class="recomended"><?php CRenderer::RenderControl("newContractorsmonth"); ?></table><br /></div></td>
							<td valign="top" style="padding-right:8px">
							
								<div id="areamonth_count" style="display:none;"><a href="/added" style="display:none;" class="newreg_area" onClick="show_month(); return false;">открыть список</a></div>
								<div id="areamonth" style="display:none;">
									<a href="/added" style="display:none;" class="newreg_area" onClick="off_month(); return false;">свернуть список</a><br>
								  <table class="recomended"><?php CRenderer::RenderControl("newAreasmonth"); ?></table><br /></div></td>
							<td valign="top" style="padding-right:8px">
							
								<div id="artistmonth_count" style="display:none;"><a href="/added" style="display:none;" class="newreg_artist" onClick="show_month(); return false;">открыть список</a></div>
								<div id="artistmonth" style="display:none;">
								<a href="/added" style="display:none;" class="newreg_artist" onClick="off_month(); return false;">свернуть список</a><br>
								<table class="recomended"><?php CRenderer::RenderControl("newArtistsmonth"); ?></table><br /></div></td>
							<td valign="top" style="padding-right:8px"><script type="text/javascript">
									function show_month() {
										document.getElementById('agencymonth').style.display = 'block';
										document.getElementById('agencymonth_count').style.display = 'none';	
                    								
                    document.getElementById('artistmonth').style.display = 'block';
										document.getElementById('artistmonth_count').style.display = 'none';
										
										document.getElementById('areamonth').style.display = 'block';
										document.getElementById('areamonth_count').style.display = 'none';
										
										document.getElementById('contractormonth').style.display = 'block';
										document.getElementById('contractormonth1').style.display = 'block';
										document.getElementById('contractormonth_count').style.display = 'none';
									}
									function off_month() {
										document.getElementById('agencymonth').style.display = 'none';
										document.getElementById('agencymonth_count').style.display = 'block';
										
										document.getElementById('artistmonth').style.display = 'none';
										document.getElementById('artistmonth_count').style.display = 'block';
                    
                    document.getElementById('areamonth').style.display = 'none';
										document.getElementById('areamonth_count').style.display = 'block';
                    
                    document.getElementById('contractormonth').style.display = 'none';
                    document.getElementById('contractormonth1').style.display = 'none';
										document.getElementById('contractormonth_count').style.display = 'block';									
									}
								</script>
								<div id="agencymonth_count" style="display:none;"><a href="/added" style="display:none;" class="newreg_agency" onClick="show_month(); return false;">открыть список</a></div>
								<div id="agencymonth" style="display:none;">
								<a href="/added" style="display:none;" class="newreg_agency" onClick="off_month(); return false;">свернуть список</a><br>
								<table class="recomended"><?php CRenderer::RenderControl("newAgenciesmonth"); ?></table><br /></div></td>
						</tr>
					</table>
</td></tr>
<tr><td class="partner_ban"><?php CRenderer::RenderControl("bottomBanners"); ?></td></tr>
<tr><td class="foot"><?php CRenderer::RenderControl("footer"); ?></td></tr>
</table>
<?php CRenderer::RenderControl("googleanalytics"); ?>
</body>
</html>
