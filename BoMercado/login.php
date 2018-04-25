<?php
include_once("includes/class.TemplatePower.inc.php");

$content= new TemplatePower("./html/login.htm");
$content->prepare();

if(isset($_POST['submit_remail']))
	{
		$mail = $_POST['email'];
		$sql_token ="SELECT * FROM  `account` WHERE  `email` = '".$mail."'";
		$result = $conn->query($sql_token);
		
		if($result->num_rows > 0)
		{	
			$row =$result->fetch_array(MYSQLI_ASSOC);
			$id= $row['idaccount'];
			$name = $row['name'];
			$sql_delete = "DELETE FROM `passwordToken` WHERE `account_idaccount`= '".$id."'";
			$conn->query($sql_delete);
			
			$key = md5(uniqid(rand()));
			$sql = "INSERT INTO `passwordToken`(`key`, `account_idaccount`) VALUES ( '".$key."' , '".$id."')";
			$conn->query($sql);

			$to = $mail;
			$subject = "Bo Mercado re-send account activation '" . $name . "'";

			$message = "
			<html>
				<head>
				<title>HTML email</title>
				</head>
				<body align='center'>
					<table width='700' style='background-color:#ea9999;' >
						<td align='center'><img src='http://topsecret.brutalcoding.com/image/tent.png' alt='Logo' width='' height='80'></td>
					</table>
					<table width='700' style='border:1px solid #ea9999;padding: 20px;'>
						<tr>
							<td>
								<h1 style='color: #b30000;'>Re-send account activation</h1>
								<p>Hello " . $name . ",<br>
								<br>
								Here is your new activation link:<br>
								<br>

								http://topsecret.brutalcoding.com/index.php?id=12&verify=" . $key . "<br><br>

								(if the link doesn't work, 'Copy and Paste' it into your browser's address bar)<br>
								<br>

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
	
	}


if(isset($_POST['submit']))
{
	
	$mail = $_POST["email"];
	$passw = $_POST["password"];
	
	$sql_get = "SELECT * FROM `account` WHERE `email`='".$mail."'";
	$result = $conn->query($sql_get);
	if($result->num_rows > 0)
	{
		$row = $result->fetch_array(MYSQLI_ASSOC);
		$passw_db = $row['password'];
		$active = $row['active'];
		$name = $row['name'];
		$accountType = $row['accountType_idaccountType'];
		$idaccount = $row['idaccount'];
		
		if(password_verify($passw, $passw_db))
		{
			if($active != 0)
			{
				$_SESSION['name'] = $name;
				$_SESSION['accountType'] = $accountType;
				$_SESSION['idaccount'] = $idaccount;
				
				header("Location: index.php?id=1");
			}
			else
			{
				// go to page to activate
				$content->newBlock("warning_box");
				$content->newBlock("activa");
			}
		}
		else
		{
			$content->newBlock("warning_box");
			$content->newBlock("warning");
		}
	}
	else
	{
		$content->newBlock("warning_box");
		$content->newBlock("warning");
	}
	
	
}

?>