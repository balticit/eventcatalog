<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Профиль - EVENT КАТАЛОГ</title>
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
<tr><td style="padding-left: 30px; padding-right: 30px; padding-top: 16px;  padding-bottom: 30px; height:100%;" valign="top">
	<table width="100%" cellpadding = "0" cellspacing = "0" border="0">
	<tr>
		<td width="130" valign="top"><?php echo $this->logo_a_start; ?><img src="<?php echo $this->logo_link; ?>" class="logo120border"/><?php echo $this->logo_a_end; ?></td>
		<td valign="top"><span style="font-size:24px; color:#666666; font-weight:bold;"><?php echo $this->user_name; ?></span>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 11px; "><a href="<?php echo $this->msg_link; ?>" <?php echo $this->msg_auth; ?> style="color:#999999;">Написать личное сообщение</a></span><br />
		<span style="font-size:12px; color:#666666;"><?php echo $this->user_types; ?></span></td>
	</tr>
	<tr><td></td>
	<td valign="top"><?php echo $this->user_info; ?></td>
	</tr>
	</table>
</td></tr>
<tr><td class="foot"><?php CRenderer::RenderControl("footer"); ?></td></tr>
</table>
<?php CRenderer::RenderControl("googleanalytics"); ?>
</body>
</html>
