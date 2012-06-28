
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<link rel="stylesheet" type="text/css" href="/cms/templates/css/cms.css"  >
<script type="text/javascript" language="javascript" src="/cms/templates/scripts/cms.js"></script>
</head>

<body>
<table><tr>
  <td>Файлы mp3 в <i>application/public/upload/</i> не ныйденные в БД (tbl__upload)</td>
  <td><form method="post"><input type="hidden" name="del_mp3" value="1"><input type="submit" value="Удалить"></form></td>
</tr></table>
<div style="font-size:10px")>
<?php 
$mp3s = SQLProvider::ExecuteQueryReverse("
select file from tbl__upload 
where length(file)>0/* and tbl_obj_id in (select file_id from tbl__artist2mp3file am join tbl__artist_doc ad on am.artist_id = ad.tbl_obj_id)*/");
$mp3s = $mp3s['file'];
$mp3s = array_map('strtolower',$mp3s);
$file_list = glob(ROOTDIR."application/public/upload/*.mp3"); 
$file_list = array_merge($file_list,glob(ROOTDIR."application/public/upload/*.wma")); 
$not_finded = array();
$all_size = 0;
foreach($file_list as $f) {  
  $f_name = array_pop(preg_split('/\//',$f));
  if (array_search(strtolower($f_name),$mp3s) === FALSE) {
    array_push($not_finded,$f_name);
    $all_size += filesize($f);
    print $f_name."<br>";
  }  
}
$all_size /= (1024*1024);

print "<p>Files size: ".number_format($all_size, 3, '.', ' ')." MiB</p>";
if (isset($_POST['del_mp3']) && $_POST['del_mp3'] == 1) {
  $cnt = sizeof($not_finded);
  $deleted = 0;
  foreach ($not_finded as $f_name) {
    if (unlink(ROOTDIR."application/public/upload/$f_name"))
      $deleted++;
  }
  print "<span style=\"font-size: 14px;\">Удалено $deleted из $cnt </span>";  
}
?>
</div>
</body>
</html>
