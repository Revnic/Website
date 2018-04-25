<?php
error_reporting(E_ALL & ~E_NOTICE);
date_default_timezone_set("America/Aruba");
session_start();

include("db/connect.php");
include_once("includes/class.TemplatePower.inc.php");

$header = new TemplatePower("./html/header.htm");
$header->prepare();

mysqli_select_db($conn,"webpage");

$time = date("H");
$greeting = "";

if($time >= 06 && $time <= 12)
{
	$greeting = "Bon Dia";	
}
elseif($time >= 13 && $time <= 18)
{
	$greeting = "Bon Tardi";	
}
elseif($time >= 19 && $time <= 21)
{
	$greeting = "Bon Nochi";	
}
elseif($time >= 22 && $time <= 24)
{
	$greeting = "Ora Di Drumi";	
}
elseif($time >= 00 && $time <= 03)
{
	$greeting = "Serio bai Drumi";	
}
elseif($time >= 04 && $time <= 05)
{
	$greeting = "Bo no ta drumi?";	
}

$name = $_SESSION['name'];
$account = $_SESSION['accountType'];

if($account > 0)
{
	$header->assign("_ROOT.login", "sign out" );
	$header->assign("_ROOT.link", "index.php?id=13" );
	$header->assign("_ROOT.name", "(".$greeting.") ".$name."" );
	
}
else
{
	$header->assign("_ROOT.login", "Login" );
	$header->assign("_ROOT.link", "Log-in" );
}

?>