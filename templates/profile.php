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
<td><?php CRenderer::RenderControl("menu"); ?>
<?php CRenderer::RenderControl("submenu"); ?>
<?php CRenderer::RenderControl("submenu1"); ?>
<?php CRenderer::RenderControl("submenu2"); ?>
<?php CRenderer::RenderControl("submenu3"); ?>
<?php CRenderer::RenderControl("submenu4"); ?>
<?php CRenderer::RenderControl("submenu5"); ?>
</td>
</tr>

<tr><td style="padding-left: 30px; padding-right: 30px; padding-top: 16px;  padding-bottom: 30px; height:100%;" valign="top">
	<table width="100%" cellpadding = "0" cellspacing = "0" border="0">
	<tr>
		<td width="130" valign="top"><?php echo $this->logo_a_start; ?><img src="<?php echo $this->logo_link; ?>" class="logo120border"/><?php echo $this->logo_a_end; ?></td>
		<td valign="top">
            <div style="float:left">
            <span style="font-size:24px; color:#666666; font-weight:bold;"><?php echo $this->user_name; ?></span>
            </div>
            <div class="residentQuickLinks" style="margin-bottom: 10px; float:left">
                &nbsp;&nbsp;&nbsp;<a class="send_message" href="<?php echo $this->msg_link; ?>"><span <?php echo $this->msg_auth; ?>>Написать личное сообщение</span></a> 
            </div>
            <div style="float:left; padding-top:10px"><?php echo $this->last_visit_date ; ?></div>
            <div class="clear"></div>
		<span style="font-size:12px; color:#666666;"><?php echo $this->user_types; ?></span>
    
    </td>
	</tr>
	<tr><td></td>
	<td valign="top"><?php echo $this->user_info; ?></td>
	</tr>
  <tr>
  <td colspan="2">    
  </td>
</tr>  
	</table>
</td></tr>
<tr><td class="foot"><?php CRenderer::RenderControl("footer"); ?></td></tr>
</table>
<?php CRenderer::RenderControl("googleanalytics"); ?>
</body>
</html>
