<?php
/*======================================================================*\
|| #################################################################### ||
|| # vBulletin 3.8.2 - Licence Number VBF18F38CE
|| # ---------------------------------------------------------------- # ||
|| # Copyright �2000-2009 Jelsoft Enterprises Ltd. All Rights Reserved. ||
|| # This file may not be redistributed in whole or significant part. # ||
|| # ---------------- VBULLETIN IS NOT FREE SOFTWARE ---------------- # ||
|| # http://www.vbulletin.com | http://www.vbulletin.com/license.html # ||
|| #################################################################### ||
\*======================================================================*/

// ######################## SET PHP ENVIRONMENT ###########################
error_reporting(E_ALL & ~E_NOTICE);
if (!is_object($vbulletin->db))
{
	exit;
}

// ########################## REQUIRE BACK-END ############################
require_once(DIR . '/includes/functions_digest.php');

// ########################################################################
// ######################### START MAIN SCRIPT ############################
// ########################################################################

// send weekly digest of new posts in threads and threads in forums
exec_digest(3);

log_cron_action('', $nextitem, 1);

/*======================================================================*\
|| ####################################################################
|| # Downloaded: 05:33, Fri Apr 3rd 2009
|| # CVS: $RCSfile$ - $Revision: 13893 $
|| ####################################################################
\*======================================================================*/
?>