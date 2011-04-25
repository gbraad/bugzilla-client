<?php
include('Smarty.class.php');
require_once('config.php');
require_once('../PHP/BugzillaClient.php');

// create object
$smarty = new Smarty;
$smarty->template_dir = "./templates";

$id = $_GET["bugId"];

// values for the purpose of this example.
$smarty->assign('title', 'Bug: ' . $id);

$bzClient = new BugzillaClient($bzBaseUrl, $bzUsername, $bzPassword);

// populate the table
$smarty->assign('bugs', $bzClient->Get($id));

// display it
$smarty->display('single.tpl');
?>