<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <?php if(!$this->is_type){ ?>
        <title><?php CRenderer::RenderControl("title"); ?></title>
		<script language="javascript"><?php CRenderer::RenderControl("jsArea"); ?></script>
		<link rel="stylesheet" type="text/css" href="/styles/fm.css">
    <?php } ?>
    <?php if($this->is_type){ ?>
        <title><?php CRenderer::RenderControl("titlefilter"); ?>������� �������� - EVENT �������</title>
		<script language="javascript"><?php CRenderer::RenderControl("jsArea"); ?></script>
		<link rel="stylesheet" type="text/css" href="/styles/fm.css">
    <?php } ?>
	<?php CRenderer::RenderControl("metadata"); ?>
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" style="width:100%; height:100%;">
<tr><td><?php CRenderer::RenderControl("topLine");?></td></tr>
<tr><!-- ���������-->
	<td style="padding-left: 30px; padding-right: 30px; padding-top: 16px;"><?php CRenderer::RenderControl("header"); ?></td>
</tr>
<tr><!--����-->
<td><?php CRenderer::RenderControl("menu"); ?>
<?php CRenderer::RenderControl("submenu"); ?>
<?php CRenderer::RenderControl("submenu1"); ?>
<?php CRenderer::RenderControl("submenu2"); ?>
<?php CRenderer::RenderControl("submenu3"); ?>
<?php CRenderer::RenderControl("submenu4"); ?>
</td>
</tr>
<tr>
    <?php if($this->is_type){ ?>
    <td style="padding-left: 30px; padding-right: 30px; padding-top: 10px;" valign="top">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td style="vertical-align:top; width:230px">
				<table cellpadding="0" cellspacing="0" border="0" style="width: 218px; border: 1px solid #dadada;">
                        <tr>
                            <td class="ram5"><?php CRenderer::RenderControl("typeList"); ?></td>
						</tr>                        
                </table>
                    <img src="/images/front/0.gif" width="220" height="10">
					<a class="addArea" href="/registration/?type=area">�������� ��������</a>
                </td>
                <td style="vertical-align:top">
					<div>
						<form method="get" style="display: none" id="find_params"></form>
                        <?php CRenderer::RenderControl("currentFind"); ?>
					</div>
					<div>
                    <?php CRenderer::RenderControl("currentCapacity"); ?>
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
						<tr><?php CRenderer::RenderControl("capacityFilter"); ?></tr>
					</table>
					</div>
					
					
					
  <!--LIST RESIDENT-->
  <div class="resident-category-title"><?php CRenderer::RenderControl("titlefilterLinks"); ?></div>
  <div><?php CRenderer::RenderControl("footerText"); ?></div>
  <?php CRenderer::RenderControl("yaListTop"); ?>
  <div class="subcategory-resident-list contractor"><?php CRenderer::RenderControl("areaList"); ?></div>
  <div class="pager"><?php CRenderer::RenderControl("pager"); ?></div>
  <!--END LIST RESIDENT-->
					

                    
                    
                    
                    
                </td>
				<?php require ROOTDIR.'templates/_leftMenuWitgets.php'; ?>
            </tr>
        </table>
    </td></tr>
<tr><td class="partner_ban"><?php CRenderer::RenderControl("bottomBanners"); ?></td></tr>
    <tr><td class="foot"><?php CRenderer::RenderControl("footer"); ?></td></tr>
</table>
<?php CRenderer::RenderControl("googleanalytics"); ?>
    <?php } ?>
    <?php if(!$this->is_type){ ?>
    <td style="padding: 10px 30px 10px 30px;" valign="top">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="vertical-align:top; width:230px">
			<table cellpadding="0" cellspacing="0" border="0" style="width: 218px; border: 1px solid #dadada;">
			<tr>
				<td class="ram5"><?php CRenderer::RenderControl("typeList"); ?></td>
			</tr>			
			</table>
			<img src="/images/front/0.gif" width="220" height="10">
            <a class="addArea" href="/registration/?type=area">�������� ��������</a>
		</td>
		<td style="vertical-align:top; padding-left: 5px;">
			<?php CRenderer::RenderControl("yaPersonal"); ?>
			
			<?php CRenderer::RenderControl("details"); ?>
			
			
			
			
		</td>
    <?php require ROOTDIR.'templates/_leftMenuWitgets.php'; ?>
	</tr>
	</table>
</td></tr>
<tr><td class="partner_ban"><?php CRenderer::RenderControl("bottomBanners"); ?></td></tr>
<tr><td class="foot"><?php CRenderer::RenderControl("footer"); ?></td></tr>
</table>
<?php CRenderer::RenderControl("googleanalytics"); ?>
<?php } ?>

<script type="text/javascript">
$(function() {
  $(".ui-dialog-titlebar-close, .ui-dialog-buttonset button").live('click',function(){
    $('.dialog-confirm').hide();
  });
});
</script>
