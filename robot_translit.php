<?php

function translitURL($str)
{
  $tr = array(
      "А"=>"a","Б"=>"b","В"=>"v","Г"=>"g",
      "Д"=>"d","Е"=>"e","Ё"=>"e","Ж"=>"zh","З"=>"z","И"=>"i",
      "Й"=>"y","К"=>"k","Л"=>"l","М"=>"m","Н"=>"n",
      "О"=>"o","П"=>"p","Р"=>"r","С"=>"s","Т"=>"t",
      "У"=>"u","Ф"=>"f","Х"=>"h","Ц"=>"с","Ч"=>"ch",
      "Ш"=>"sh","Щ"=>"w","Ъ"=>"","Ы"=>"y","Ь"=>"",
      "Э"=>"e","Ю"=>"yu","Я"=>"ya","а"=>"a","б"=>"b",
      "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ё"=>"e","ж"=>"zh",
      "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
      "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
      "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
      "ц"=>"c","ч"=>"ch","ш"=>"sh","щ"=>"w","ъ"=>"",
      "ы"=>"y","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya",
      " "=> "_", "."=> "", "/"=> "_","-"=>"", "&"=>"and"
  );
  $str = trim($str);
  $str = preg_replace("/-/",' ',$str);
  $str = preg_replace("/\s{2,}/"," ",$str);
  $urlstr = strtr($str,$tr);
  $urlstr = preg_replace('/[^A-Za-z0-9_\-\']/', '_', $urlstr);
  $urlstr =  preg_replace("/_{2,}/","_",$urlstr);
  return preg_replace("/_$/","",$urlstr);
}








define("MYSQL_HOST", "localhost");
define("MYSQL_USER", "eventcatalog");
define("MYSQL_DATABASE", "eventcatalog");
define("MYSQL_PASSWORD", "_g8KaCwFh_Fs9i_n23Q-nxaW");
define("MYSQL_CHARSET", "UTF8");
define("MYSQL_PORT", "63627");
define("BASEURL", "http://eventcatalog.ru");

/*
define("MYSQL_HOST","localhost");
	define("MYSQL_USER","root");
	define("MYSQL_DATABASE","new_event");
	define("MYSQL_PASSWORD","");
	define("MYSQL_CHARSET","UTF8");
	define("MYSQL_PORT","3306");

*/
//	База данных
//	подключаемся к MySQL
if(!@mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PASSWORD))
{
	die('<meta http-equiv="content-type" content="text/html; charset=utf-8">11111111Отсутствует соединение с сервером MySQL');	
}
else	
{
	//	подключаемся к БД
	if(!@mysql_select_db(MYSQL_DATABASE))
	{
		die('<meta http-equiv="content-type" content="text/html; charset=utf-8">222222222Отсутствует соединение с базой данных');
	}
	//	устанавливаем необходимую кодировку
	mysql_query("SET NAMES ".MYSQL_CHARSET);
}



/* TRANSLITE RESIENT_NEWS */

$result = mysql_query("SELECT * FROM tbl__resident_news ");
$row = mysql_fetch_array($result);

do
{
$title_url = translitURL($row['title']);
$tbl_obj_id = $row['tbl_obj_id'];
$update = mysql_query("UPDATE tbl__resident_news SET title_url='$title_url' WHERE tbl_obj_id='$tbl_obj_id' ");

}
while($row = mysql_fetch_array($result));


/* TRANSLITE NEWS */

$result = mysql_query("SELECT * FROM tbl__news ");
$row = mysql_fetch_array($result);

do
{
$title_url = translitURL($row['title']);
$tbl_obj_id = $row['tbl_obj_id'];
$update = mysql_query("UPDATE tbl__news SET title_url='$title_url' WHERE tbl_obj_id='$tbl_obj_id' ");

}
while($row = mysql_fetch_array($result));


echo "dfdf";
?>
