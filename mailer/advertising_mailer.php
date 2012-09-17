<?php

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

define("MYSQL_HOST", "localhost");
define("MYSQL_USER", "eventcatalog");
define("MYSQL_DATABASE", "eventcatalog_staging");
define("MYSQL_PASSWORD", "_g8KaCwFh_Fs9i_n23Q-nxaW");
define("MYSQL_CHARSET", "cp1251");
define("MYSQL_PORT", "63627");
define("BASEURL", "http://eventcatalog.ru");

$dbLink = mysql_connect(MYSQL_HOST . ":" . MYSQL_PORT, MYSQL_USER, MYSQL_PASSWORD) or die("Could not connect: " . mysql_error());
mysql_select_db(MYSQL_DATABASE, $dbLink) or die('Can\'t use db : ' . mysql_error());
mysql_query("SET NAMES CP1251");


$mailer = ExecuteQuery("select * from tbl_advertising_mailer_config where id=1 AND status=0 ",$dbLink);
foreach($mailer as $item){ 

    if($item['date'] < date('Y-m-d H:i:s') ) {
    
      $update = ExecuteQuery("update tbl_advertising_mailer_config set status='1' where id = 1",$dbLink);
 
      
 
      $filter = $item['filter'];
      $add_header = str_replace("\\n","\r\n",$item['header'])."\r\n";
      $subject = $item['subject'];
      $body = $item['body'];
      $user_subscribed = $item['u_subscribed'];
      
      $sql = ExecuteQuery("select tbl_obj_id,email,login_type from vw__all_users where subscribe2=1 and (0=1 $filter)
                       union all
                       select null,email,null from tbl__subscribed where 1 = $user_subscribed
                    ",$dbLink);
 
      foreach($sql as $f) {
         $i++;
         if ($f["email"] != '') {

            mail($f["email"],$subject,$body,$add_header);
            sleep(1);
         }

         
      }
    
    }

}

mysql_close($dbLink);
?>

