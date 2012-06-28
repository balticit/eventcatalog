<?php
//cms defines
include_once(ROOTDIR . "cms/defines.php");
//base classes
AddAutoload(ROOTDIR . "cms/classes/");
AddAutoload(ROOTDIR . "cms/classes/gridview/");
include_once(ROOTDIR . "cms/classes/misc.php");
//handlers
AddAutoload(ROOTDIR . "cms/pages/");
ini_set('display_errors', 1);
$importer = new DendroImporter();
//$importer->Public_Topics_Order();
//$importer->Ad_blocks_subscribe();
////$importer->EventTV();
$importer->AddArtistColumns();
$importer->ArtistGroupTitleUrl();
$importer->AddEventTvTitleUrl();
$importer->AddBookTitleUrl();
$importer->addCreationDateColumn();
$importer->fixTranslit();
$importer->fixCatUrl();
$importer->NewCMSUser();
$importer->MergeFixes();
//$importer->changeLogin();
//$importer->recreateAllusersView();
//$importer->UserLike();
//$importer->Artist_City();
//$importer->Agency_Adds();
//$importer->Msg_Read();
//$importer->Agency_View();
//$importer->Area_Artist_Agency_Fields();
//$importer->AreaView();
//$importer->ArtistStyles();
//$importer->EventTVmain();
//$importer->AgencyRecreateView3();
//$importer->ContractorRecreateView();
//$importer->ArtistAreaCMSViews();
//$importer->PublicEventTvDates();
//$importer->ColsTranslit();

//$importer->ResidentsRegDate();
//$importer->EventTVNewRubrics();
//$importer->ResidenNullFields();
//$importer->NewCMSUser();
//$importer->AgencyTextType();

//$importer->FirstResident();
//$importer->refillTranslit();

//$importer->AgencyPageTitle();
$importer->AddProAccount();

$importer->AddSeoFields();

ini_set("session.gc_maxlifetime", 10800);


session_set_cookie_params(10800);

?>
