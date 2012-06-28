<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title><?php echo $this->reqMessage; ?> - EVENT КАТАЛОГ</title>
    <?php CRenderer::RenderControl("metadata"); ?>
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" style="width:100%; height:100%;">
<tr><!-- Заголовок-->
	<td style="padding-left: 30px; padding-right: 30px; padding-top: 16px;"><?php CRenderer::RenderControl("header"); ?></td>
</tr>
<tr><!--Меню-->
<td><?php CRenderer::RenderControl("menu"); ?></td>
</tr>
<tr>
<td><?php CRenderer::RenderControl("submenu"); ?></td>
</tr>
<tr style="height:100%;" valign="top"><td style="padding-left: 30px; padding-right: 30px; padding-top: 16px;  padding-bottom: 30px;" align="center">
	<h1><?php echo $this->reqMessage; ?></h1><br/>
	<form method="post">
		<input type="hidden" name="event" value="login" />
		<table border="0" cellpadding="4" cellspacing="0" >
		<tr>
			<td width="14">Логин: </td><td><span class="login"><input type="text" name="login" value="<?php echo $this->login; ?>" size="20"></span></td>
		</tr><tr>
			<td>Пароль:</td><td><input type="password" name="password" size="20"></td>
		</tr><tr>	
			<td align="right"><input type="checkbox" name="remeber" value="remember" /></td><td>Запомнить</td>
		</tr><tr>	
			<td colspan="2" align="right">
			<a onclick="javascript:locate('/registration/reset/');" style="cursor:pointer;" href="#"><span class="style2">Забыли пароль?</span></a>
			&nbsp;&nbsp;<input type="submit" value="Войти"/></td>
		  </tr>
		</table>
	</form>
				<?php //CRenderer::RenderControl("account"); ?>	
</td></tr>
<tr><td class="foot"><?php CRenderer::RenderControl("footer"); ?></td></tr>
</table>
<?php CRenderer::RenderControl("googleanalytics"); ?>
</body>
</html>
