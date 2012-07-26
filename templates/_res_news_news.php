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
<td><?php CRenderer::RenderControl("menu"); ?></td>
</tr>
<tr>
	<td><?php CRenderer::RenderControl("submenu"); ?></td>
</tr>
<tr><td style="padding-left: 30px; padding-right: 30px; padding-top: 16px; height: 100%" valign="top">
	<table width="100%">
		<tr>
			<td width="230" style="vertical-align:top; padding-left:5px;  width:230px">
				 <table cellpadding="0" cellspacing="0" border="0"  style="width: 218px; border: 1px solid #dadada;">				  
					<tr><td class="ram5"><?php CRenderer::RenderControl("typeList"); ?></td></tr>					
				</table>
				<a id="witgetAddResident" href="/add_res_news">Добавить свою новость</a>
			</td>
			<td>
				<div>	
					<?php CRenderer::RenderControl("details"); ?>
					<div style="margin-top: 20px;">
						<table>
						<tr>
							<td valign="top" width=200>								 
								<div style="margin-top:15px; margin-bottom:3px;"><?php CRenderer::RenderControl("newContractors"); ?></div>	
								<a href="/resident_news" {show_contractor} style="color:#f05620;">Все новости компаний</span>
							</td>
							<td valign="top" width=200>
								<div style="margin-top:15px; margin-bottom:3px;"><?php CRenderer::RenderControl("newAreas"); ?></div>	
								<a href="/resident_news" {show_area} style="color:#3399ff;">Все новости площадок</span>
							</td>
							<td valign="top" width=200>
								<div style="margin-top:15px; margin-bottom:3px;"><?php CRenderer::RenderControl("newArtists"); ?></div>
								<a href="/resident_news" {show_artist} style="color:#ff0066;">Все новости артистов</span>
							</td>
							<td valign="top" width=200>
								<div style="margin-top:15px; margin-bottom:3px;"><?php CRenderer::RenderControl("newAgencies"); ?></div>
								<a href="/resident_news" {show_agency} style="color:#99cc00;">Все новости агентств</span>
							</td>
						</tr>
						</table>
					</div>
					<h4 style="display: none" class="detailsBlockTitle noDisplay"><a name="photos">Другие новости этого резидента</a></h4>
					<div style="display: none"><?php CRenderer::RenderControl("otherNews"); ?></div>
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
