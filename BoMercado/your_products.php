<?php
include_once("includes/class.TemplatePower.inc.php");
include_once("function/priceConverter.php");

$content= new TemplatePower("./html/your_products.htm");
$content->prepare();

$idaccount = $_SESSION['idaccount'];


if(!empty($idaccount))
{
	$sql_prod = "SELECT * FROM `product` 
	INNER JOIN `country` 
	ON product.country_idcountry = country.idcountry 
	WHERE `account_idaccount`= '".$idaccount."'";
	$r_prod = $conn->query($sql_prod);
	
	if(isset($_POST['delete']))
	{
		$sql_check="SELECT * FROM `product` WHERE `idproduct`='".$_POST['delete']."'
		AND `account_idaccount`='".$_SESSION['idaccount']."'";
		$r_check = $conn->query($sql_check);
		
		if($r_check->num_rows > 0)
		{
			$sql_delete = "DELETE FROM `product` WHERE `idproduct`='".$_POST['delete']."'";
			
			$sql_select = "SElECT `url` FROM `imageupload` 
			WHERE `product_idproduct`='".$_POST['delete']."'";
			
			$r_select = $conn->query($sql_select);
			if($r_select->num_rows > 0)
			{
				while($row_r = $r_select->fetch_assoc())
				{
					unlink($row_r['url']);
				}
			}
			
			$conn->query($sql_delete);
			header("Refresh:0");
		}
		else
		{
			header("Location: error-404");
		}
	}
	
	if($r_prod->num_rows > 0)
	{
		$currency = "";
		while($row_prod = $r_prod->fetch_assoc())
		{
			$currency = $row_prod['currency'];
			$content->newBlock("AD_BOX");
			$newDate = date("d-m-Y", strtotime($row_prod['date']));
			$content->assign(array (date_p => $newDate,
									title => $row_prod['title'],
									currency => $row_prod['currency'],
									id_prod => $row_prod['idproduct'] ));
									
			$content->assign("price", priceConverter($row_prod['price']));
									
			$content->assign("link", "index.php?id=7&product=".$row_prod['idproduct']."");
			//views
			$sql_view = "SELECT idview FROM `view` WHERE `product_idproduct`= '".$row_prod['idproduct']."'";
			$r_view = $conn->query($sql_view);
			$rowcount = $r_view->num_rows;
			$content->assign("count", $rowcount);
			
			
			if($row_prod['auction'] == 1)
			{
				$content->newBlock("OFFER");
				$content->assign("currency2", $currency);
				//last offer
				$sql_offer = "SELECT amount FROM `auction` INNER JOIN `bid` 
				ON auction.idauction = bid.auction_idauction
				WHERE auction.product_idproduct = '".$row_prod['idproduct']."'
				ORDER BY idbid DESC LIMIT 1";
				$r_offer = $conn->query($sql_offer);
				$row_offer = $r_offer->fetch_assoc();
				
				$content->assign("price2", priceConverter($row_offer['amount']));
				
			}
		}
	}
	else
	{
		$content->newBlock("EMPTY_BOX");
	}
	

}
else
{
	header("Location: Log-in");
}

?>