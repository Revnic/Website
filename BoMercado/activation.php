<?php
include_once("includes/class.TemplatePower.inc.php");

$content= new TemplatePower("./html/activation.htm");
$content->prepare();

$verify = $_GET["verify"];
	
$sql_verify = "SELECT `account_idaccount` FROM `passwordToken` WHERE `key`='".$verify."'";
$result = $conn->query($sql_verify);

if($_GET["verify"]!= "" && $result->num_rows > 0)
{
	$row = $result->fetch_array(MYSQLI_ASSOC);
	$id = $row['account_idaccount'];
	$sql_active = "UPDATE `account` SET `active` = 1 WHERE `idaccount`= ".$id."";
	
	if($conn->query($sql_active) === TRUE)
	{
		$sql_delete = "DELETE FROM `passwordToken` WHERE `account_idaccount`= '".$id."'";
		$conn->query($sql_delete);
		$content->newBlock("KLA");
		mysqli_close($conn);
	}
	else
	{
		$content->newBlock("ERROR");
	}
}
else
{
	header("Location: index.php?id=404");
}
?>