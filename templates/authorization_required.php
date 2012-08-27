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
<?php /*
<tr><!--Меню-->
<td><?php CRenderer::RenderControl("menu"); ?></td>
</tr>
<tr>
<td><?php CRenderer::RenderControl("submenu"); ?></td>
</tr>
*/ ?>
<tr style="height:100%;" valign="top"><td style="padding-left: 30px; padding-right: 30px; padding-top: 16px;  padding-bottom: 30px;" align="center">
	<h1 style="color:#000"><?php echo $this->reqMessage; ?></h1><br />
	<form method="post" id="loginform_form">
		<input type="hidden" name="event" value="login" />
		<table border="0" cellpadding="4" cellspacing="0" >
		<tr>
			<td width="14">Логин: </td><td><span class="login"><input type="text" name="login" value="<?php echo $this->login; ?>" size="20"></span></td>
		</tr><tr>
			<td>Пароль:</td><td><input type="password" name="password" size="20"></td>
		</tr><tr>	
			<td></td><td><input style="margin:0" type="checkbox" name="remeber" value="remember" /> Запомнить &nbsp;&nbsp;&nbsp;&nbsp;<a onclick="javascript:locate('/registration/reset/');" style="cursor:pointer;" href="#"><span class="style2">Забыли пароль?</span></a>
			</td>
		</tr><tr>
		  <td></td>
			<td align="left">
			<input type="submit" value="Войти"/></td>
		  </tr>
		  <tr>
		  <td></td>
			<td align="left">
			<div id="loginmessage" style="display: none; color: red;">Неверный логин или пароль!</div>
		  </tr>
		</table>
		
	</form>

<script type="text/javascript">
$(function() {	
	
	$('#loginform_form').bind('submit', function(){
						var loginmessage = $('#loginmessage');
						loginmessage.hide();
						var authForm = $(this);
					    $.ajax({
					        url:    '/ajax/authorize/',
					        data:   authForm.serialize(),
					        success: function(responseText) {
								if (responseText == '1')
									document.location = document.location+'';
								else
									loginmessage.show();
					        }
					    });
						return false;
					});
});
</script>
	
	<div class="style_404" style="margin: 20px 0 0 0; ">Но вы можете перейти в разделы:<div style="font-size:18px"><a href="/contractor" class="contractor">Подрядчики</a>, <a href="/area" class="area">Площадки</a>, <a href="/artist" class="artist">Артисты</a>, <a href="agency" class="agency">Агентства</a>, <a href="/book" class="common">Эвентотека</a></div>или вернуться на <a href="/" style="font-size:14px" class="black">главную</a></div>
				<?php //CRenderer::RenderControl("account"); ?>	
</td></tr>
<?php /*
<tr><td class="foot"><?php CRenderer::RenderControl("footer"); ?></td></tr>
*/ ?>
</table>
<?php CRenderer::RenderControl("googleanalytics"); ?>
</body>
</html>