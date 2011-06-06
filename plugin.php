<?php
require_once('./libraries/lib.inc.php');
//
$misc->printHeader($lang['strdatabase']);
$misc->printBody();
$misc->printTrail($_REQUEST['subject']);
//
$plugin_manager->do_action($_REQUEST['plugin'], $_REQUEST['action']);
$misc->printFooter();
?>
