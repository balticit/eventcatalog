<?php
//Core defines
	error_reporting(E_ALL);
	define("DEBUG",false);
	define("ROOTDIR",realpath(dirname(realpath(__FILE__))).'/');
	define("TMP_DIR",ROOTDIR."tmp/");
	define("CONTROL_SEMISECTOR","$");
	define("DEFAULT_XML_ENCODING","utf-8");
	define("DEFAULT_HTML_ENCODING","utf-8");
//SQL	
	define("MYSQL_HOST","127.0.0.1");
	define("MYSQL_USER","eventcatalog");
	define("MYSQL_DATABASE","eventcatalog");
	define("MYSQL_PASSWORD","_g8KaCwFh_Fs9i_n23Q-nxaW");
	define("MYSQL_CHARSET","cp1251");
	define("MYSQL_PORT","3306");
//SMTP	
	define("DEFAULT_REPLY_ADDRESS","catalog@eventcatalog.ru");
//Security
	define("SESSION_USER_STORAGE","SESSION_USER_STORAGE");
//User Images uploads
	define("IMAGES_UPLOAD_DIR","application/public/upload/");
	define("COMMENT_IMAGES_UPLOAD_DIR",IMAGES_UPLOAD_DIR."comments/");
	define("IMAGE_PATH_SEMISECTOR","-");
	define("IMAGE_LOGO_SIZE_LIMIT",20480);
	define("IMAGE_SIZE_LIMIT",102400);
	define("IMAGE_LAGRE_SIZE_LIMIT",512000);
	define("VIDEO_SIZE_LIMIT",51200000);
	define("MAX_UPLOAD_FILE_SIZE",20971520);
	define("IMAGE_LOGO_PREFIX","logo");
	define("IMAGE_MENU_PREFIX","menu");
	define("IMAGE_MAP_PREFIX","map");
	define("IMAGE_MP3_PREFIX","mp3");
	define("IMAGE_BASE_PREFIX","image");
	define("UPLOAD_BASE_PREFIX","file");
	define("IMAGE_FOTO_PREFIX","foto");
	define("IMAGE_VIDEO_PREFIX","video");
//image sizes	
	include_once("upload_image_sizes.php");
//filters
	define("FILTER_LETTERS","1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZÀÁÂÃÄÅ¨ÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÝÞß");
//Google map key
	define("GOOGLEMAPKEY","ABQIAAAANFini5Xz0nFg8z7jLQP1SxSB2bIXRppcnU4lfC1EQkS1zc6t-RRSZil5OqAKMijn5PIRYg8uMXmqtA");
//CMS defines
//DateTime Picker
define("DATE_TIME_PICKER_SCRIPT_PATH","/js/datetimepicker.js") ;
define("DATE_TIME_PICKER_IMAGE_PATH","/images/cal.gif") ;
//Tiny MCE
define("TINYMCE_SCRIPT_PATH","/js/tiny_mce/tiny_mce.js") ;
define("TINYMCE_DEFAULT_LANGUAGE","ru_CP1251");	
//autoloading
function AddAutoload($path = null,$class = null)
{	
	static $paths;

	if (!is_array($paths))
	{
		$paths = array();
	}
	
	if ((is_null($class))&&(!is_null($path)))
	{
		//print $path."<br/>";
		
		$paths[md5($path)] = $path;
		if (defined("FULL_CLASS_LOAD"))
		{
			LoadDir($path);
		}
	}
	
	if ((!is_null($class))&&(is_null($path)))
	{
		$_class = preg_replace("/_php$/",".php",$class);
		if ($_class==$class)
		{
			$_class.=".php";
		}
		
		foreach ($paths as $_path) {
			
		//print $class." ".$_path.$_class."<br/>";
			if (is_file($_path.$_class))
			{
				include_once($_path.$_class);
			}
		}
	}
}

function  __autoload($class_name) {
    AddAutoload(null,$class_name);
}

//includes
	include(ROOTDIR."classes/index.php");
	include(ROOTDIR."pagecode/functions.php");
	include(ROOTDIR."pagecode/index.php");
?>
