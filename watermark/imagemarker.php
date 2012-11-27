<?php
error_reporting(E_ERROR);
require_once("watermark.php");
define("ROOTDIR",rtrim($_SERVER['DOCUMENT_ROOT'],"/\\\\")."/");
define("IMAGES_UPLOAD_DIR","application/public/upload/".($_GET["comments"]==1?"comments/":""));
$imagename = $_GET["request"];
$imgs = preg_split("/\//",$imagename);
$imagename = $imgs[sizeof($imgs)-1];
$resizex = sizeof($imgs)>2?$imgs[0]:0;
$resizey = sizeof($imgs)>2?$imgs[1]:0;
if ((!file_exists(ROOTDIR.IMAGES_UPLOAD_DIR.$imagename))
	&&(isset($_SERVER["REDIRECT_QUERY_STRING"]))
	&&(strlen($_SERVER["REDIRECT_QUERY_STRING"])>8))
{
	$imagename = substr($_SERVER["REDIRECT_QUERY_STRING"],8);
	$imgs = preg_split("/\//",$imagename);
	$imagename = $imgs[sizeof($imgs)-1];
	$resizex = sizeof($imgs)>2?$imgs[0]:0;
	$resizey = sizeof($imgs)>2?$imgs[1]:0;
}
$exts = preg_match("/.(\\w+)$/i",$imagename,$matches);
$ext = strtolower($matches[1]);
$img = null;
if (!file_exists(ROOTDIR.IMAGES_UPLOAD_DIR.$imagename))
{
	header("HTTP/1.0 404 Not Found");
	die();
}
switch ($ext) {
	case "jpeg":
	case "jpg":
		$img = imagecreatefromjpeg(ROOTDIR.IMAGES_UPLOAD_DIR.$imagename);
		break;
	case "png":
		$img = imagecreatefrompng(ROOTDIR.IMAGES_UPLOAD_DIR.$imagename);
		break;
	case "gif":
		$img = imagecreatefromgif(ROOTDIR.IMAGES_UPLOAD_DIR.$imagename);
		break;
}

if (($resizex>0)&&($resizey>0))
{
	$newimg=@imagecreatetruecolor($resizex,$resizey);
	@imagecopyresampled($newimg,$img,0,0,0,0,$resizex,$resizey,imagesx($img),imagesy($img));
	$img=$newimg;
}
$wm = null;
$wm_width = imagesx($img);
$wm_height = imagesy($img);

if (($wm_width>260)||($wm_height>260))
{
	$wm = watermark::create_plain_watermark($img,"",ROOTDIR."watermark/font.ttf",255,255,255,100);
}
else
{
	$wm = $img;
}
switch ($ext) {
	case "jpeg":
	case "jpg":
		header('Content-type: image/jpeg');
		imagejpeg($wm, null, 85);
		break;
	case "png":
		header('Content-type: image/png');
		imagepng($wm);
		break;
	case "gif":
		header('Content-type: image/gif');
		imagegif($wm);
		break;
}
?>
