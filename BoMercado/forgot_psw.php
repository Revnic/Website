<?php
include_once("includes/class.TemplatePower.inc.php");

$content= new TemplatePower("./html/lostpassword.htm");
$content->prepare();

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$content->newBlock("warning_box");
	$content->newBlock("warning");
	
	$mail = $_POST['mail'];
	$sql_mail = "SELECT * FROM `account` WHERE `email`='".$mail."'";
	$result = $conn->query($sql_mail);
	
	if($result->num_rows > 0)
	{
		$row = $result->fetch_array(MYSQLI_ASSOC);
		$id = $row['idaccount'];
		
		$sql_check = "SELECT * FROM `passwordtoken` WHERE `account_idaccount`='".$id."'";
		$result = $conn->query($sql_check);
		
		$key = md5(uniqid(rand()));
		
		if($result->num_rows > 0)
		{
			$sql_update = "UPDATE `passwordtoken` SET `key`='".$key."' WHERE `account_idaccount`='".$id."'";
			$conn->query($sql_update);
		}
		else
		{
			$sql_insert = "INSERT INTO `passwordtoken`(`key`, `account_idaccount`) VALUES ( '".$key."' , '".$id."')";
			$conn->query($sql_insert);
		}
		
		$to = $mail;
		$subject = "Reset password";

		$message = "
		<html>
			<head>
			<title>HTML email</title>
			</head>
			<body align='center'>
				<table width='700' style='border:1px solid #ea9999;padding: 20px;'>
					<tr>
						<td>
							<h1 style='color: #b30000;'>Reset password</h1>
							<p>Hello,<br>
							<br>
							Click the link below to reset your password<br>
							<br>

							http://topsecret.brutalcoding.com/index.php?id=14&key=".$key."<br><br>

							(if the link doesn't work, 'Copy and Paste' it into your browser's address bar)<br>
							<br>
							
							If you didn't request to reset a password please just ignore this mail.

							</p>
						</td>
					</tr>
				</table>
			</body>
		</html>
		";
		
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= 'From: <no-reply@bomercado.com>' . "\r\n";

		mail($to, $subject, $message, $headers);
		
	}
	else
	{
		echo "error";
	}
	
}

?>