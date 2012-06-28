<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>EVENT КАТАЛОГ – Портал для организаторов мероприятий</title>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
	<link rel="stylesheet" type="text/css" href="/styles/front.css?<?php echo time(); ?>">
	<link rel="stylesheet" type="text/css" href="/styles/scroll_vert.css?<?php echo time(); ?>">
	<script type="text/javascript" language="JavaScript" src="/js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="/js/jquery.jcarousel.min.js"></script>
</head>
<body style="background:#f8f8f8;">
<div id="cnt_scroll"><ul id="mycarousel" class="jcarousel jcarousel-skin-tango"><?php CRenderer::RenderControl("msgs"); ?></ul></div>
</body>
<script type="text/javascript">
    jQuery('#mycarousel').jcarousel({vertical: true,scroll: 7, visible: 7});
</script>
</html>
