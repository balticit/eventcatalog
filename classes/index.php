<?php
	include_once(ROOTDIR."classes/Misc.php");
	include_once(ROOTDIR."classes/xmlparser/xmlparser.php");
	include_once(ROOTDIR."classes/sql/SQLProvider.php");
	include_once(ROOTDIR."classes/IPropertyOverloaded.php");
	include_once(ROOTDIR."classes/CEventHandler.php");
	include_once(ROOTDIR."classes/CObject.php");
	include_once(ROOTDIR."classes/CStringFormatter.php");
	include_once(ROOTDIR."classes/CClassFactory.php");
	include_once(ROOTDIR."classes/CApplicationContext.php");
	include_once(ROOTDIR."classes/CSiteMapHandler.php");
	include_once(ROOTDIR."classes/CPageBuilder.php");
	include_once(ROOTDIR."classes/CURLHandler.php");
	include_once(ROOTDIR."classes/CRenderer.php");
	include_once(ROOTDIR."classes/CDebugger.php");
	AddAutoload(ROOTDIR."classes/");
	AddAutoload(ROOTDIR."classes/sql/");
	AddAutoload(ROOTDIR."classes/data/");
	AddAutoload(ROOTDIR."classes/data/filters/");
	AddAutoload(ROOTDIR."classes/html/");
	AddAutoload(ROOTDIR."classes/html/simple/");
	AddAutoload(ROOTDIR."classes/security/");
	AddAutoload(ROOTDIR."classes/validation/");
	/*
	include_once(ROOTDIR."classes/data/index.php");
	include_once(ROOTDIR."classes/html/index.php");
	include_once(ROOTDIR."classes/security/index.php");
	;*/
?>
