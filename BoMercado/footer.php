<?php
 
include_once ("includes/class.TemplatePower.inc.php");
 
$footer = new TemplatePower("./html/footer.htm");
$footer->prepare();


$header->printToScreen();
$content->printToScreen();
$footer->printToScreen();
?>