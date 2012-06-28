<?php

//Mailer script for eventcatalog.ru
function ExecuteNonReturnQuery($query, $dbLink) {
    mysql_query($query, $dbLink) or die("Invalid query: '$query'" . mysql_error());
}

function renderItem($doc, $linkText) {
    $base = BASEURL;
    $titleUrl = $doc['title_url'];
    $image = $doc['logo_image'];
    $title = $doc['title'];
    $annotation = $doc['annotation'];
    return "<tr style='height: 81px;'>
		<td style='padding: 0;margin: 0;cursor: pointer;'>
			<a href='$base$titleUrl'>
                            <img src='$base/upload/$image' style='width: 120px;height: 80px;border: 1px solid #CCCCCC;padding: 2px;display: block;'>
			</a>
			</td>
			<td style='padding-left: 10px;padding-top: 5px;' valign='top'>
                            <a href='$base$titleUrl' style='color:#000000;text-decoration:none'>$title</a><br/>
                            <div style='position:relative; width: 500px; overflow:hidden; height:50px; z-index:0; margin-right: 5px; color:black;'>
                                $annotation<br/>
                                <a href='$base$titleUrl' style='color: blue;text-decoration:none;'>$linkText</a>
                            </div>
			</td>
            </tr>";
}

function renderNewResident($doc,$color,$name,$type){
    $base = BASEURL;
    $image = $doc['logo'];
    $title = $doc['title'];
    $text = $doc['description'];
    $title_url = $doc['title_url'];
        return "<tr style='height: 81px;'>
		<td style='padding: 0;margin: 0;cursor: pointer;'>
			<a href='$base/$type/$title_url'>
                            <img src='$base/upload/$image' style='width: 120px;height: 80px;border: 1px solid #CCCCCC;padding: 2px;display: block;'>
			</a>
			</td>
			<td style='padding-left: 10px;padding-top: 5px;' valign='top'>
                            <span style='color:$color'>$name: <a style='text-decoration:none;color:$color' href='$base/$type/$title_url'>$title</a></span><br>
                            <div style='position:relative; width: 500px; overflow:hidden; height:50px; z-index:0; margin-right: 5px; color:black;'>
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
		<td style='padding: 0;margin: 0;cursor: pointer;'>
			<a href='$base/$res_type/$title_url'>
                            <img src='$base/upload/$image' style='width: 120px;height: 80px;border: 1px solid #CCCCCC;padding: 2px;display: block;'>
			</a>
			</td>
			<td style='padding-left: 10px;padding-top: 5px;' valign='top'>
                            <span style='color:$color'>$sub: <a style='text-decoration:none;color:$color' href='$base/$res_type/$title_url'>$res_title</a></span><br>
                            <a href='$base/resident_news/news$id' style='color:#000000;text-decoration:none'>$title</a><br/>
                            <div style='position:relative; width: 500px; overflow:hidden; height:50px; z-index:0; margin-right: 5px; color:black;'>
                                $annotation<br/>
                                <a href='$base/resident_news/news$id' style='color: blue;text-decoration:none;'>$linkText</a>
                            </div>
			</td>
            </tr>";
}

function prepareBody() {
    $dbLink = mysql_connect(MYSQL_HOST . ":" . MYSQL_PORT, MYSQL_USER, MYSQL_PASSWORD) or die("Could not connect: " . mysql_error());
    $filter = " AND DATEDIFF(NOW(),registration_date)<7";
    mysql_select_db(MYSQL_DATABASE, $dbLink) or die('Can\'t use db : ' . mysql_error());
    //aaply params
    ExecuteNonReturnQuery("SET CHARACTER SET " . MYSQL_CHARSET, $dbLink);
    ExecuteNonReturnQuery("SET NAMES " . MYSQL_CHARSET, $dbLink);
    ExecuteNonReturnQuery("SET max_sort_length = 2000;", $dbLink);
    $bodyStart = "<html><body><table pacing='0' cellpadding='0' border='0' style='border-collapse: collapse;color: #fff;padding: 0;'>";
    $bodyEnd = "</table></body></html>";
    $html = "";
    //eventtv_array
    $publics = executeQuery(
            "select tbl_obj_id, title, title_url, logo_image, annotation, registration_date,
          case when DATEDIFF(NOW(),registration_date)<10 then 1 else 0 end is_new
        from tbl__eventtv_doc where active = 1" . $filter . " order by registration_date desc", $dbLink);
    if (count($publics) != 0) {
        $html .="<tr><td colspan='2' style='color:black;padding-top:20px;font-weight:bold;'>НОВОЕ ВИДЕО НА EVENT TV:</td></tr>";
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
        $html.="<tr><td colspan='2' style='color:black;padding-top:20px;font-weight:bold;'>НОВЫЕ СТАТЬИ В ЭВЕНТОТЕКЕ:</td></tr>";
        foreach ($eteka as $doc) {
            $doc['title_url'] = '/book/' . $doc['title_url'];
            $html .=renderItem($doc, "Читать статью полностью");
        }
    }
    //Industry news
    $filter2 = " AND DATEDIFF(NOW(),creation_date)<5";
    $industry = executeQuery("select `title`,`creation_date` as registration_date,`tbl_obj_id`, s_image as logo_image, annotation from `tbl__news` where `active`=1 AND creation_date IS NOT NULL" . $filter2, $dbLink);
    if (count($industry) != 0) {
        $html.="<tr><td colspan='2' style='color:black;padding-top:20px;font-weight:bold;'>НОВОСТИ ИНДУСТРИИ:</td></tr>";
        foreach ($industry as $doc) {
            $doc['title_url'] = '/news/details' . $doc['tbl_obj_id'] . '/';
            $html.=renderItem($doc, "Читать новость полностью");
        }
    }

    //Resident news
    $filter3 = " AND DATEDIFF(NOW(),rn.date)<5";
    $res_news = executeQuery("select rn.*, DATE_FORMAT(date,'%d.%m.%y') as `strdate` from `tbl__resident_news` rn where rn.`active`=1" . $filter3, $dbLink);
    if (count($res_news) != 0) {
        $html.="<tr><td colspan='2' style='color:black;padding-top:20px;font-weight:bold;'>НОВОСТИ РЕЗИДЕНТОВ:</td></tr>";
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
    $newArea = executeQuery("select tbl_obj_id,logo,title,title_url,description,registration_date from tbl__area_doc where active=1".$filter,$dbLink);
    $newArtist = executeQuery("select tbl_obj_id,logo,title,title_url,description,registration_date from tbl__artist_doc where active=1".$filter,$dbLink);
    $newAgency = executeQuery("select tbl_obj_id,logo_image,title,title_url,description,registration_date from tbl__agency_doc where active=1".$filter,$dbLink);
    $newContractor = executeQuery("select tbl_obj_id,logo_image,title,title_url,description,registration_date from tbl__contractor_doc where active=1".$filter,$dbLink);
    if(count($newArea)!=0||count($newArtist)!=0||count($newAgency)!=0||count($newContractor)!=0){
        $html.="<tr><td colspan='2' style='color:black;padding-top:20px;font-weight:bold;'>НОВЫЕ РЕЗИДЕНТЫ:</td></tr>";
        foreach ($newArea as $doc){
            if(!isset($doc['logo'])){
                $doc['logo'] = $doc['logo_image'];
            }
            $doc['description'] = strip_tags(substr($doc['description'],0,50)."...");
            $html .= renderNewResident($doc,"#39F","Площадка","area");
        }
        foreach ($newArtist as $doc){
            if(!isset($doc['logo'])){
                $doc['logo'] = $doc['logo_image'];
            }
            $doc['description'] = strip_tags(substr($doc['description'],0,50)."...");
            $html .= renderNewResident($doc,"#F06","Артист","artist");
        }
        foreach ($newAgency as $doc){
            if(!isset($doc['logo'])){
                $doc['logo'] = $doc['logo_image'];
            }
            $doc['description'] = strip_tags(substr($doc['description'],0,50)."...");
            $html .= renderNewResident($doc,"#9C0","Агенство","agency");
        }
        foreach ($newContractor as $doc){
            if(!isset($doc['logo'])){
                $doc['logo'] = $doc['logo_image'];
            }
            $doc['description'] = strip_tags(substr($doc['description'],0,50)."...");
            $html .= renderNewResident($doc,"#F05620","Подрядчик","contractor");
        }
    }

    //This week events
     $events = SQLProvider::ExecuteQuery("select t.*, YEAR(t.date) as 'gr_year',MONTH(t.date) as 'gr_month',UNIX_TIMESTAMP(t.date) as unixdate, a.title_url from `tbl__event_calendar` t left join `tbl__area_doc` a on a.tbl_obj_id = t.area_id where UNIX_TIMESTAMP(t.date)>=".strtotime(date("Y-m-d"))."
                    and t.active=1
                  order by UNIX_TIMESTAMP(t.date) asc");



    mysql_close($dbLink);
    return $bodyStart . $html . $bodyEnd;
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

define("MYSQL_HOST", "mysql.baze.eventcatalog.ru.postman.ru");
define("MYSQL_USER", "root");
define("MYSQL_DATABASE", "eventcatalog_ru");
define("MYSQL_PASSWORD", "2ygMPCBrm8");
define("MYSQL_CHARSET", "cp1251");
define("MYSQL_PORT", "63627");
define("BASEURL", "http://eventcatalog.ru");


$test_mail = "vladimir.firsovv@gmail.com";
$html = prepareBody();
mail($test_mail, "the subject", $html, "Content-type: text/html; charset=windows-1251 \r\n"
        . "From: noreply@eventcatalog.ru \r\n"
        . "X-Mailer: PHP/" . phpversion());
?>

