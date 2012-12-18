<?php
function declension($c, $str1, $str2, $str5, $show_num = true)
{
  $c = abs($c) % 100;
  if ($c >= 10 && $c < 20)
    return ($show_num?$c:'').$str5;
  $c %= 10;
  if ($c > 1 && $c < 5)
    return ($show_num?$c:'').$str2;
  if ($c == 1)
    return ($show_num?$c:'').$str1;
  return ($show_num?$c:'').$str5;
}

function getNewsItem($item){

  if( $item["title_url"] == '') {$item["title_url"] = 'news'.$item["tbl_obj_id"];}
	$item["news_url"] = $item["title_url"];
  
	$strItem = "<table style='margin-right:10px'>
				<tr class='gray_row'>
    <td valign='top' class='left_cell' onClick='javascript:location.href=\"/resident_news/".$item['news_url']."\";'>";
	$strItem.="<img src='".$item['logo_image']."' class='logo120border' /></td>";
	$strItem.="<td class='text_cell' valign='top' style='padding-top:0;oveflow: hidden'>";
	
	$strItem.="<a href='/resident_news/".$item['news_url']."/' style='color: black; font-family: Arial, Helvetica, sans-serif;font-size:16px;font-weight:bold'>".$item['title']."</a><br/>";
	$strItem.="<span style='color:gray'>".$item['strdate']."</span><br/>";
	$strItem.=$item['text']."</td></tr><tr><td colspan='2' style='padding-top:3px;'>";
	$strItem.="<img src='/images/front/0.gif' alt='' height='6' width='1' /></td></tr></table>";
	
	return $strItem;
}


function Date_Ru($date)
{
	$date = explode(" ",$date);
	$date_array = explode("-",$date[0]);
	
	$day = $date_array[2];
	$month = $date_array[1];
	$year = $date_array[0];
	
	if ($month == "01") { $m = "���"; }
	if ($month == "02") { $m = "���"; }
	if ($month == "03") { $m = "���"; }
	if ($month == "04") { $m = "���"; }
	if ($month == "05") { $m = "���"; }
	if ($month == "06") { $m = "���"; }
	if ($month == "07") { $m = "���"; }
	if ($month == "08") { $m = "���"; }
	if ($month == "09") { $m = "���"; }
	if ($month == "10") { $m = "���"; }
	if ($month == "11") { $m = "���"; }
	if ($month == "12") { $m = "���"; }
	 
	$date = $day.'<br />'.$m;
	
	return $date;
}

// ����� �� �����
function onSiteTime($regdate = ''){
    $regdate = (is_int($regdate)?$regdate:strtotime($regdate));
    if(empty($regdate)) return false;
    $sitetime = time() - $regdate;
    // ���� 365*24*3600 = 31536000;
    $years = (int)($sitetime/31536000);
    // ������ 30*24*3600 = 2592000
    $months = (int)(($sitetime - $years*31536000)/2592000);
    // ��� 24*3600 = 86400
    $days = (int)(($sitetime - $years*31536000 - $months*2592000)/86400);
    
    $hour = (int)(($sitetime - $years*31536000 - $months*2592000 - $days*86400)/3600);
    
    if($days > 0 && $hour > 0) {$days = $days+1;}
    
    return ($years>0?declension($years, ' ���', ' ����', ' ���').' ':'').($months>0?declension($months, ' �����', ' ������', ' �������'):'').' '.($months>0||$years>0?'':($days>0?declension($days,' ����',' ���',' ����'):'1 ����'));
}

function lastVisitSite($visit_date,$reg_date){
    if($visit_date == '0000-00-00 00:00:00') {$visit_date = $reg_date;}
    $visit_date = (is_int($visit_date)?$visit_date:strtotime($visit_date));
    if(empty($visit_date)) return false;
    $sitetime = time() - $visit_date;
    // ���� 365*24*3600 = 31536000;
    $years = (int)($sitetime/31536000);
    // ������ 30*24*3600 = 2592000
    $months = (int)(($sitetime - $years*31536000)/2592000);
    // ��� 24*3600 = 86400
    $days = (int)(($sitetime - $years*31536000 - $months*2592000)/86400);
    // ���� 60*60 = 3600
    $hour = (int)(($sitetime - $years*31536000 - $months*2592000 - $days*86400)/3600);
    // ������ 60 = 60
    $minut = (int)(($sitetime - $years*31536000 - $months*2592000 - $days*86400 - $hour*3600)/60);

//if($years > 0) { return "<span class=\"lastVisitSite\" >��� �� �����:</span> ������ ���� �����"; }
if($months > 0 || $years > 0) { return "<span class=\"lastVisitSite\">��� �� �����:</span> ������ ������ �����";}
if($days > 7) { return "<span class=\"lastVisitSite\">��� �� �����:</span> ������ ������ �����";}
if($days > 1) { return "<span class=\"lastVisitSite\">��� �� �����:</span> ".declension($days,' ����',' ���',' ����')." �����";}   
if($hour > 1) { return "<span class=\"lastVisitSite\">��� �� �����:</span> ".declension($hour,' ���',' ����',' �����')." �����";}
if($minut > 1) { return "<span class=\"lastVisitSite\">��� �� �����:</span> ".declension($minut,' ������',' ������',' �����')." �����";}
    
   // return ($years>0?declension($years, ' ���', ' ����', ' ���').' ':'').($months>0?declension($months, ' �����', ' ������', ' �������'):'').' '.($months>0||$years>0?'':($days>0?declension($days,' ����',' ���',' ����'):'1 ����')).' '.($months>0||$years>0?'':($hour>0?declension($hour,' ���',' ����',' �����'):'1 ���')).' '.($months>0||$years>0?'':($minut>0?declension($minut,' ������',' ������',' �����'):'1 ������')). ' �����';
}

function caruoselDate($date){
    $date = (is_int($date)?$date:strtotime($date));
    if(empty($date)) return false;
    $sitetime = time() - $date;
    // ���� 365*24*3600 = 31536000;
    $years = (int)($sitetime/31536000);
    // ������ 30*24*3600 = 2592000
    $months = (int)(($sitetime - $years*31536000)/2592000);
    // ��� 24*3600 = 86400
    $days = (int)(($sitetime - $years*31536000 - $months*2592000)/86400);
    // ���� 60*60 = 3600
    $hour = (int)(($sitetime - $years*31536000 - $months*2592000 - $days*86400)/3600);
    // ������ 60 = 60
    $minut = (int)(($sitetime - $years*31536000 - $months*2592000 - $days*86400 - $hour*3600)/60);

//if($years > 0) { return "<span class=\"lastVisitSite\" >��� �� �����:</span> ������ ���� �����"; }
if($months > 0 || $years > 0) { return "������ ������ �����";}
if($days > 7) { return "������ ������ �����";}
if($days > 1) { return declension($days,' ����',' ���',' ����')." �����";}   
if($hour > 1) { return declension($hour,' ���',' ����',' �����')." �����";}
if($minut > 1) { return declension($minut,' ������',' ������',' �����')." �����";}
    
   // return ($years>0?declension($years, ' ���', ' ����', ' ���').' ':'').($months>0?declension($months, ' �����', ' ������', ' �������'):'').' '.($months>0||$years>0?'':($days>0?declension($days,' ����',' ���',' ����'):'1 ����')).' '.($months>0||$years>0?'':($hour>0?declension($hour,' ���',' ����',' �����'):'1 ���')).' '.($months>0||$years>0?'':($minut>0?declension($minut,' ������',' ������',' �����'):'1 ������')). ' �����';
}

function addDate($date){
    $date = (is_int($date)?$date:strtotime($date));
    if(empty($date)) return false;
    $sitetime = time() - $date;
    // ���� 365*24*3600 = 31536000;
    $years = (int)($sitetime/31536000);
    // ������ 30*24*3600 = 2592000
    $months = (int)(($sitetime - $years*31536000)/2592000);
    // ��� 24*3600 = 86400
    $days = (int)(($sitetime - $years*31536000 - $months*2592000)/86400);
    // ���� 60*60 = 3600
    $hour = (int)(($sitetime - $years*31536000 - $months*2592000 - $days*86400)/3600);
    // ������ 60 = 60
    $minut = (int)(($sitetime - $years*31536000 - $months*2592000 - $days*86400 - $hour*3600)/60);


if($days > 7) { return "������ ������ �����";}
if($days > 2) { return declension($days,' ����',' ���',' ����')." �����";} 
if($days == 2) { return "���������";}
if($days == 1 ) { return "�����";}
if($days == 0 ) { return "�������";}

}



function UserAge($birthday = ''){

// ��������� ���� � ����� ������ ��������(��� �������
// ������� 21 ��� 1982 ���� 19 ����� 12 ����� � 10 ������ :) )
$sec = 0;
$min = 0;
$hour = 0;

$birthday = explode('-',$birthday);

$day = $birthday[0];
$month = $birthday[1];
$year = $birthday[2];

//������ �������� ����� Unix ��� ��������� ����
$birthdate_unix = mktime($hour, $min, $sec, $month, $day, $year);

//�������� ����� unix ��� �������� �������
$current_unix = time();

//���������� �������� �����
$period_unix=$current_unix - $birthdate_unix;

// �������� ������� �������

// ������� ���������� ������
$age_in_years = floor($period_unix / (365*24*60*60));

// ������� ���������� �����
$age_in_days = floor($period_unix / (24*60*60));

// ������� ���������� ������
$age_in_hours = floor($period_unix / (60*60));

// ������� ���������� ��������
$age_in_minutes = floor($period_unix / 60);

// ������� ���������� ���������
$age_in_seconds = $period_unix;



return $age_in_years.($age_in_years>0?declension($age_in_years, ' ���', ' ����', ' ���', false).' ':'');

}



function get_video_id( $url ) {

  $_URL = explode("/", $url);
  if(!in_array("http:",$_URL)) {
    $url = 'http://'.$url;
  }

  if( preg_match( '/http:\/\/youtu.be/', $url, $matches) ) {
   $url = parse_url($url, PHP_URL_PATH);
   $url = str_replace( '/', '',  $url);
   return $url;
  
  } elseif ( preg_match( '/watch/', $url, $matches) ) {
   $arr = parse_url($url);
   $url = str_replace( 'v=', '', $arr['query'] );
   return $url;
   
  } elseif ( preg_match( '/http:\/\/www.youtube.com\/v/', $url, $matches) ) {
   $arr = parse_url($url);
   $url = str_replace( '/v/', '', $arr['path'] );
   return $url;
  
  } elseif ( preg_match( '/http:\/\/www.youtube.com\/embed/', $url, $matches) ) {
   $arr = parse_url($url);
   $url = str_replace( '/embed/', '', $arr['path'] );
   return $url;
    
  } elseif ( preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=[0-9]/)[^&\n]+|(?<=v=)[^&\n]+#", $url, $matches) ) {
   return $matches[0];
    
  } else {
   return false;
  }
 }


?>