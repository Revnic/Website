<?php
include_once("includes/class.TemplatePower.inc.php");

$content= new TemplatePower("./html/your_products.htm");
$content->prepare();

$idaccount = $_SESSION['idaccount'];


if(!empty($idaccount))
{
	
}
else
{
	header("Location: index.php?id=2");
}

?>