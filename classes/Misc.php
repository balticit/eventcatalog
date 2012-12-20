<?php

function ValidateDir(&$dirname)
{
  $endChar = $dirname[strlen($dirname)-1];
  if (($endChar!="/")&&($endChar!="\\"))
  {
    $dirname.="/";
  }
}

function RealDir($dirname)
{
  if (!is_dir($dirname))
  {
    $dirname = ROOTDIR.$dirname;
    if (!  is_dir($dirname))
    {
      return null;
    }
  }
  else
  {
    $dirname = realpath($dirname);
  }
  ValidateDir($dirname);
  return $dirname;
}

function RealFile($file)
{
  if (!is_file($file)){
    $file = ROOTDIR.$file;
    if (!is_file($file))
      return null;
  }
  return $file;
}

function LoadDir($dirname)
{
  ValidateDir(&$dirname);
  if (is_dir($dirname))
  {
    $files = scandir($dirname);
    $files = preg_grep("/^.*.php$/",$files);
    foreach ($files as $key=>$value)
    {
      if (is_file($dirname.$value))
      {
        include_once($dirname.$value);
      }
    }
  }
}

function GPT($name,$type = "int",$defval = null)
{
  $value = GetParam($name,"prg",$defval);
  return VType($value,$type,$defval);
}

function VType($value,$type = "int",$defval = null)
{
  if ($value===$defval)
  {
    return $value;
  }

  switch ($type) {
    case "numeric":
    return (is_numeric($value))?$value:$defval;
    break;
    case "int":
    return (is_numeric($value)&&(intval($value)==floatval($value)))?$value:$defval;
    break;
    case "string":
    return (is_string($value))?$value:$defval;
    break;
    default:
    return $value;
    break;
  }
}

function GP($name,$defval = null)
{
  return GetParam($name,"prgc",$defval);
}

function GetParam($name,$order="pgrsc",$defval = null)
{
  $order = strtolower($order);
  for ($i=0;$i<strlen($order);$i++)
  {
    switch ($order[$i]) {
      case "p":
      if (is_array($name))
      {
        $p = &$_POST;
        for ($j=0;$j<sizeof($name);$j++)
        {
          if (isset($p[$name[$j]]))
          {
            $p=&$p[$name[$j]];
            if ($j == (sizeof($name)-1))
            {
              return $p;
            }
          }
          else
          {
            break;
          }
        }
      }
      else
      {
        if (isset($_POST[$name])){return $_POST[$name];}
      }
      break;
      case "g":
      if (is_array($name))
      {
        $p = &$_GET;
        for ($j=0;$j<sizeof($name);$j++)
        {
          if (isset($p[$name[$j]]))
          {
            $p=&$p[$name[$j]];
            if ($j == (sizeof($name)-1))
            {
              return $p;
            }
          }
          else
          {
            break;
          }
        }
      }
      else
      {
        if (isset($_GET[$name])){return $_GET[$name];}
      }
      break;

      case "c":
      if (is_array($name))
      {
        $allset = sizeof($name)>0;
        foreach($name as $kk=>$v)
          if (!isset($_COOKIE[$v])) $allset = false;
      }
      else
        $allset = isset($_COOKIE[$name]);
      if ($allset)
      {
        return $_COOKIE[$name];
      }
      break;
      case "s":
      if (is_array($name))
      {
        $p = &$_SESSION;
        for ($j=0;$j<sizeof($name);$j++)
        {
          if (isset($p[$name[$j]]))
          {
            $p=&$p[$name[$j]];
            if ($j == (sizeof($name)-1))
            {
              return $p;
            }
          }
          else
          {
            break;
          }
        }
      }
      else
      {
        //session_register($name);
        if (isset($_SESSION[$name])){return $_SESSION[$name];}
      }
      break;
      case "r":
      if (!is_array($name))
      {
        if (isset(CURLHandler::$rewriteParams[$name])){return CURLHandler::$rewriteParams[$name];}
      }
      break;
    }
  }
  return $defval;
}

function IsArrayValue(&$arr,$index,&$value = null)
{
  if (isset($arr[$index]))
  {
    if (strlen($arr[$index])>0)
    {
      $value = $arr[$index];
      return true;
    }
  }
  return false;
}


function CPArray(&$array,$elements = "*")
{
  $result = array();
  if (!is_array($array))
  {
    return $result;
  }

  $keys = array_keys($array);
  if (is_array($elements))
  {
    $keys = array_intersect($elements,$keys);
  }
  foreach ($keys as $key)
  {
    $result[$key] = $array[$key];
  }
  return  $result;
}

function CopyArray(&$source,&$destination)
{
  if ((!is_array($source))||(!is_array($destination)))
  {
    return false;
  }
  $skeys = array_keys($source);
  foreach ($skeys as $skey) {
    $destination[$skey] = $source[$skey];
  }
  return true;
}

function SetInArray(&$array,$key,$value)
{
  if (is_array($key))
  {
    $tarr = &$array;
    $s = sizeof($key);
    if ($s==0)
    {
      return ;
    }
    for ($i=0;$i<$s;$i++)
    {
      if ($i==$s-1)
      {
        $tarr[$key[$i]] = $value;
      }
      else
      {
        if (!isset($tarr[$key[$i]]))
        {
          $tarr[$key[$i]] = array();
        }
        if (!is_array($tarr[$key[$i]]))
        {
          $tarr[$key[$i]] = array();
        }
        $tarr = &$tarr[$key[$i]];
      }
    }
  }
  else
  {
    $array[$key] = $value;
  }
}

function SendHTMLMail($reciever,$message,$title="",$encoding="utf-8",$sender = DEFAULT_REPLY_ADDRESS,$reply=DEFAULT_REPLY_ADDRESS)
{
  $headers = "MIME-Version: 1.0" . "\r\n";
  $headers .= "Content-type: text/html; charset=$encoding" . "\r\n";
  //$headers .= "To: $reciever". "\r\n";
  $headers .= "From: EventCatalog.ru < $sender >" . "\r\n";
  $headers .= "Reply-to: $reply" . "\r\n";
  //$headers .= "X-Mailer: PHP/" . phpversion();

  $title="=?utf-8?B?".base64_encode($title)."?=";
  mail($reciever,$title,$message,$headers);
}

function IsNullOrEmpty($string)
{
  return is_null($string) || ($string=="");
}

function PrepareImagePathPart($part)
{
  return preg_replace("/[^A-Za-z0-9_\.]/","_",str_replace(IMAGE_PATH_SEMISECTOR,"_",$part));
}

function GetFilename($filename)
{
  $files = preg_split("/[\\/]/",$filename,-1,PREG_SPLIT_NO_EMPTY);
  return sizeof($files)==0?$filename:$files[sizeof($files)-1];
}

function CutString($str, $length = 50, $stopPoints = array(" ",".",","),$comm = "...")
{

  if (strlen($str)<=$length)
  return $str;
  $bp =0;
  for ($i=0;$i<strlen($str);$i++)
  {
    $ch = substr($str,$i,1);
    if (!(array_search($ch,$stopPoints)===false))
    {
      if ($i<$length)
      {
        $bp = $i+1;


      }
      else
      {
        return substr($str,0,$bp).$comm;
      }
    }
  }
}
function BreakString($str, $length = 50, $stopPoints = array(" ",".",","),$comm = "<br/>")
{

  if (strlen($str)<=$length)
  return $str;
  $bp =0;
  $istr = "";
  $fstr = "";
  $retstr = "";
  $delta = 0;
  for ($i=0;$i<strlen($str);$i++)
  {
    $ch = substr($str,$i,1);
    $istr.=$ch;
    $delta++;
    if (!(array_search($ch,$stopPoints)===false))
    {
      if ((strlen($istr)+strlen($fstr))<$length)
      {
        $fstr .= $istr;
        $istr = "";
      }
      else
      {
        $retstr.=$fstr.$comm;
        $fstr=$istr;
        $istr="";
      }
    }
  }
  if ((strlen($istr)+strlen($fstr))<$length)
  {
    $retstr.=$fstr.$istr;
  }
  else
  {
    $retstr.=$fstr.$comm.$istr;
  }
  return $retstr;
}

function ProcessMessage($text)
{
    return str_replace('  ', ' &nbsp;', nl2br(htmlspecialchars($text)));
}

function StripBadUTF8($str) { // (C) SiMM, based on ru.wikipedia.org/wiki/Unicode
$ret = '';
for ($i = 0;$i < strlen($str);) {
    $tmp = $str{$i++};
    $ch = ord($tmp);
    if ($ch > 0x7F) {
        if ($ch < 0xC0) continue;
        elseif ($ch < 0xE0) $di = 1;
        elseif ($ch < 0xF0) $di = 2;
        elseif ($ch < 0xF8) $di = 3;
        elseif ($ch < 0xFC) $di = 4;
        elseif ($ch < 0xFE) $di = 5;
        else continue;

        for ($j = 0;$j < $di;$j++) {
            $tmp .= $ch = $str{$i + $j};
            $ch = ord($ch);
            if ($ch < 0x80 || $ch > 0xBF) continue 2;
        }
        $i += $di;
    }
    $ret .= $tmp;
}
return $ret;
}

function translitURL($str)
{
  $tr = array(
      "À"=>"a","Á"=>"b","Â"=>"v","Ã"=>"g",
      "Ä"=>"d","Å"=>"e","¨"=>"e","Æ"=>"zh","Ç"=>"z","È"=>"i",
      "É"=>"y","Ê"=>"k","Ë"=>"l","Ì"=>"m","Í"=>"n",
      "Î"=>"o","Ï"=>"p","Ð"=>"r","Ñ"=>"s","Ò"=>"t",
      "Ó"=>"u","Ô"=>"f","Õ"=>"h","Ö"=>"ñ","×"=>"ch",
      "Ø"=>"sh","Ù"=>"w","Ú"=>"","Û"=>"y","Ü"=>"",
      "Ý"=>"e","Þ"=>"yu","ß"=>"ya","à"=>"a","á"=>"b",
      "â"=>"v","ã"=>"g","ä"=>"d","å"=>"e","¸"=>"e","æ"=>"zh",
      "ç"=>"z","è"=>"i","é"=>"y","ê"=>"k","ë"=>"l",
      "ì"=>"m","í"=>"n","î"=>"o","ï"=>"p","ð"=>"r",
      "ñ"=>"s","ò"=>"t","ó"=>"u","ô"=>"f","õ"=>"h",
      "ö"=>"c","÷"=>"ch","ø"=>"sh","ù"=>"w","ú"=>"",
      "û"=>"y","ü"=>"","ý"=>"e","þ"=>"yu","ÿ"=>"ya",
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

function getProType($resident_type, $resident_id){
  if (is_numeric($resident_id)){
      $res = SQLProvider::ExecuteQuery(
        "select pro_type from tbl__pro_accounts
         where to_resident_id = $resident_id
           and to_resident_type = '$resident_type'
           and payed = 1
           and date_end >= CURRENT_DATE
        order by date_end");
      if (is_array($res) && sizeof($res)>0)
        return $res[0]['pro_type'];
      else
        return 0;

		}
		else
		  return 0;
}
function getPro2List($resident_type)
{
  $info_field = "";  
  switch($resident_type){
    case 'contractor':
      $info_field = "ifnull(r.short_description,r.description)";
      break;
	// case 'area':
      // $info_field = "ifnull(r.short_description,r.description)";
      // break;
    default:
      $info_field = "r.description";
  }
  $logo_field = "";  
  switch($resident_type){
    case 'agency':
    case 'contractor':
      $logo_field = "r.logo_image";
      break;
    default:
      $logo_field = "r.logo";
  }
  $pro2_residents = SQLProvider::ExecuteQuery(
    "select r.title, $info_field info,
      r.title_url, pa.to_resident_type, $logo_field logo
    from tbl__pro_accounts pa
    join tbl__".$resident_type."_doc r on r.tbl_obj_id = pa.to_resident_id
    where to_resident_type = '$resident_type'
      and payed = 1
      and pro_type = 2
      and date_end >= CURRENT_DATE()
      and r.active = 1
    order by rand()");
  if (is_array($pro2_residents) && sizeof($pro2_residents))
    foreach($pro2_residents as &$pro_resident){
      $pro_resident['info'] = strip_tags($pro_resident['info']);
      if (strlen($pro_resident['info']) > PRO2_LIST_INFO_LENGTH)
        $pro_resident['info'] = substr($pro_resident['info'],0,PRO2_LIST_INFO_LENGTH)."...";
      $pro_resident['logo'] = GetFilename($pro_resident['logo']);
    }
  return $pro2_residents;
}

function getProBackgroud($resident_type)
{
  switch($resident_type){
    case 'contractor':	return '#eb880e';
    case 'area':		return '#3399FF';
    case 'artist':		return '#FF0066';
    case 'agency':		return '#99CC00';
  }
  // case 'contractor':	return '#F05620';
}

function getProLogoForPreview($resident_type) {
	switch($resident_type) {
		case 'contractor':	return '<img class="logo_wrap_pro" width="36" height="36" src="/images/pro/pro_1.png" />';
		case 'area':		return '<img class="logo_wrap_pro" width="36" height="36" src="/images/pro/pro_2.png" />';
		case 'artist':		return '<img class="logo_wrap_pro" width="36" height="36" src="/images/pro/pro_3.png" />';
		case 'agency':		return '<img class="logo_wrap_pro" width="36" height="36" src="/images/pro/pro_4.png" />';
	}
}

{
  return '<img src="/images/pro/pro.png" width="32" height="15" alt="PRO" style="margin:0 0 -3px 0;">';
}
function getProLogo()
{
  return '<img src="/images/pro/pro.png" width="32" height="15" alt="PRO" style="margin:0 0 -3px 0;">';
}
?>
