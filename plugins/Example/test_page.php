<?php
require_once('../../libraries/lib.inc.php');
//
$misc->printHeader($lang['strdatabase']);
$misc->printBody();
$misc->printTopbar();
//
$plugin_manager->do_action($_REQUEST['plugin'], $_REQUEST['action']);
$misc->printFooter();
?>
