<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title><?php CRenderer::RenderControl("title"); ?></title>
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
<tr>
    <?php if($this->is_activity){ ?>
        <td style="padding-left: 30px; padding-right: 30px; padding-top: 10px;" valign="top">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
				<td style="vertical-align:top; width:230px">  
				<table cellpadding="0" cellspacing="0" border="0" style="width: 218px; border: 1px solid #dadada;">
			
				<tr>			
					<td class="ram5"><?php CRenderer::RenderControl("activityList"); ?></td>					
				</tr>
			
				</table>
                <img src="/images/front/0.gif" width="220" height="10">
			    <a class="addAgency" href="/registration/?type=agency">Добавить Агентство</a>
                </td>
                <td style="vertical-align:top">
					<div style="padding-left: 5px;">
					<?php CRenderer::RenderControl("yaListTop"); ?>
					<?php CRenderer::RenderControl("titlefilterLinks"); ?>
					<table border="0" cellpadding="0" cellspacing="0" class="tableInline" style="margin: 0 0px 0 0; width:auto;"><?php CRenderer::RenderControl("agencyList"); ?></table>
                    <p class="text"><?php CRenderer::RenderControl("pager"); ?></p><br>
                    <?php CRenderer::RenderControl("footerText"); ?>
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
<?php }?>
<?php if(!$this->is_activity){ ?>
    <td style="padding: 10px 30px 10px 30px; height:100%" valign="top">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td width="225" style="vertical-align:top;width:230px">
			<table cellpadding="0" cellspacing="0" border="0" style="width: 218px; border: 1px solid #dadada;">			
			<tr>				
				<td class="ram5"><?php CRenderer::RenderControl("activityList"); ?></td>				
			</tr>			
			</table>
			<img src="/images/front/0.gif" width="220" height="10">
            <a class="addAgency" href="/registration/?type=agency">Добавить Агентство</a>
		</td>
                
		<td style="vertical-align:top;  padding-left: 5px;">
			<?php CRenderer::RenderControl("yaPersonal"); ?>
			<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr><td valign="top"><?php CRenderer::RenderControl("agenDetails"); ?></td></tr></table>
			

			
		</td>
                <?php require ROOTDIR.'templates/_leftMenuWitgets.php'; ?>
	</tr>
	</table>
</td></tr>
<tr><td class="partner_ban"><?php CRenderer::RenderControl("bottomBanners"); ?></td></tr>
<tr><td class="foot"><?php CRenderer::RenderControl("footer"); ?></td></tr>
</table>
<?php CRenderer::RenderControl("googleanalytics"); ?>
<?php }?>

<script type="text/javascript">
$(function() {
  $(".ui-dialog-titlebar-close, .ui-dialog-buttonset button").live('click',function(){
    $('.dialog-confirm').hide();
  });
});
</script>



</body>
</html>
