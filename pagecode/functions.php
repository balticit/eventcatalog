<?php
function declension($c, $str1, $str2, $str5, $show_num = true)
{
  $c = abs($c) % 100;
  if ($c > 10 && $c < 20)
    return ($show_num?$c:'').$str5;
  $c %= 10;
  if ($c > 1 && $c < 5)
    return ($show_num?$c:'').$str2;
  if ($c == 1)
    return ($show_num?$c:'').$str1;
  return ($show_num?$c:'').$str5;
}

function getNewsItem($item){
	$strItem = "<table style='margin-right:10px'>
				<tr class='gray_row'>
    <td valign='top' class='left_cell' onClick='javascript:location.href=\"/resident_news/news".$item['tbl_obj_id']."\";'>";
	$strItem.="<img src='/upload/".$item['logo_image']."' class='logo120border' /></td>";
	$strItem.="<td class='text_cell' valign='top' style='padding-top:0;oveflow: hidden'>";
	$strItem.="<span style='color:gray'>".$item['strdate']."</span><br/>";
	$strItem.="<b><a href='/resident_news/news".$item['tbl_obj_id']."/' style='color: black;font-size:13px'>".$item['title']."</a></b><br/>";
	$strItem.=$item['text']."</td></tr><tr><td colspan='2' style='padding-top:3px;'>";
	$strItem.="<img src='/images/front/0.gif' alt='' height='6' width='1' /></td></tr></table>";
	
	return $strItem;
}

// врем€ на сайте
function onSiteTime($regdate = ''){
    $regdate = (is_int($regdate)?$regdate:strtotime($regdate));
    if(empty($regdate)) return false;
    $sitetime = time() - $regdate;
    // годы 365*24*3600 = 31536000;
    $years = (int)($sitetime/31536000);
    // мес€цы 30*24*3600 = 2592000
    $months = (int)(($sitetime - $years*31536000)/2592000);
    // дни 24*3600 = 86400
    $days = (int)(($sitetime - $years*31536000 - $months*2592000)/86400);
    
    return ($years>0?declension($years, ' год', ' года', ' лет').' ':'').($months>0?declension($months, ' мес€ц', ' мес€ца', ' мес€цев'):'').' '.($days>0?declension($days,' день',' дн€',' дней'):'1 день');
}

function UserAge($birthday = ''){

// ”казываем дату и врем€ ¬ашего рождени€(дл€ примера
// возьмем 21 ма€ 1982 года 19 часов 12 минут и 10 секунд :) )
$sec = 0;
$min = 0;
$hour = 0;

$birthday = explode('-',$birthday);

$day = $birthday[0];
$month = $birthday[1];
$year = $birthday[2];

//“еперь вычислим метку Unix дл€ указанной даты
$birthdate_unix = mktime($hour, $min, $sec, $month, $day, $year);

//¬ычислим метку unix дл€ текущего момента
$current_unix = time();

//ѕросчитаем разность меток
$period_unix=$current_unix - $birthdate_unix;

// ѕолучаем искомый возраст

// ¬озраст измер€емый годами
$age_in_years = floor($period_unix / (365*24*60*60));

// ¬озраст измер€емый дн€ми
$age_in_days = floor($period_unix / (24*60*60));

// ¬озраст измер€емый часами
$age_in_hours = floor($period_unix / (60*60));

// ¬озраст измер€емый минутами
$age_in_minutes = floor($period_unix / 60);

// ¬озраст измер€емый секундами
$age_in_seconds = $period_unix;

return $age_in_years.($age_in_years>0?declension($age_in_years, ' год', ' года', ' лет', false).' ':'');

}
?>