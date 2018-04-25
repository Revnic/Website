<?php
include_once("includes/class.TemplatePower.inc.php");

$content= new TemplatePower("./html/content.htm");
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


if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	// search page
	$content->newBlock('SEARCH');
	$content->newBlock('CONTENT_SEARCH');
	

	
}
else
{

	if(!empty($_GET['category']))
	{
		$category = $_GET['category'];
		switch($category)
		{
			case recent:
				$sql_recent = "SELECT * FROM `product` ORDER BY `idproduct` DESC";
				$result = $conn->query($sql_recent);
				if ($result->num_rows > 0) 
				{
					while($row = $result->fetch_assoc())
					{
						$content->newBlock("RECENT_ALL");
						$content->assign(array( titulo_producto => $row['title'],
												price => $row['price'],
												idProduct => $row['idproduct'],
												description => $row['description'],
												datum => date("d-m-Y", strtotime($row['date']))));
						
						
						$sql_image = "SELECT `url` FROM `imageupload` WHERE `product_idproduct`= '".$row['idproduct']."'";
						$result_image = $conn->query($sql_image);
						$image = $result_image->fetch_assoc();
						$content->assign("prod_image", $image['url']);
						
						$sql_flag = "SELECT country.currency, country.name, imageflag.url FROM country 
						INNER JOIN imageflag ON country.idcountry = imageflag.country_idcountry 
						WHERE country.idcountry ='".$row['country_idcountry']."'";
						$result_flag = $conn->query($sql_flag);
						
						$flag = $result_flag->fetch_assoc();
						$content->assign( array( flag => $flag['url'],
												 currency => $flag['currency'],
												 name_country => $flag['name']));
					}
				}
				break;
			case auction:
				$sql_recent = "SELECT * FROM `product` WHERE `auction`= '1' ORDER BY `idproduct`  DESC";
				$result = $conn->query($sql_recent);
				if ($result->num_rows > 0) 
				{
					while($row = $result->fetch_assoc())
					{
						$content->newBlock("AUCTION_ALL");
						$content->assign(array( titulo_producto => $row['title'],
												price => $row['price'],
												idProduct => $row['idproduct'],
												description => $row['description'],
												datum => date("d-m-Y", strtotime($row['date']))));
						
						
						$sql_image = "SELECT `url` FROM `imageupload` WHERE `product_idproduct`= '".$row['idproduct']."'";
						$result_image = $conn->query($sql_image);
						$image = $result_image->fetch_assoc();
						$content->assign("prod_image", $image['url']);
						
						$sql_flag = "SELECT country.currency, country.name, imageflag.url FROM country 
						INNER JOIN imageflag ON country.idcountry = imageflag.country_idcountry 
						WHERE country.idcountry ='".$row['country_idcountry']."'";
						$result_flag = $conn->query($sql_flag);
						
						$flag = $result_flag->fetch_assoc();
						$content->assign( array( flag => $flag['url'],
												 currency => $flag['currency'],
												 name_country => $flag['name']));
					}
				}
				break;
			case popular:
				
				break;
			default:
				//category page
				$content->newBlock('SEARCH');
				$content->newBlock('CONTENT_CATEGORY');
		}
		
	}
	else
	{
		// home page
		$content->newBlock('CONTENT');
		
		// recent
		$sql_recent = "SELECT * FROM `product` ORDER BY `idproduct` DESC LIMIT 4";
		$result = $conn->query($sql_recent);
		if ($result->num_rows > 0) 
		{
			
			while($row = $result->fetch_assoc())
			{
				$content->newBlock("RECENT");
				$content->assign(array( titulo_producto => $row['title'],
										price => $row['price'],
										idProduct => $row['idproduct']));
				
				
				$sql_image = "SELECT `url` FROM `imageupload` WHERE `product_idproduct`= '".$row['idproduct']."'";
				$result_image = $conn->query($sql_image);
				$image = $result_image->fetch_assoc();
				$content->assign("prod_image", $image['url']);
				
				$sql_flag = "SELECT country.currency, country.name, imageflag.url FROM country 
				INNER JOIN imageflag ON country.idcountry = imageflag.country_idcountry 
				WHERE country.idcountry ='".$row['country_idcountry']."'";
				$result_flag = $conn->query($sql_flag);
				
				$flag = $result_flag->fetch_assoc();
				$content->assign( array( flag => $flag['url'],
										 currency => $flag['currency'],
										 name_country => $flag['name']));
			}
		}
		
		//auction
		$sql_action = "SELECT * FROM `product` WHERE `auction`= '1' ORDER BY `idproduct`  DESC LIMIT 4";
		$result_action = $conn->query($sql_action);
		if ($result_action->num_rows > 0) 
		{
			$content->newBlock("AUCTION_TITLE");
			while($row_auction = $result_action->fetch_assoc())
			{
				$content->newBlock("AUCTION");
				$content->assign(array( titulo_producto_a => $row_auction['title'],
										price_a => $row_auction['price'],
										idProduct_a => $row_auction['idproduct']));
				
				
				$sql_image = "SELECT `url` FROM `imageupload` WHERE `product_idproduct`= '".$row_auction['idproduct']."'";
				$result_image_a = $conn->query($sql_image);
				$image_action = $result_image_a->fetch_assoc();
				$content->assign("prod_image_a", $image_action['url']);
				
				$sql_flag = "SELECT country.currency, country.name, imageflag.url FROM country 
				INNER JOIN imageflag ON country.idcountry = imageflag.country_idcountry 
				WHERE country.idcountry ='".$row_auction['country_idcountry']."'";
				$result_flag = $conn->query($sql_flag);
				
				$flag = $result_flag->fetch_assoc();
				$content->assign( array( flag => $flag['url'],
										 currency => $flag['currency'],
										 name_country => $flag['name']));
			}
		}
		
		//popular
		
		
	}
}





?>