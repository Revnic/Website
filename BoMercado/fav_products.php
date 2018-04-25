<?php
include_once("includes/class.TemplatePower.inc.php");
include_once("function/priceConverter.php");

$content= new TemplatePower("./html/fav_products.htm");
$content->prepare();

$idaccount = $_SESSION['idaccount'];

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

if(!empty($idaccount))
{
	$sql_select = "SELECT * FROM `favourite` 
	WHERE `account_idaccount`='".$idaccount."'
	ORDER BY `idfavourite` DESC";
	$result = $conn->query($sql_select);
	
	if($result->num_rows >0)
	{
		while($row = $result->fetch_assoc())
		{
			$content->newBlock("FAV_BOX");
			
			$sql_prod = "SELECT * FROM `product` INNER JOIN `imageflag` 
			INNER JOIN `country` ON country.idcountry = imageflag.country_idcountry
			AND product.country_idcountry = country.idcountry 
			WHERE product.idproduct ='".$row['product_idproduct']."'";
			$r_prod = $conn->query($sql_prod);
			
			$rowProd = $r_prod->fetch_assoc();
			$content->assign(array ( id => $rowProd['idproduct'],
									title => $rowProd['title'],
									currency => $rowProd['currency'],
									url_flag => $rowProd['url'],
									flag_title => basename($rowProd['name'])));
									
			$content->assign("price", priceConverter($rowProd['price']));
									
			$sql_image = "SELECT * FROM imageupload 
			WHERE product_idproduct = '".$rowProd['idproduct']."'";
			$r_image = $conn->query($sql_image);
			
			$rowImage = $r_image->fetch_assoc();
			if($r_image->num_rows >0)
			{
				$content->assign(array(image_title => basename($rowImage['url']),
										url_image => $rowImage['url']));
			}
			else
			{
				$content->assign("url_image", "image/no_image.png");
			}
			
		}
	}
	else
	{
		
	}
}
else
{
	header("Location: Log-in");
}
?>