<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<link rel="stylesheet" type="text/css" href="/cms/templates/css/cms.css"  >
<script type="text/javascript" language="javascript" src="/cms/templates/scripts/cms.js"></script>
</head>

<body>
<form method="post">
<table border="0">
	<tr>
		<td colspan="2">
			<?php CRenderer::RenderControl("content"); ?>
		</td>
	</tr>
	<tr>
		<td><input type="submit" value="Сохранить"/></td>
		<td><input type="reset" value="Сбросить"/></td>
	</tr>
</table>
</form>
</body>
</html>
