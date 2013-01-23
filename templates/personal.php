<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Каталог персонала - EVENT КАТАЛОГ</title>
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

</td>
</tr>
<tr><td style="padding-left: 30px; padding-right: 30px; padding-top: 16px; height: 100%" valign="top">

	<table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="vertical-align:top;width:220px">
			<table cellpadding="0" cellspacing="0" border="0" width="241px">
				
				<td>
				<div style="height:32px; padding-top:3px; width:213px; " ></div><div style="height:20px;"></div>
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
						<?php CRenderer::RenderControl("personalTypeList"); ?>
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
								
<div align="left" style="padding-left:24px;">
<div style="position:absolute; z-index:50;" >
	<table border="0" cellpadding="0" cellspacing="0" >
		
		<tr>
			<td align="left" valign="center"><a class="regis_top" href="/registration/personal/type/vacancy" style="color: rgb(113, 113, 113);text-align:center; padding:4px 0px;">Добавить вакансию</a></td>
			<td align="left" valign="center"><a class="regis_top" href="/registration/personal/type/cv" style="color: rgb(113, 113, 113);text-align:center; padding:4px 0px;">Добавить резюме</a></td>				
		</tr>										
	</table>
</div>
</div>
	<br/><br/>						


		<div class="screen_content" >
									
			
	



<div class="left_list">

	

	<br>
<script type="text/javascript" language="javascript">
	function ClosePopup()
	{
		remove_PopUp('displayPopupWindow');
		return false;
	}
	function ShowPopup(id,title)
	{
	    var  v = window;
		var data =document.getElementById(id);
		create_PopUp('displayPopupWindow', title, document.getElementById(id+'cont').innerHTML, (document.body.clientWidth-data.clientWidth)/2, (document.body.clientHeight-data.clientHeight)/2, data.clientWidth, data.clientHeight, '#CCCCCC', '#FAFAFA');
		return false;
	}
</script>
<div class="left_list" style="padding-left:24px;">
<table border="0" cellpadding="0" cellspacing="0" class="tableInline" width="65%" >
	
	<?php CRenderer::RenderControl("vacancyList"); ?>
	<?php CRenderer::RenderControl("cvList"); ?>
</table>
</div>
</div
><p class="text"><?php CRenderer::RenderControl("pager"); ?></p>
								</div>


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
