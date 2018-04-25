<?php
include_once("includes/class.TemplatePower.inc.php");

$content= new TemplatePower("./html/confirm.htm");
$content->prepare();

$mail = $_SESSION["mail"];
$name = $_SESSION["name"];

if($mail != "")	
{
	//check account if active
	$sql_active = "SELECT `active` FROM `account` WHERE `email`='".$mail."'";
	$rslt= mysqli_query($conn,$sql_active);
	$row_active =$rslt->fetch_array(MYSQLI_ASSOC);
	if($row_active["active"] == 0)
	{
		$content->newBlock("KLA");
		$content->assign("useremail", $mail);
		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$sql_token ="SELECT `idaccount` FROM  `account` WHERE  `email` = '".$mail."'";
			$result = $conn->query($sql_token);
			
			if($result->num_rows > 0)
			{	
				$row =$result->fetch_array(MYSQLI_ASSOC);
				$id= $row['idaccount'];
				$sql_delete = "DELETE FROM `passwordToken` WHERE `account_idaccount`= '".$id."'";
				if ($conn->query($sql_delete) === TRUE) 
				{
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
				else
				{
					echo "error";
				}
				
			}
		}
	}
	else
	{
		$content->newBlock("TEY");
	}
}
else
{
	header("Location: index.php?id=404");
}
?>