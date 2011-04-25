<?php
include('Smarty.class.php');
require_once('config.php');
require_once('../PHP/BugzillaClient.php');

// create object
$smarty = new Smarty;
$smarty->template_dir = "./templates";

$query = 'NEED_INFO';
$smarty->assign('title', $query);

$bzClient = new BugzillaClient($bzBaseUrl, $bzUsername, $bzPassword);

// populate the table
$smarty->assign('bugs', $bzClient->Search('', $query, '', '', 'QA'));

// display it
$smarty->display('list.tpl');
?>
