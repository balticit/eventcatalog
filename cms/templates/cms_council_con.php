<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
