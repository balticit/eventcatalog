<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>������� ��������� - EVENT �������</title>
	<?php CRenderer::RenderControl("metadata"); ?>
</head>
<body>
	<table border="0" cellpadding="0" cellspacing="0" style="width:100%; height:100%;">
		<tr><td><?php CRenderer::RenderControl("topLine");?></td></tr>
		<tr><!-- ���������-->
			<td style="padding-left: 30px; padding-right: 30px; padding-top: 16px;"><?php CRenderer::RenderControl("header"); ?></td>
		</tr>
		<tr><!--����-->
		<td><?php CRenderer::RenderControl("menu"); ?></td>
		</tr>
		<tr>
			<td><?php CRenderer::RenderControl("submenu"); ?></td> 
		</tr>
		<tr><td style="padding-left: 30px; padding-right: 30px; padding-top: 10px; padding-bottom: 16px; height: 100%" valign="top">
				<table>
					<tr>
						<td style="vertical-align:top; padding-left:5px;  width:230px">
								<table cellpadding="0" cellspacing="0" border="0"  style="width: 218px; border: 1px solid #dadada;">
									<tr>
										<td class="ram5">
											<div style="margin:0;" class="eventotekaMenu_title">
												<a href="javascript:void(9);" class="black">����</a>
											</div>
											<?php CRenderer::RenderControl("newsCategoriesList"); ?>
										</td>
									</tr>
								</table>
								<img src="/images/front/0.gif" width="220" height="10">
						</td>
						<td id="asdf" width="100%" valign="top">
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
</body>
</html>
