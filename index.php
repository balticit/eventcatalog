<?php
session_start();
try{
  include_once("defines.php");
  include_once("pro_defines.php");
  $Application = CApplicationContext::GetInstance();
  $Application->Start(ROOTDIR."pagecode/config/sitemap.xml");
  $Application->End();
}
catch (Exception  $ex){
  print_r($ex);
}
?>
