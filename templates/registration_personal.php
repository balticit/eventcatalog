<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>����������� - EVENT �������</title>
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
<tr><td style="padding-left: 30px; padding-right: 30px; padding-top: 16px;  padding-bottom: 30px; height:100%;" valign="top">

	<table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign="top" >
			<div style="padding-left:15px;">
				<div style="padding-right: 15px;padding-left:20px;">
					<?php CRenderer::RenderControl("errors"); ?>
					<?php CRenderer::RenderControl("personal"); ?>
				</div>
			</div>
		</td>
	</tr>
	</table>
</td></tr>
<tr><td class="foot"><?php CRenderer::RenderControl("footer"); ?></td></tr>
</table>
<?php CRenderer::RenderControl("googleanalytics"); ?>
</body>
</html>
