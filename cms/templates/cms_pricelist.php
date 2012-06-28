<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<link rel="stylesheet" type="text/css" href="/cms/templates/css/cms.css"  >
<script type="text/javascript" language="javascript" src="/cms/templates/scripts/cms.js"></script>
</head>

<body>
<form method="post" enctype="multipart/form-data">
<?php
	
	if ((isset($_FILES["pricelist"]))and(!$_FILES["pricelist"]["error"])) {
		$filename = file_get_contents($_SERVER['DOCUMENT_ROOT']."/pagecode/settings/pricelist/filename.htm");
		//@unlink(file_get_contents($_SERVER['DOCUMENT_ROOT']."/pricelist/$filename");
		echo "Файл загружен.<br>";
		move_uploaded_file($_FILES["pricelist"]["tmp_name"],$_SERVER['DOCUMENT_ROOT']."/pricelistfile/".$_FILES["pricelist"]["name"]);
		file_put_contents($_SERVER['DOCUMENT_ROOT']."/pagecode/settings/pricelist/filename.htm",$_FILES["pricelist"]["name"]);
	}
	
	$filename = file_get_contents($_SERVER['DOCUMENT_ROOT']."/pagecode/settings/pricelist/filename.htm");
	echo "Текущий файл прайс-листа 1 <a href=/pricelistfile/$filename>$filename</a><br>";

?>



	Загрузить файл <input type="file" name="pricelist" /> <input type="submit" value="Загрузить" />

</form>

<form method="post" enctype="multipart/form-data">
<?php
	
	if ((isset($_FILES["pricelist1"]))and(!$_FILES["pricelist1"]["error"])) {
		$filename1 = file_get_contents($_SERVER['DOCUMENT_ROOT']."/pagecode/settings/pricelist/filename1.htm");
		//@unlink(file_get_contents($_SERVER['DOCUMENT_ROOT']."/pricelist/$filename1");
		echo "Файл загружен.<br>";
		move_uploaded_file($_FILES["pricelist1"]["tmp_name"],$_SERVER['DOCUMENT_ROOT']."/pricelistfile/".$_FILES["pricelist1"]["name"]);
		file_put_contents($_SERVER['DOCUMENT_ROOT']."/pagecode/settings/pricelist/filename1.htm",$_FILES["pricelist1"]["name"]);
	}
	
	$filename1 = file_get_contents($_SERVER['DOCUMENT_ROOT']."/pagecode/settings/pricelist/filename1.htm");
	echo "Текущий файл прайс-листа 2 <a href=/pricelistfile/$filename1>$filename1</a><br>";

?>



	Загрузить файл <input type="file" name="pricelist1" /> <input type="submit" value="Загрузить" />

</form>

<form method="post" enctype="multipart/form-data">
<?php
	
	if ((isset($_FILES["pricelist2"]))and(!$_FILES["pricelist2"]["error"])) {
		$filename2 = file_get_contents($_SERVER['DOCUMENT_ROOT']."/pagecode/settings/pricelist/filename2.htm");
		//@unlink(file_get_contents($_SERVER['DOCUMENT_ROOT']."/pricelist/$filename2");
		echo "Файл загружен.<br>";
		move_uploaded_file($_FILES["pricelist2"]["tmp_name"],$_SERVER['DOCUMENT_ROOT']."/pricelistfile/".$_FILES["pricelist2"]["name"]);
		file_put_contents($_SERVER['DOCUMENT_ROOT']."/pagecode/settings/pricelist/filename2.htm",$_FILES["pricelist2"]["name"]);
	}
	
	$filename2 = file_get_contents($_SERVER['DOCUMENT_ROOT']."/pagecode/settings/pricelist/filename2.htm");
	echo "Текущий файл прайс-листа 3 <a href=/pricelistfile/$filename2>$filename2</a><br>";

?>



	Загрузить файл <input type="file" name="pricelist2" /> <input type="submit" value="Загрузить" />

</form>
</body>
</html>
