<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Барахолка - <?php CRenderer::RenderControl("title"); ?> - EVENT КАТАЛОГ</title>
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

	<table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="vertical-align:top; padding-left:5px;  width:220px">
			<table cellpadding="0" cellspacing="0" border="0" width="241">
				
				<td>
				<div style="height:32px; padding-top:3px; width:213px; " >
</div><div style="height:20px;"></div>
					<table cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td class="ram1">
						</td>
						<td class="ram2">
						</td>
						<td class="ram3">
						</td>
					</tr>
					<tr>
						<td class="ram4">
						</td>
						<td class="ram5">
						<?php CRenderer::RenderControl("junkTypeList"); ?>
						</td>
						<td class="ram6">
						</td>
					</tr>
					<tr>
						<td class="ram7">
						</td>
						<td class="ram8">
						</td>
						<td class="ram9">
						</td>
					</tr>
					</table>
                                        <a id="witgetAddResident" href="/registration/?type=user">Добавить резидента</a>
				</td>
				</tr>
				
				<tr>
				<td>
					<div style="font-weight:bold; font-size:11px; color:#999; font-family:Tahoma; margin-top:30px; white-space:nowrap;"><?php CRenderer::RenderControl("leftBanner1"); ?><br/><br><br><?php CRenderer::RenderControl("leftBanner2"); ?><br/><br><br><?php CRenderer::RenderControl("leftBanner3"); ?>&nbsp;</div>
                                                        </div>
				</td>
                                <?php require ROOTDIR.'templates/_leftMenuWitgets.php'; ?>
				</tr>
			</table>
		</td>
		<td valign="top" >
								
<div align="center" style="padding-left:24px;">

	<table border="0" cellpadding="0" cellspacing="0" >
		
		<tr>
			<td valign="center" align="left">
														<a class="regis_top" href="/registration/junksale/type/sale" style="color:#717171;">Разместить объявление о продаже</a>			</td>
			<td valign="center" align="left">
														<a class="regis_top" href="/registration/junksale/type/buy" style="color:#717171;">Разместить объявление о покупке</a>			</td>
			</tr>											
	</table>

</div>
									


		<table width="100%">
		<tr>
			<?php CRenderer::RenderControl("sellLink"); ?>
			<td style="width:0px">&nbsp;</td>
			<?php CRenderer::RenderControl("searchLink"); ?>
		
		</tr>
		</table>
		<?php CRenderer::RenderControl("details"); ?>


	


							</td>
		</tr>
	
	
	</table>
</td></tr>
<tr><td class="partner_ban"><?php CRenderer::RenderControl("bottomBanners"); ?></td></tr>
<tr><td class="foot"><?php CRenderer::RenderControl("footer"); ?></td></tr>
</table>
<?php CRenderer::RenderControl("googleanalytics"); ?>
</body>
</html>
