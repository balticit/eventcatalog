<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Сообщения - EVENT КАТАЛОГ</title>
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
<tr><td style="padding-left: 30px; padding-right: 30px; padding-top: 16px; padding-bottom: 30px; height:100%;" valign="top">

	<table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign="top" >
			<div style="padding-left:15px;">
				<div style="padding-right: 15px;padding-left:20px;  ">
					<?php if ($this->is_sent){?>
						<h1>Сообщение отправлено</h1>
					<?php } else { ?>
						<div style="width:100%;">
						<p style="font-size:16px; font-weight:bold;"><?php echo $this->action=="compose"?"Новое сообщение":"Ответить на сообщение"; ?></p>
						<?php if ($this->action=="reply"){ ?>
							<p style="color:#333333;"><?php echo $this->reply_mess["text"]; ?></p>
						<?php }?>
						<table border="0">
						<tr>
							<td colspan="2" style="font-weight:bold; " valign="middle">Кому:</td>
						</tr>
						<tr>
							<td style="width:140px;height:80px;" align="center"><div><img src="<?php echo $this->reciever_data["logo"]==''?"/images/nologo.png":$this->reciever_data["logo"]; ?>" class="logo120" /></div></td>
							<td style="font-weight:bold;"><?php echo $this->reciever_data["title"]; ?></td>
						</tr>
						</table>
						<form method="post">
						<textarea name="message_text" style="width:600px; height:100px;"></textarea><br /><br />
						<input type="submit" value="Написать"/><br /><br /><br />
						</form>
						</div>
					<?php }?>
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
