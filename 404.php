<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>EVENT КАТАЛОГ – Портал для организаторов мероприятий</title>
	<?php print file_get_contents(ROOTDIR."pagecode/settings/master_files/metadata.htm");	?>
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" style="width:100%; height:100%;">
<tr><td style="padding: 16px 30px 10px 30px;"><?php
$header = new CHeaderObject();
$login = new CTemplateData();
$login->key="login";
$login->source="file";
$login->sourceEncoding="windows-1251";
$login->targetEncoding="windows-1251";
//$login->filename=ROOTDIR."pagecode/settings/master_files/header_404.htm";
$logout = new CTemplateData();
$logout->key="logout";
$logout->source="file";
$logout->sourceEncoding="windows-1251";
$logout->targetEncoding="windows-1251";
$logout->filename=ROOTDIR."pagecode/settings/master_files/header_logout.htm";
//$header->itemTemplates = array("login"=>$login, "logout"=>$logout);
print $header->RenderHTML();
?></td></tr>
<tr valign="middle"><td style="height: 100%" align="center">
  <div class="style_404">
	  <img src="/images/error404.jpg" width="527" height="492" style="display: block" alt="Ошибка 404" title="Ошибка 404. Извините, страница не доступна">
	  <div style="margin: 20px 0 0 0; ">Но вы можете перейти в разделы:<div style="font-size:18px"><a href="/contractor" class="contractor">Подрядчики</a>, <a href="/area" class="area">Площадки</a>, <a href="/artist" class="artist">Артисты</a>, <a href="/agency" class="agency">Агентства</a>, <a href="/eventoteka" class="common">Эвентотека</a></div>или вернуться на <a href="/" style="font-size:14px" class="black">главную</a></div>
	</div>
</td></tr>
<?php /*
<tr><td class="foot"><?php
$footer = new CFooterObject();
$footer->template = file_get_contents(ROOTDIR."pagecode/settings/master_files/footerCabinets.htm");
print $footer->RenderHTML();?></td></tr>
*/?>
</table>
</body>
</html>
