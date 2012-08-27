<?php

//Mailer script for eventcatalog.ru
function ExecuteNonReturnQuery($query, $dbLink) {
    mysql_query($query, $dbLink) or die("Invalid query: '$query'" . mysql_error());
}

function getMonth($month) {
    switch ($month) {
        case 1: $month = "января";
            break;
        case 2 : $month = "февраля";
            break;
        case 3 : $month = "марта";
            break;
        case 4 : $month = "апреля";
            break;
        case 5: $month = "мая";
            break;
        case 6 : $month = "июня";
            break;
        case 7 : $month = "июля";
            break;
        case 8 : $month = "августа";
            break;
        case 9 : $month = "сентября";
            break;
        case 10 : $month = "октября";
            break;
        case 11 : $month = "ноября";
            break;
        case 12 : $month = "декабря";
            break;
    }
    return $month;
}

function generateHeader($username = "user") {
    $timestamp = time();
    $day_end = date("d", $timestamp);
    $date_time_array = getdate($timestamp);
    $hours = $date_time_array['hours'];
    $minutes = $date_time_array['minutes'];
    $seconds = $date_time_array['seconds'];
    $month = $date_time_array['mon'];
    $strmonthend = getMonth($month);
    $day = $date_time_array['mday'];
    $year = $date_time_array['year'];
    $start = mktime($hours, $minutes, $seconds, $month, $day - 3, $year);
    $arr_start = getdate($start);
    $strmonthstart = getMonth($arr_start['mon']);
    $start_date = date("d", $start);

    return "<tr>
		<td style='background-color:gray;padding-left:10px;vertical-align:middle;padding-top:10px;padding-bottom:10px;' colspan='2'>
			<img style='float:left;' width='70px' src='http://eventcatalog.ru/images/mail_logo.jpg'>
			<div style='margin-top:21px;margin-left:80px;color:white;font-size:18pt;vertical-align:middle;'>
				EVENT NEWS
				<span style='font-size:18pt;display:block;margin-right:10px;float:right;'>$day_end $strmonthend $year</span>
			</div>
		</tr>
		<tr>
		<td style='padding-top:10px;color:black;padding-left:10px' colspan='2'>Здравствуйте, $username!<br/>
		За период с $start_date $strmonthstart по $day_end $strmonthend $year года на портале eventcatalog.ru добавились:
		</td>
		</tr>";
}

function generateFooter() {
    return "<tr><td style='padding-top:5px;color:black;text-decoration:none;padding-left:10px' colspan='2'>Удачной Вам event-недели и до встречи на страницах сайта!<br/><br/>
    С уважением,<br/>
    Ваш ежедневный помощник<br/>
    <a href='http://www.eventcatalog.ru'>Eventcatalog.ru</a><br/>
    <br/>
    <p style='margin: 10px 0 0'>
    <a style='text-decoration:none' target='_blank' href='http://www.facebook.com/EventCatalog'>
        <img src='http://eventcatalog.ru/images/facebook.png'>
    </a>
    <a style='text-decoration:none' target='_blank' href='http://vk.com/public34359442'>
        <img src='http://eventcatalog.ru/images/vkontakte.png'>
    </a>
    <a style='text-decoration:none' target='_blank' href='http://www.yandex.ru/?add=83173&amp;from=promocode'>
        <img src='http://eventcatalog.ru/images/yandex.png'>
    </a></p>
    <p style='color:gray;font-size:8pt;'>
        Если Вы не желаете получать рассылку от портала для организаторов мероприятий, перейдите по <a style='color:gray;' href='http://eventcatalog.ru/u_cabinet'>этой ссылке</a>.
    </p
    </td>
    </tr>";
}

function renderItem($doc, $linkText) {
    $base = BASEURL;
    $titleUrl = $doc['title_url'];
    $image = $doc['logo_image'];
    $title = $doc['title'];
    $annotation = $doc['annotation'];
    return "<tr style='height: 81px;'>
		<td style='padding: 0;margin: 0;cursor: pointer;padding-left:10px;padding-top:5px;'>
			<a href='$base$titleUrl'>
                            <img src='$base/upload/$image' style='width: 120px;height: 80px;border: 1px solid #CCCCCC;padding: 2px;display: block;'>
			</a>
			</td>
			<td style='padding-left: 5px;padding-top:5px;' valign='top'>
                            <a href='$base$titleUrl' style='color:#000000;text-decoration:none'><b>$title</b></a><br/>
                            <div style='position:relative; width: 700px; overflow:hidden; height:50px; z-index:0; margin-right: 5px; color:black;'>
                                $annotation<br/>
                                <a href='$base$titleUrl' style='color: blue;text-decoration:none;'>$linkText</a>
                            </div>
			</td>
            </tr>";
}

function renderNewResident($doc, $color, $name, $type) {
    $base = BASEURL;
    $image = $doc['logo'];
    $title = $doc['title'];
    $text = $doc['description'];
    $city_name = $doc['city_name'];
    $title_url = $doc['title_url'];
    return "<tr style='height: 81px;'>
		<td style='padding: 0;margin: 0;cursor: pointer;padding-left:10px;padding-top:5px;'>
			<a href='$base/$type/$title_url'>
                            <img src='$base/upload/$image' style='width: 120px;height: 80px;border: 1px solid #CCCCCC;padding: 2px;display: block;'>
			</a>
			</td>
			<td style='padding-left: 10px;padding-top:5px;' valign='top'>
                            <span style='color:$color'>$name: <a style='text-decoration:none;color:$color' href='$base/$type/$title_url'>$title</a></span><br>
				<span style='color:black'>Город: $city_name</span><br>
                            <div style='position:relative; width: 700px; overflow:hidden; height:50px; z-index:0; margin-right: 5px; color:black;'>
                                $text<br/>
                                <a href='$base/$type/$title_url' style='color: blue;text-decoration:none;'>Перейти на страницу компании</a>
                            </div>
			</td>
            </tr>";
}

function renderResidentNews($doc, $linkText) {
    $base = BASEURL;
    $image = $doc['logo'];
    $title = $doc['title'];
    $res_type = $doc['resident_type'];
    $title_url = $doc['title_url'];
    $sub = $doc['sub'];
    $res_title = $doc['res_title'];
    $id = $doc['tbl_obj_id'];
    $annotation = $doc['text'];
    $color = $doc['color'];
    return "<tr style='height: 81px;'>
		<td style='padding:0;margin: 0;cursor: pointer;padding-left:10px;padding-top:5px;'>
			<a href='$base/$res_type/$title_url'>
                            <img src='$base/upload/$image' style='width: 120px;height: 80px;border: 1px solid #CCCCCC;padding: 2px;display: block;'>
			</a>
			</td>
			<td style='padding-left: 10px;padding-top:5px;' valign='top'>
                            <span style='color:$color'>$sub: <a style='text-decoration:none;color:$color' href='$base/$res_type/$title_url'>$res_title</a></span><br>
                            <a href='$base/resident_news/news$id' style='color:#000000;text-decoration:none;font-weight:bold;'>$title</a><br/>
                            <div style='position:relative; width: 700px; overflow:hidden; height:50px; z-index:0; margin-right: 5px; color:black;'>
                                $annotation<br/>
                                <a href='$base/resident_news/news$id' style='color: blue;text-decoration:none;'>$linkText</a>
                            </div>
			</td>
            </tr>";
}

function prepareBody() {
    $dbLink = mysql_connect(MYSQL_HOST . ":" . MYSQL_PORT, MYSQL_USER, MYSQL_PASSWORD) or die("Could not connect: " . mysql_error());
    $filter = " AND DATEDIFF(NOW(),registration_date)<4";
    mysql_select_db(MYSQL_DATABASE, $dbLink) or die('Can\'t use db : ' . mysql_error());
    mysql_query("SET NAMES CP1251");
    //aaply params
    ExecuteNonReturnQuery("SET CHARACTER SET " . MYSQL_CHARSET, $dbLink);
    ExecuteNonReturnQuery("SET NAMES " . MYSQL_CHARSET, $dbLink);
    ExecuteNonReturnQuery("SET max_sort_length = 2000;", $dbLink);
    $bodyEnd = "</table></body></html>";
    $html = "";

    //This week events
     //$events = executeQuery("select t.* from `tbl__event_calendar` t
       //  where t.date>now() year(t.date) = year(now()) and week(t.date, 1) = week(now(), 1) and t.active=1");

    //eventtv_array
    $publics = executeQuery(
            "select tbl_obj_id, title, title_url, logo_image, annotation, registration_date,
          case when DATEDIFF(NOW(),registration_date)<10 then 1 else 0 end is_new
        from tbl__eventtv_doc where active = 1" . $filter . " order by registration_date desc", $dbLink);
    if (count($publics) != 0) {
        $html .="<tr><td colspan='2' style='color:black;padding-top:10px;font-weight:bold;padding-left:10px;'>НОВОЕ ВИДЕО НА EVENT TV:</td></tr>";
        foreach ($publics as $doc) {
            $doc['title_url'] = '/eventtv/' . $doc['title_url'];
            $html .= renderItem($doc, "Смотреть репортаж");
        }
    }
    //Eventoteka array
    $eteka = executeQuery(
            "select tbl_obj_id, title,title_url, logo_image, annotation,
                  registration_date,
                  case when DATEDIFF(NOW(),registration_date)<10 then 1 else 0 end is_new
                  from tbl__public_doc where active = 1" . $filter . " order by registration_date desc", $dbLink);
    if (count($eteka) != 0) {
        $html.="<tr><td colspan='2' style='color:black;padding-top:10px;font-weight:bold;padding-left:10px;'>НОВЫЕ СТАТЬИ В ЭВЕНТОТЕКЕ:</td></tr>";
        foreach ($eteka as $doc) {
            $doc['title_url'] = '/book/' . $doc['title_url'];
            $html .=renderItem($doc, "Читать статью полностью");
        }
    }
    //Industry news
    $filter2 = " AND DATEDIFF(NOW(),creation_date)<4";
    $industry = executeQuery("select `title`,`creation_date` as registration_date,`tbl_obj_id`, s_image as logo_image, annotation from `tbl__news` where `active`=1 AND creation_date IS NOT NULL" . $filter2, $dbLink);
    if (count($industry) != 0) {
        $html.="<tr><td colspan='2' style='color:black;padding-top:10px;font-weight:bold;padding-left:10px;'>НОВОСТИ ИНДУСТРИИ:</td></tr>";
        foreach ($industry as $doc) {
            $doc['title_url'] = '/news/details' . $doc['tbl_obj_id'] . '/';
            $html.=renderItem($doc, "Читать новость полностью");
        }
    }

    //Resident news
    $filter3 = " AND DATEDIFF(NOW(),rn.date)<4";
    $res_news = executeQuery("select rn.*, DATE_FORMAT(date,'%d.%m.%y') as `strdate` from `tbl__resident_news` rn where rn.`active`=1" . $filter3, $dbLink);
    if (count($res_news) != 0) {
        $html.="<tr><td colspan='2' style='color:black;padding-top:10px;font-weight:bold;padding-left:10px;'>НОВОСТИ РЕЗИДЕНТОВ:</td></tr>";
        foreach ($res_news as $key => $val) {
            $res = ExecuteQuery("SELECT * FROM tbl__" . $res_news[$key]["resident_type"] . "_doc WHERE tbl_obj_id=" . $res_news[$key]["resident_id"], $dbLink);
            $res_news[$key]["title_url"] = $res[0]['title_url'];
            $res_news[$key]["res_title"] = $res[0]['title'];
            if (isset($res[0]['logo'])) {
                $res_news[$key]["logo"] = $res[0]['logo'];
            } else {
                $res_news[$key]["logo"] = $res[0]['logo_image'];
            }
            $res_news[$key]["title"] = $res_news[$key]["title"];
            $res_news[$key]["text"] = strip_tags(substr($res_news[$key]["text"], 0, 100) . "...");
            switch ($val['resident_type']) {
                case 'area': $res_news[$key]['sub'] = 'Новость площадки';
                    $res_news[$key]['color'] = "#39F";
                    break;
                case 'artist': $res_news[$key]['sub'] = 'Новость артиста';
                    $res_news[$key]['color'] = "#F06";
                    break;
                case 'contractor': $res_news[$key]['sub'] = 'Новость подрядчика';
                    $res_news[$key]['color'] = "#F05620";
                    break;
                case 'agency': $res_news[$key]['sub'] = 'Новость агентства';
                    $res_news[$key]['color'] = "#9C0";
                    break;
            }
        }
        foreach ($res_news as $doc) {
            $html.=renderResidentNews($doc, "Читать новость полностью");
        }
    }
    //new_resident
    $newArea = executeQuery("select a.tbl_obj_id,a.logo,a.title,a.title_url,a.description,a.registration_date,c.title as city_name from tbl__area_doc a left join tbl__city c on a.city = c.tbl_obj_id where a.active=1" . $filter, $dbLink);
    $newArtist = executeQuery("select a.tbl_obj_id,a.logo,a.title,a.title_url,a.description,a.registration_date,c.title as city_name from tbl__artist_doc a left join tbl__city c on a.city = c.tbl_obj_id where a.active=1" . $filter, $dbLink);
    $newAgency = executeQuery("select a.tbl_obj_id,a.logo_image,a.title,a.title_url,a.description,a.registration_date,c.title as city_name from tbl__agency_doc a left join tbl__city c on a.city = c.tbl_obj_id where a.active=1" . $filter, $dbLink);
    $newContractor = executeQuery("select a.tbl_obj_id,a.logo_image,a.title,a.title_url,a.description,a.registration_date,c.title as city_name from tbl__contractor_doc a left join tbl__city c on a.city = c.tbl_obj_id where a.active=1" . $filter, $dbLink);
    if (count($newArea) != 0 || count($newArtist) != 0 || count($newAgency) != 0 || count($newContractor) != 0) {
        $html.="<tr><td colspan='2' style='color:black;padding-top:10px;font-weight:bold;padding-left:10px;'>НОВЫЕ РЕЗИДЕНТЫ:</td></tr>";
        foreach ($newArea as $doc) {
            if (!isset($doc['logo'])) {
                $doc['logo'] = $doc['logo_image'];
            }
            $doc['description'] = strip_tags(substr($doc['description'], 0, 150) . "...");
            $html .= renderNewResident($doc, "#39F", "Площадка", "area");
        }
        foreach ($newArtist as $doc) {
            if (!isset($doc['logo'])) {
                $doc['logo'] = $doc['logo_image'];
            }
            $doc['description'] = strip_tags(substr($doc['description'], 0, 150) . "...");
            $html .= renderNewResident($doc, "#F06", "Артист", "artist");
        }
        foreach ($newAgency as $doc) {
            if (!isset($doc['logo'])) {
                $doc['logo'] = $doc['logo_image'];
            }
            $doc['description'] = strip_tags(substr($doc['description'], 0, 150) . "...");
            $html .= renderNewResident($doc, "#9C0", "Агенство", "agency");
        }
        foreach ($newContractor as $doc) {
            if (!isset($doc['logo'])) {
                $doc['logo'] = $doc['logo_image'];
            }
            $doc['description'] = strip_tags(substr($doc['description'], 0, 150) . "...");
            $html .= renderNewResident($doc, "#F05620", "Подрядчик", "contractor");
        }
    }
    mysql_close($dbLink);
    return $html . generateFooter(). $bodyEnd;
}

function executeQuery($query, $dbLink) {
    $result = mysql_query($query, $dbLink) or die("Invalid query: $query error: " . mysql_error());
    $retrows = array();
    $i = 0;
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $retrows[$i] = $row;
        $i++;
    }
    return $retrows;
}

//DB connection and base url

define("MYSQL_HOST", "localhost");
define("MYSQL_USER", "eventcatalog");
define("MYSQL_DATABASE", "eventcatalog");
define("MYSQL_PASSWORD", "_g8KaCwFh_Fs9i_n23Q-nxaW");
define("MYSQL_CHARSET", "cp1251");
define("MYSQL_PORT", "63627");
define("BASEURL", "http://eventcatalog.ru");
//prepareBody
$bodyStart = "<html><body><table pacing='0' cellpadding='0' style='padding-left:10px;padding-right:10px;border:gray 1px solid;border-collapse: collapse;color: #fff;padding: 0;'>";
$html = prepareBody();
//get all users with subscription
$dbLink = mysql_connect(MYSQL_HOST . ":" . MYSQL_PORT, MYSQL_USER, MYSQL_PASSWORD) or die("Could not connect: " . mysql_error());
mysql_select_db(MYSQL_DATABASE, $dbLink) or die('Can\'t use db : ' . mysql_error());
mysql_query("SET NAMES CP1251");
$users = ExecuteQuery("select title,email from tbl__registered_user where subscribe=1",$dbLink);
foreach($users as $doc){    
    $current_message =  $bodyStart . generateHeader($doc['title']) . $html;
    //send_mail
    mail($doc['email'], "Свежие новости от портала eventcatalog.ru", $current_message, "Content-type: text/html; charset=windows-1251 \r\n"
        . "From: EVENT NEWS <noreply@eventcatalog.ru> \r\n"
        . "X-Mailer: PHP/" . phpversion());
    sleep(1);
}
mysql_close($dbLink);
?>

