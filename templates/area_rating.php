<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>������� �������� - EVENT �������</title>
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
<tr><!--����������-->
<td style="padding-left: 30px; padding-right: 30px; padding-top: 10px;">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="vertical-align:top;width:220px">
			<table cellpadding="0" cellspacing="0" border="0" width="241" >
				<tr>
				<td class="ram5"><?php CRenderer::RenderControl("typeList"); ?></td>
				</tr>
			</table>
			<img src="/images/front/0.gif" width="220" height="10">
            <a class="addArea" href="/registration/?type=area">�������� ��������</a>
		</td>
		<td style="vertical-align:top">
			<table cellspacing="10">
			<tr>
				<td style="padding-right: 30px;"><a class="toplinkscon" href="/contractor/rating" style="font-size:12px;">������� �����������</a></td>
				<td style="color:#3399ff; font-size:12px; font-weight: bold; padding-right: 30px;">������� ��������</a></td>
				<td style="padding-right: 30px;"><a class="toplinksartist" href="/artist/rating" style="font-size:12px;">������� ��������</a></td>
				<td style="padding-right: 30px;"><a class="toplinksagent" href="/agency/rating" style="font-size:12px;">������� ��������</a></td>
			</tr>
			</table>
			<div style="padding-top:5px; padding-left:5px;">
				<table border="0" cellpadding="0" cellspacing="10"><?php CRenderer::RenderControl("rateList"); ?></table>
			</div>
			<p class="text"><?php CRenderer::RenderControl("pager"); ?></p><br />
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
