<?php
include_once("includes/class.TemplatePower.inc.php");

$content= new TemplatePower("./html/account.htm");
$content->prepare();

$idaccount = $_SESSION['idaccount'];


if(!empty($idaccount))
{
	// get user info
	
	$sql_select = "SELECT * FROM account WHERE idaccount = '".$idaccount."'";
	$r_select = $conn->query($sql_select);
	
	$row = $r_select->fetch_assoc();
	$content->assign(array(	name => $row['name'],
							street => $row['street_name'],
							user_mail => $row['email']));
							
	if($row['phonenr'] == 0)
	{
		$content->assign("phone", "");
	}
	else
	{
		$content->assign("phone", $row['phonenr']);
	}
	
	$uploadData = 1;
	$uploadMail = 1;
	
	$name_check = 1;
	$name_empty = 1;
	$invalidChar = 1;
	$email_check = 1;
	$email_match = 1;
	$email_empty = 1;
	$invalidMail = 1;
	
	if(isset($_POST['data']))
	{
		if(!empty($_POST['name']))
		{
			if(preg_match("/^[a-zA-Z0-9]+$/", $_POST['name']))
			{
				$sql_name = "SELECT `name`, `idaccount` FROM `account` WHERE `name`= '".$_POST['name']."'";
				$result_name = $conn->query($sql_name);
				$id = $result_name->fetch_assoc();
				
				if(!empty($id['idaccount']))
				{
					if($id['idaccount'] != $idaccount)
					{
						$uploadData = 0;
						$name_check = 0;
					}
				}
			}
			else
			{
				$uploadData = 0;
				$invalidChar = 0;
			}
		}
		else
		{
			$uploadData = 0;
			$name_empty = 0;
		}
		
		if($uploadData == 1)	
		{
			$sql_update="UPDATE `account` SET `name`='".$_POST['name']."',
			`street_name`='".$_POST['street-name']."', `phonenr`='".$_POST['phone']."' 
			WHERE `idaccount`= '".$idaccount."'";
			$conn->query($sql_update);
			$content->newBlock("msg_box_green");
			$content->newBlock("DATA");
			header("Refresh:3");
		}
		else
		{
			$content->newBlock( "warning_box" );
			if($name_check == 0){
				$content->newBlock( "nomber" );
			}
			
			if($name_empty == 0){
				$content->newBlock( "nomber_empty" );
			}
			
			if($invalidChar == 0){
				$content->newBlock( "invalidChar" );
			}
		}
	}
	
		
	if(isset($_POST['mail']))
	{
		if (!empty($_POST['new_email']) && !empty($_POST['second_new_email']))
		{
			if(filter_var($_POST['new_email'], FILTER_VALIDATE_EMAIL))
			{
				if($_POST['new_email'] == $_POST['second_new_email'])
				{
					$sql = "SELECT email FROM account WHERE email='".$_POST['new_email']."'";
					$result = $conn->query($sql);
					
					if($result->num_rows >= 1)
					{
						 $email_check = 0;
						 $uploadMail = 0;
					}
				}
				else
				{
					$email_match = 0;
					$uploadMail = 0;
				}
			}
			else
			{
				$invalidMail = 0;
				$uploadMail = 0;
			}
		}
		else
		{
			$email_empty = 0;
			$uploadMail = 0;
		}
		
		if($uploadMail == 1)	
		{
			$sql_update="UPDATE `account` SET `email`='".$_POST['new_email']."'
			WHERE `idaccount`= '".$idaccount."'";
			$conn->query($sql_update);
			$content->newBlock("msg_box_green");
			$content->newBlock("MAILOK");
			header("Refresh:3");
		}
		else
		{
			$content->newBlock( "warning_box" );
			if($email_check == 0){
				$content->newBlock( "email" );
			}
		
			if($invalidMail == 0){
				$content->newBlock( "invalidMail" );
			}
			
			if($email_match == 0){
				$content->newBlock( "email_match" );
			}
			
			if($email_empty == 0){
				$content->newBlock( "email_empty" );
			}
		}
	}
	
}
else
{
	header("Location: Log-in");
}
?>