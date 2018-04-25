<?php
include_once("includes/class.TemplatePower.inc.php");
include_once("function/priceConverter.php");
session_start();

$content= new TemplatePower("./html/product_view.htm");
$content->prepare();


//Get category
$sql = "SELECT * FROM `category` ORDER BY `name`";
$result = $conn->query($sql);

while($row = $result->fetch_assoc())
{
	$content->newBlock('CATEGORY_TITLE');
	$content->assign( array(title => $row['name'],
							id => $row['idcategory']));
							
	
	$content->newBlock('CATEGORY_BAR');
	$content->assign( array(title_b => $row['name'],
							id_b => $row['idcategory']));
}

//Get country
$sql = "SELECT * FROM `country` ORDER BY `name`";
$result = $conn->query($sql);

while($row = $result->fetch_assoc())
{
	$content->newBlock('COUNTRY_BAR');
	$content->assign( array(country => $row['name'],
							id_c => $row['idcountry']));
}


$idMember = $_SESSION['idaccount'];
$idproduct = "";
$sellerid = "";
$title = "";
$currency = "";


$ip = $_SERVER['REMOTE_ADDR'];
$sql_ip = "SELECT * FROM `view` WHERE `ipaddress` = '".$ip."' AND `product_idproduct`= '".$_GET['product']."'";
$r_ip = $conn->query($sql_ip);

if($r_ip->num_rows == 0)
{
	$sql_insert = "INSERT INTO `view`(`ipaddress`, `product_idproduct`) 
					VALUES('".$ip."', '".$_GET['product']."')";
	$conn->query($sql_insert);
}

//amount views
$sql_view = "SELECT idview FROM `view` WHERE `product_idproduct`= '".$_GET['product']."'";
$r_view = $conn->query($sql_view);
$rowcount = $r_view->num_rows;
$content->assign("_ROOT.count", $rowcount);
	
//amount fav
$sql_select = "SELECT * FROM `favourite` 
WHERE `product_idproduct` = '".$_GET['product']."' ";
$result = $conn->query($sql_select);
$fav_count = $result->num_rows;
$content->assign("_ROOT.fav_count", $fav_count);

 

//query pa check si e exist↓

if(!empty($_GET['product']))
{
	$content->assign("_ROOT.product", $_GET['product']);
	
	$sql_prod = "SELECT * FROM `product` 
	INNER JOIN `country` 
	ON product.country_idcountry = country.idcountry 
	WHERE `idproduct`='".$_GET['product']."'";
	
	$result = $conn->query($sql_prod);
	$row = $result->fetch_array(MYSQLI_ASSOC);

	$title = $row['title'];
	$description = $row['description'];
	$condition = $row['condition'];
	$price = $row['price'];
	$date = $row['date'];
	$delivery = $row['delivery'];
	$smail = $row['show_mail'];
	$sphone = $row['show_phone'];
	$category = $row['category_idcategory'];
	$country = $row['country_idcountry'];
	$sellerid = $row['account_idaccount'];
	$auction = $row['auction'];
	$idproduct = $row['idproduct'];
	$currency = $row['currency'];
	$free = $row['free'];
	
	$_SESSION['idproduct'] = $idproduct;
	
	//fav
	$sql_select = "SELECT * FROM `favourite` 
	WHERE `account_idaccount`= '".$idMember."'
	AND `product_idproduct` = '".$idproduct."' ";
	$result = $conn->query($sql_select);
	
	if($result->num_rows > 0)
	{
		//favourite
		$content->assign('_ROOT.fav', "image/star_yellow.png");
		$content->assign('_ROOT.fav_title', "Warda den bo favorito");
		
	}
	else
	{
		//not favourite
		$content->assign('_ROOT.fav', "image/star_gray.png");
		$content->assign('_ROOT.fav_title', "Warde den bo favorito");
	}
	
	//product date
	$newDate = date("d F Y", strtotime($date));

	$content->assign("_ROOT.title", $title);
	$content->assign("_ROOT.date", $newDate);
	
	if($price != 0)
	{
		$content->assign("_ROOT.price", priceConverter($price));
		$content->assign("_ROOT.currency", $currency);
	}
	elseif($auction == 1 && $price == 0)
	{
		$content->assign("_ROOT.price", "Findishi");
	}
	elseif($free == TRUE)
	{
		$content->assign("_ROOT.price", "Gratis");
	}
	else
	{
		$content->assign("_ROOT.price", "Puntra pa prijs");
	}
	
	if($condition != 1)
	{
		$content->newBlock('CONDITION');
		$content->assign("condition", $condition);
	}
	
	if($delivery != 1)
	{
		$content->newBlock('ENTREGA');
		$content->assign("delivery", $delivery);
	}
	$content->assign("_ROOT.description", $description);
	
	

	//seller info
	$sql_seller = "SELECT `name`, `email`, `street_name` FROM `account` WHERE `idaccount`= '".$sellerid."'";
	$r_seller = $conn->query($sql_seller);

	$row_seller = $r_seller->fetch_assoc();
	$sellerName = $row_seller['name'];
	$sellerEmail = $row_seller['email'];
	
	if(!empty($idMember))
	{
		$sellerStreet = $row_seller['street_name'];
	}
	if($sellerStreet != '')
	{
		$content->newBlock("LOCATION");
		$content->assign("location", $sellerStreet);
	}

	$content->assign("_ROOT.seller-name", $sellerName);

	//user info
	$sql_user = "SELECT `name`, `email`, `phonenr`, `street_name` FROM `account` WHERE `idaccount`= '".$idMember."'";
	$r_user = $conn->query($sql_user);

	$row_user = $r_user->fetch_assoc();
	$userName = $row_user['name'];
	$userEmail = $row_user['email'];
	$userPhone = $row_user['phonenr'];

	$content->assign("_ROOT.user-name", $userName);
	$content->assign("_ROOT.user-mail", $userEmail);
	$content->assign("_ROOT.user-phone", $userPhone);


	$sql_image = "SELECT `url` FROM `imageupload` WHERE `product_idproduct`= '".$_GET['product']."'";
	$result_image = $conn->query($sql_image);
	if($result_image->num_rows > 0)
	{
		While($row_image = $result_image->fetch_assoc())
		{
			$content->newBlock('IMAGE-BOX');
			$content->assign("image-url", $row_image['url']);
		}
	}
	else
	{
		$content->newBlock('EMPTY-IMAGE-BOX');
	}

	if($auction == 1)
	{
		$content->newBlock('AUCTION_BOX');
		
		if($price != 0)
		{
			$content->assign(array(price2 => priceConverter($price),
								currency2 => "ta cuminsa na: <br> ".$currency));
		}
		
	}

	if(!empty($idMember))
	{
		if($smail == '1')
		{
			$content->newBlock('EMAIL');
		}
		
		if($sphone == '1')
		{
			$content->newBlock('PHONE');
		}
		
		$content->newBlock('AUCTION');
		$content->newBlock('TEST_AUCTION_BOX');
	}
	else
	{
		$content->newBlock('LOGINMSG');
		if($auction == 1)
		{
			$content->newBlock('TEST_AUCTION_BOX');
			$content->assign("disable", "disabled");
			$content->assign("hide", "hidden");
		}
		
	}
}
Else
{
	header("Location: Error-404");
}

If(isset($_POST['sendMail']))
{
	$sql_seller = "SELECT `email` FROM `account` WHERE `idaccount`= '".$sellerid."'";
	$r_seller = $conn->query($sql_seller);

	$row_seller = $r_seller->fetch_assoc();
	$sellerEmail = $row_seller['email'];
	
	$sql_user = "SELECT`email` FROM `account` WHERE `idaccount`= '".$idMember."'";
	$r_user = $conn->query($sql_user);

	$row_user = $r_user->fetch_assoc();
	$userEmail = $row_user['email'];
	
	$phone= "";
	if($_POST['phone'] != 0){
		$phone = "<br> Telefoon: ".$_POST['phone'];
	}
	
	$contact = "<br><br> Contacto:<br> E-mail: ".$userEmail."".$phone."";
	
	$to = $sellerEmail;
	$subject = $_POST['tema'];
	$message = $_POST['txtarea-mail'].$contact ;
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	$headers .= 'From: Mensaje via Bo Mercado <'.$userEmail.'>' . "\r\n";

	mail($to, $subject, $message, $headers);

	
}


// bid settings
If(isset($_POST['bidButton']))
{
	if($_POST['bid'] != 0)
	{
		$sql_auction = "SELECT * FROM `auction` WHERE `product_idproduct` = '".$idproduct."'";
		$r_auction = $conn->query($sql_auction);
		$row_auc = $r_auction->fetch_assoc();
		$auctionId = $row_auc['idauction'];
		
		$date = date("Y-m-d");
		
		$sql_bid = "SELECT MAX(amount) FROM `bid` WHERE `auction_idauction`='".$auctionId."'";
		$result = $conn->query($sql_bid);
		$bid_r = $result->fetch_assoc();
		
		if($_POST['bid'] <= $bid_r['MAX(amount)'])
		{
			$content->assign('ERROR-MSG', "Bo no por pone un oferta mas abow of iqual cu e ultimo oferta.");
		}
		else
		{
			
			$sql_insert = "INSERT INTO `bid`(`amount`, `date`, `account_idaccount`, `auction_idauction`) 
			VALUES ('".$_POST['bid']."', '".$date."', '".$idMember."', '".$auctionId."' )";
			
			// send an email to the last bidder to notify him
			$sql_lastBid = "SELECT bid.amount, bid.date, bid.idbid, account.name FROM `auction` 
			JOIN `bid` ON auction.idauction = bid.auction_idauction 
			JOIN `account` ON bid.account_idaccount = account.idaccount
			WHERE auction.product_idproduct ='".$idproduct."'
			ORDER BY idbid DESC LIMIT 1";
			$r_lastBid = $conn->query($sql_lastBid);
			
			if($conn->query($sql_insert)=== TRUE)
			{
				if($r_lastBid->num_rows == 1)
				{
					$row_lastBid = $r_lastBid->fetch_assoc();
					
					$sql_lastBidInfo = "SELECT `email`, `name` FROM `account` WHERE `name` = '".$row_lastBid['name']."' ";
					$r_lastBidInfo = $conn->query($sql_lastBidInfo);
					$row_lastBidInfo = $r_lastBidInfo->fetch_assoc();
					
					$to = $row_lastBidInfo['email'];
					$subject = "Oferta riba ".$title."";
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
										<p>Hello " . $row_lastBidInfo['name'] . ",<br>
										<br>
										Unfortunately another bidder has bid higher then you on a product that you bid on.<br><br>
										
										Click on the following link to go to the product:<br>
										http://topsecret.brutalcoding.com/index.php?id=7&product=".$idproduct."
										</p>
									</td>
								</tr>
							</table>
						</body>
					</html>
					";
					$headers = "MIME-Version: 1.0" . "\r\n";
					$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
					$headers .= 'From: Bo Mercado <message-noreply@bomercado.com>' . "\r\n";

					mail($to, $subject, $message, $headers);
				}
				
				// send an email to seller to notify their was a bid
				$sql_seller = "SELECT `name`, `email` FROM `account` WHERE `idaccount`= '".$sellerid."'";
				$r_seller = $conn->query($sql_seller);

				$row_seller = $r_seller->fetch_assoc();
				$sellerName = $row_seller['name'];
				$sellerEmail = $row_seller['email'];
				
				$sql_user = "SELECT `name`, `email` FROM `account` WHERE `idaccount`= '".$idMember."'";
				$r_user = $conn->query($sql_user);

				$row_user = $r_user->fetch_assoc();
				$userName = $row_user['name'];
				$userEmail = $row_user['email'];
				$price = priceConverter($_POST['bid']);
				
				$sql_cur = "SELECT country.currency FROM `product`
				JOIN `country` ON product.country_idcountry = country.idcountry
				WHERE product.idproduct = '".$idproduct."'";
				$r_cur = $conn->query($sql_cur);
				
				$r_cur = $r_cur->fetch_assoc();
				$currency = $r_cur['currency'];
				
				$to = $sellerEmail;
				$subject = "Oferta riba ".$title."";
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
									<p>Hello " . $sellerName . ",<br>
									<br>
									E personsa ".$userName." a kaba di pone un oferta riba bo producto.<br><br>
									
									Producto : ".$title."<br>
									E oferta ta ".$currency." ".$price."<br><br>
									
									link di e producto:<br>
									http://topsecret.brutalcoding.com/index.php?id=7&product=".$idproduct."
								
									<br><br>
									Information di comprado:<br>
									Nomber: ".$userName."<br>
									E-mail: ".$userEmail."
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
				$headers .= 'From: Bo Mercado <message-noreply@bomercado.com>' . "\r\n";

				mail($to, $subject, $message, $headers);
				
			}
			else
			{
				$content->assign('ERROR-MSG', "Algo a bai fout porfabor purba atrobe.");
			}
		}
	}
	else
	{
		$content->assign("style", 'style="border-color: red;"');
	}
}


$sql_bid = "SELECT bid.amount, bid.date, bid.idbid, account.name FROM `auction` 
JOIN `bid` ON auction.idauction = bid.auction_idauction 
JOIN `account` ON bid.account_idaccount = account.idaccount
WHERE auction.product_idproduct ='".$idproduct."'
ORDER BY idbid DESC LIMIT 5";
$r_bid = $conn->query($sql_bid);

$count = 0;
if($r_bid->num_rows > 0)
{
	$content->newBlock('AUCTION_BID_BOX');
	while($row_bid = $r_bid->fetch_assoc())
	{
		
		$content->newBlock('AUCTION_BID');
		$content->assign(array ( name => $row_bid['name'],
								 date_bid => date("d M 'y", strtotime($row_bid['date']))));
		
		
		$content->assign("amount", priceConverter($row_bid['amount']));
		
		//$content->assign("amount", "hallo");
								 
		$sql_cur = "SELECT country.currency FROM `product`
		JOIN `country` ON product.country_idcountry = country.idcountry
		WHERE product.idproduct = '".$idproduct."'";
		$r_cur = $conn->query($sql_cur);
		
		$r_cur = $r_cur->fetch_assoc();
		$currency = $r_cur['currency'];
		$content->assign("currency", $currency );
		
		if( $count > 0)
		{
			$content->assign('line', 'class="auction_li"');
		}
		
		$count += 1;
	}	
}	 



?>