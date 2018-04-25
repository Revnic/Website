<?php
error_reporting(E_ALL & ~E_NOTICE);
date_default_timezone_set("America/Aruba");
session_start();

include("db/connect.php");
include_once("includes/class.TemplatePower.inc.php");

$header = new TemplatePower("./html/header.htm");
$header->prepare();

mysqli_select_db($conn,"webpage");


$name = $_SESSION['name'];
$account = $_SESSION['accountType'];

if($account > 0)
{
	$header->assign("_ROOT.login", "sign out" );
	$header->assign("_ROOT.link", "index.php?id=13" );
	$header->assign("_ROOT.name", "(Bon Bini ".$name. ")" );
	
}
else
{
	$header->assign("_ROOT.login", "Login" );
	$header->assign("_ROOT.link", "index.php?id=2" );
}

?>