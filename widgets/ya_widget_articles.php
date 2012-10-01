<?php
header('Content-Type: text/html; charset=utf-8');
$limit = 24;

function formatDate($time) {
	$return = date("d.m", $time);
	$today = mktime(0, 0, 0, date("m"), date("d"), date("y"));
	if ($today <= $time) $return = date("H:i", $time);
	return $return;
}
?>
<html>
<header>
<style>
* { font: 13px Arial;  }
body {font: 13px Arial; margin: -2px 0 0 0;}
a {color: #1a3dc1;text-decoration:underline;}
a:hover {color: red;text-decoration:none;}
a.orange {color: #F05620;}
a.orange:hover {color: #F05620;}
a.blue {color: #3399FF;}
a.blue:hover {color: #3399FF;}
a.green {color: #99CC00;}
a.green:hover {color: #99CC00;}
a.pink {color: #FF0066;}
a.pink:hover {color: #FF0066;}
.photo{width:40px; height:24px;border:none; margin-right: 8px;	}
.list {
	padding: 4px 0;
}
.time {color: #666666; font-size: 11px;}
.bg {
	position: absolute;
	bottom: 0;
	width: 100%;
	height: 19px;
	background: url('scroll/bg.png') repeat-x;
}
</style>
<script type="text/javascript" src="http://img.yandex.net/webwidgets/1/WidgetApi.js"></script>
<script type="text/javascript" src="http://yandex.st/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript" src="scroll.js"></script>
<script type="text/javascript">
widget.onload = function(){ widget.adjustIFrameHeight(); }
$(document).ready(function() {
	$('#scrolling').scroll();
});
</script>
</header>
<body>
<div id=scrolling>
<?php
$data = array();
$new_data = array();
$link = mysql_connect("localhost","widget","widget_password",true);
$sql = mysql_query('SELECT `tbl_obj_id`, `logo_image`, `title`, UNIX_TIMESTAMP(`registration_date`) as `registration_date` FROM `tbl__public_doc` WHERE `active` = 1 ORDER BY `registration_date` DESC LIMIT '.$limit.'');
if (mysql_error()) die(mysql_error());
WHILE ($r = mysql_fetch_assoc($sql)) {
	$r['url'] =  '/book/details'.$r['tbl_obj_id'];
	$data[] = $r;
}
$sql = mysql_query('SELECT `tbl_obj_id`, `logo_image`, `title`, UNIX_TIMESTAMP(`registration_date`) as `registration_date` FROM `tbl__eventtv_doc` WHERE `active` = 1 ORDER BY `registration_date` DESC LIMIT '.$limit.'');
if (mysql_error()) die(mysql_error());
WHILE ($r = mysql_fetch_assoc($sql)) {
	$r['url'] =  '/eventtv/details'.$r['tbl_obj_id'];
	$data[] = $r;
}

foreach ($data as $val) {
	$n = 0;
	foreach ($data as $val2) {
		if ($val['registration_date'] < $val2['registration_date']) $n++;
	}
	$new_data[$n] = $val;
}

if ($limit > sizeof($data)) $limit = sizeof($data); $limit = 16;

for ($i = 0; $i < $limit; $i++) {
	echo '<div class="list"><a href="http://eventcatalog.ru'.$new_data[$i]['url'].'" target="_blank" ><img src="http://eventcatalog.ru/upload/'.$new_data[$i]['logo_image'].'" align="absmiddle" class="photo">'.$new_data[$i]['title'].'</a><span class="time">, '.formatDate($new_data[$i]['registration_date']).'</span></div>';
}

?>
<div class="list">&nbsp;</div>
</div>
</body>
</html>