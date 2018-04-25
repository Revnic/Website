<?php
include_once("includes/class.TemplatePower.inc.php");

$content= new TemplatePower("./html/reset_password.htm");
$content->prepare();

$key = $_GET['key'];

$sql_select = "SELECT `key` FROM `passwordToken` WHERE `KEY`='".$key."'" ;
$result = $conn->query($sql_select);

if($key != "" && $result->num_rows > 0 )
{
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$passw = $_POST["psw"];
		$passwCheck = $_POST["psw2"];
		
		$upload = 1;
		$passww = 1;
		$passw_match = 1;
		$passw_empty = 1;
		
		$sql = "SELECT * FROM `passwordToken` WHERE `key` = '".$_GET['key']."'";
		$result = $conn->query($sql);
		
		if($result->num_rows > 0)
		{
			//check password
			if(!empty($passw) && !empty($passwCheck))
			{
				if($passw == $passwCheck)
				{
					if(preg_match("#.*^(?=.{6,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$#", $passw))
					{
						//password hashing
						$hashPassword = password_hash($passw, PASSWORD_BCRYPT, array('cost' => 11));;
					}
					else
					{
						$passww = 2;
						$upload = 2; 
					}
				}
				else
				{
					$passw_match = 2;
					$upload = 2;
				}
			}
			else
			{
				$passw_empty = 2;
				$upload = 2;
			}
			
			if($upload == 2)
			{
				$content->newBlock( "warning_box" );
				if($passw_empty == 2){
					$content->newBlock( "passw" );
				}
				
				if($passw_match == 2){
					$content->newBlock( "passw_match" );
				}
				
				if($passww == 2){
					$content->newBlock( "passww" );
				}
			}	
			else
			{
				$row = $result->fetch_array(MYSQLI_ASSOC);
				$id = $row['account_idaccount'];
				
				$sql_update = "UPDATE `account` SET `password` = '".$hashPassword."' WHERE `idaccount`='".$id."'";
				if($conn->query($sql_update)=== TRUE){
					 
					$sql_delete = "DELETE FROM `passwordToken` WHERE `account_idaccount`= '".$id."'";
					$conn->query($sql_delete);
					
					header("Location: index.php?id=2");
				}
			} 
		}
		else
		{
			header("Location: index.php?id=404");
		}
	}
}
else
{
	header("Location: index.php?id=404");
}
?>