<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>������� ����������� - EVENT �������</title>
	<?php CRenderer::RenderControl("metadata"); ?>
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" style="width:100%; height:100%;">
<tr>
  <td><?php CRenderer::RenderControl("topLine");?></td></tr>
<tr><!-- ���������-->
  <td style="padding-left: 30px; padding-right: 30px; padding-top: 16px;"><?php CRenderer::RenderControl("header"); ?></td>
</tr>
<tr><!--����-->
  <td>
    <?php CRenderer::RenderControl("menu"); ?>
    <?php CRenderer::RenderControl("submenu"); ?>
  </td>
</tr>
<tr><!--����������-->
<td style="padding-left: 30px; padding-right: 30px; padding-top: 10px;">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="vertical-align:top; padding-left:5px; width:230px ">

<table cellpadding="0" cellspacing="0" border="0" style="width: 218px; border: 1px solid #dadada;">
  <tr>
    <td class="ram5">
      <div class="eventotekaMenu_title" style="margin: 0 0 0 0;">
        <a class="black" href="/rating">�������� ��������</a>
      </div>
      <div><a style="color:#000000; margin-left: 0;" class="leftml2 leftmgray" href="/rating">��� ��������</a></div>
      <div class="selected"><a style="color:#f05620; margin-left: 0;" class="leftml2 leftmgray" href="/rating/contractor">������� �����������</a></div>
      <div><a style="color:#3399ff; margin-left: 0;" class="leftml2 leftmgray" href="/rating/area">������� ��������</a></div>
      <div><a style="color:#E3007B; margin-left: 0;" class="leftml2 leftmgray" href="/rating/artist">������� ��������</a></div>
      <div><a style="color:#99cc00; margin-left: 0;" class="leftml2 leftmgray" href="/rating/agency">������� ��������</a></div>
    </td>
  </tr>
</table>


		</td>
		<td style="vertical-align:top;">
			<div class="h3">������� �����������</div>
			
			<div style="padding-top:5px; ">
			  
  			<table border="0" cellpadding="0" cellspacing="0" width="100%"><?php CRenderer::RenderControl("rateList"); ?></table>
  				
			</div>
			
      <br />
      <p><?php // CRenderer::RenderControl("pager"); ?></p>
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
