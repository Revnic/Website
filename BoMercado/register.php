<?php
include_once("includes/class.TemplatePower.inc.php");

$content= new TemplatePower("./html/register.htm");
$content->prepare();

$sql = "SELECT idcountry, name FROM country";
$result = $conn->query($sql);

if($result->num_rows > 0)
{
	while($row = $result->fetch_assoc())
	{
		$content->newBlock("option");
		$content->assign("name", $row["name"]);
		$content->assign("value", $row["idcountry"]);
	}
}


if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$name = $_POST["nomber"];
	$email = $_POST["email"];
	$emailCheck = $_POST["email2"];
	$passw = $_POST["psw"];
	$passwCheck = $_POST["psw2"];
	$optionCountry = $_POST["country"];
	$hashPassword = "";
	
	$upload = 1;
	$name_check = 1;
	$name_empty = 1;
	$email_check = 1;
	$email_match = 1;
	$email_empty = 1;
	$country = 1;
	$passw_empty = 1;
	$passw_match = 1;
	$agree = 1;
	$invalidChar = 1;
	$invalidMail = 1;
	$passww = 1;
	
	//check user agreement box is clicked
	if(!isset($_POST["agree"]))
	{
		$agree = 2;
		$upload = 2;
	}
	
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
	
	//check country 
	if($optionCountry == 0)
	{
		$country = 2;
	}
	
	//Check name if it exists
	if (!empty($name))
	{
		if(preg_match("/^[a-zA-Z0-9]+$/", $name))
		{
			$sql = "SELECT name FROM account WHERE name='".$name."'";
			$result = $conn->query($sql);
			
			if($result->num_rows >= 1)
			{
				 $name_check = 2;
				 $upload = 2;
			}
		}
		else
		{
			$invalidChar = 2;
			$upload = 2;
		}
		
	}
	else
	{
		$name_empty = 2;
		$upload = 2;
	}
	
	//Check email if it exists
	if (!empty($email) && !empty($emailCheck))
	{
		if(filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			if($email == $emailCheck)
			{
				$sql = "SELECT email FROM account WHERE email='".$email."'";
				$result = $conn->query($sql);
				
				if($result->num_rows >= 1)
				{
					 $email_check = 2;
					 $upload = 2;
				}
			}
			else
			{
				$email_match = 2;
				$upload = 2;
			}
		}
		else
		{
			$invalidMail = 2;
			$upload = 2;
		}
	}
	else
	{
		$email_empty = 2;
		$upload = 2;
	}
	
	$mailW = 0;
	$nameW = 0;
	if($upload == 2)
	{
		$content->newBlock( "warning_box" );
		if($name_check == 2){
			$content->newBlock( "nomber" );
			$mailW = 1;
		}
		
		if($invalidChar == 2){
			$content->newBlock( "invalidChar" );
			$mailW = 1;
		}
		
		if($name_empty == 2){
			$content->newBlock( "nomber_empty" );
			$mailW = 1;
		}
		
		if($email_check == 2){
			$content->newBlock( "email" );
			$nameW = 1;
		}
		
		if($invalidMail == 2){
			$content->newBlock( "invalidMail" );
			$nameW = 1;
		}
		
		if($email_match == 2){
			$content->newBlock( "email_match" );
			$nameW = 1;
		}
		
		if($email_empty == 2){
			$content->newBlock( "email_empty" );
			$nameW = 1;
		}
		
		if($country == 2){
			$content->newBlock( "country" );
			$mailW = 1;
			$nameW = 1;
		}
		
		if($passw_empty == 2){
			$content->newBlock( "passw" );
			$mailW = 1;
			$nameW = 1;
		}
		
		if($passw_match == 2){
			$content->newBlock( "passw_match" );
			$mailW = 1;
			$nameW = 1;
		}
		
		if($passww == 2){
			$content->newBlock( "passww" );
			$mailW = 1;
			$nameW = 1;
		}
		
		if($agree == 2){
			$content->newBlock( "agree" );
			$mailW = 1;
			$nameW = 1;
		}
		
		if($mailW == 1 ){
			$content->assign("_ROOT.email", $email);
			$content->assign("_ROOT.email2", $emailCheck);
		}
		
		if($nameW == 1){
			$content->assign("_ROOT.name", $name);
		}
	}
	else
	{
		$key= md5(uniqid(rand()));
		
		$sql_insert = "INSERT INTO `account`(`name`,`email`,`password`,`active`,`accountType_idaccountType`,`country_idcountry`)
		VALUES ('".$name."', '".$email."','".$hashPassword."',0,3,'".$optionCountry."')";
		
		if($conn->query($sql_insert) === TRUE)
		{
			$id = mysqli_insert_id($conn);
			
			$sql_token = "INSERT INTO `passwordtoken`(`key`, `account_idaccount`) VALUES ( '".$key."' , '".$id."')";
			
			if($conn->query($sql_token) === TRUE)
			{
				
				$to = $email;
				$subject = 'Bo Mercado activation';
				$message ="
				<html>
					<head>
					<title>HTML email</title>
					</head>
					<body align='center'>
						<table width='700' style='background-color:#ea9999;' >
							<td align='center'><img src='http://topsecret.brutalcoding.com/image/tent.png' alt='Logo' width='' height='80'></td>
							
						</table>
						<table width='700' style='border:1px solid #ea9999; padding: 20px;'>
							<tr>
								<td >
									<h1 style='color: #b30000;'>Welcome to Bo Mercado!</h1>
									<br>
									Thank you ".$name." for joining Bo mercado.<br>
									Please verify your account by clicking the following link:<br>
									<br>
									
									http://topsecret.brutalcoding.com/index.php?id=12&verify=".$key."<br><br>
									
									(if the link doesn't work, 'Copy and Paste' it into your browser's address bar)<br>
									<br>
									-The Bo Mercado team
									</p>
								</td>
							</tr>
						</table>
					</body>
				</html>
				";

				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$headers .= 'From: <no-reply@bomercado.com>' . "\r\n";

				mail($to, $subject, $message, $headers);
				
				$_SESSION["mail"] = $email;
				$_SESSION["name"] = $name;
				
				header("Location: Confirmation");
			}
			else
			{
				echo "error";
			}
		}
		else
		{	
			$content->newBlock("warning_box");
			$content->newBlock("database");
		}
		mysqli_close($conn);
	}
	
	/* $recaptcha=$_POST['g-recaptcha-response'];
	if(!empty($recaptcha))
	{
		include("getCurlData.php");
		$google_url="https://www.google.com/recaptcha/api/siteverify";
		$secret='6Lcu0yUTAAAAAFlnhMqpyxSYmqhYoIhRwHZZ4mNM';
		$ip=$_SERVER['REMOTE_ADDR'];
		$url=$google_url."?secret=".$secret."&response=".$recaptcha."&remoteip=".$ip;
		$res=getCurlData($url);
		$res= json_decode($res, true);
		//reCaptcha success check
		if($res['success'])
		{
			if($upload == 1)
			{
				//
			
			}
			
		}
		else
		{
			echo "<script type='text/javascript'>alert('Please re-enter your reCAPTCHA');history.go(-1);</script>";
		}
	}
	 else
	{
		echo "<script type='text/javascript'>alert('Please re-enter your reCAPTCHA');history.go(-1);</script>";
	} */
}
?>