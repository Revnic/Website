<?PHP
include_once("includes/class.TemplatePower.inc.php");
include("db/connect.php");
session_start();

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
				goto reload;
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

reload:
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