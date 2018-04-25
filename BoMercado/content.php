<?php
include_once("includes/class.TemplatePower.inc.php");
include_once("function/priceConverter.php");

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


if($_SERVER['REQUEST_METHOD'] == "POST")
{
	//Sorted results
	$category = $_GET['category'];
	$country = $_GET['country'];
	$value = $_POST['sorted'];
	
	$sql_category="";
	$orderBy ="";
	$desc ="";
	
	switch($value)
	{
		case 1:
			$orderBy ="date";
			$desc ="DESC";
			break;
		case 2:
			$orderBy ="date";
			$desc ="ASC";
			break;
		case 3:
			$orderBy ="price";
			$desc ="DESC";
			break;
		case 4:
			$orderBy ="price";
			$desc ="ASC";
			break;
	}
	
	
	if(!empty($category) && empty($country))
	{
		$sql_category = "SELECT * FROM `product` WHERE 
			`category_idcategory`= ".$category." ORDER BY `".$orderBy."` ".$desc." ";
	}
	else
	{
		if($category != "*" && $country == "*")
		{
			$sql_category = "SELECT * FROM `product` WHERE 
			`category_idcategory`= ".$category." ORDER BY `".$orderBy."` ".$desc." ";
		}
		elseif($category != "*" && $country != "*")
		{
			$sql_category = "SELECT * FROM `product` WHERE 
			`category_idcategory`= ".$category." AND 
			`country_idcountry` = ".$country." ORDER BY `".$orderBy."` ".$desc."";
		}
		elseif($category == "*" && $country != "*")
		{
			$sql_category = "SELECT * FROM `product` WHERE
			`country_idcountry` = ".$country." ORDER BY `".$orderBy."` ".$desc."";
		}
		else
		{
			$sql_category = "SELECT * FROM `product` ORDER BY `".$orderBy."` ".$desc."";
		}
	}
	
	$result = $conn->query($sql_category);

	if($result->num_rows > 0)
	{
		$content->newBlock('SEARCH');
		switch($value)
		{
			case 1;
				$content->assign("SELECT1", "selected");
				break;
			case 2;
				$content->assign("SELECT2", "selected");
				break;
			case 3;
				$content->assign("SELECT3", "selected");
				break;
			case 4;
				$content->assign("SELECT4", "selected");
				break;
		}
		while($row = $result->fetch_assoc())
		{
			$content->newBlock('CONTENT_CATEGORY');
			$content->assign(array( titulo_producto => $row['title'],
									idProduct => $row['idproduct'],
									description => $row['description'],
									datum => date("d-M-Y", strtotime($row['date']))));
			
			$sql_image = "SELECT `url` FROM `imageupload` WHERE `product_idproduct`= '".$row['idproduct']."'";
			$result_image = $conn->query($sql_image);
			if($result_image->num_rows > 0)
			{
				$image = $result_image->fetch_assoc();
				$content->assign(array(prod_image => $image['url'],
										image_title => basename($image['url'])));
			}
			else
			{
				$content->assign("prod_image", "image/no_image.png");
			}
			
			$sql_flag = "SELECT country.currency, country.name, imageflag.url FROM country 
			INNER JOIN imageflag ON country.idcountry = imageflag.country_idcountry 
			WHERE country.idcountry ='".$row['country_idcountry']."'";
			$result_flag = $conn->query($sql_flag);
			
			$flag = $result_flag->fetch_assoc();
			$content->assign( array( flag => $flag['url'],
									 name_country => $flag['name']));
			 
			if($row['price'] != 0)
			{
				$content->assign("price", priceConverter($row['price']));
				$content->assign("currency", $flag['currency']);
			}
			elseif($row['auction'] == 1 && $row['price'] == 0)
			{
				$content->assign("price", "Oferta");
			}
			elseif($row['free'] == TRUE)
			{
				$content->assign("price", "Gratis");
			}
			else
			{
				$content->assign("price", "Puntra pa prijs");
			}		
		}
	}
}
else
{
	if(!empty($_GET['category'])&& empty($_GET['country']))
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
						$content->newBlock("CONTENT_CATEGORY");
						$content->assign(array( titulo_producto => $row['title'],
												idProduct => $row['idproduct'],
												description => $row['description'],
												datum => date("d-M-Y", strtotime($row['date']))));
						
						$sql_image = "SELECT `url` FROM `imageupload` WHERE `product_idproduct`= '".$row['idproduct']."'";
						$result_image = $conn->query($sql_image);
						if($result_image->num_rows > 0)
						{
							$image = $result_image->fetch_assoc();
							$content->assign(array(prod_image => $image['url'],
													image_title => basename($image['url'])));
						}
						else
						{
							$content->assign("prod_image", "image/no_image.png");
						}
						
						$sql_flag = "SELECT country.currency, country.name, imageflag.url FROM country 
						INNER JOIN imageflag ON country.idcountry = imageflag.country_idcountry 
						WHERE country.idcountry ='".$row['country_idcountry']."'";
						$result_flag = $conn->query($sql_flag);
						
						$flag = $result_flag->fetch_assoc();
						$content->assign( array( flag => $flag['url'],
												 name_country => $flag['name']));
												 
						if($row['price'] != 0)
						{
							$content->assign("price", priceConverter($row['price']));
							$content->assign("currency", $flag['currency']);
						}
						elseif($row['auction'] == 1 && $row['price'] == 0)
						{
							$content->assign("price", "Oferta");
						}
						elseif($row['free'] == TRUE)
						{
							$content->assign("price", "Gratis");
						}
						else
						{
							$content->assign("price", "Puntra pa prijs");
						}						 
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
						$content->newBlock("CONTENT_CATEGORY");
						$content->assign(array( titulo_producto => $row['title'],
												idProduct => $row['idproduct'],
												description => $row['description'],
												datum => date("d-M-Y", strtotime($row['date']))));
						
						$sql_image = "SELECT `url` FROM `imageupload` WHERE `product_idproduct`= '".$row['idproduct']."'";
						$result_image = $conn->query($sql_image);
						if($result_image->num_rows > 0)
						{
							$image = $result_image->fetch_assoc();
							$content->assign(array(prod_image => $image['url'],
													image_title => basename($image['url'])));
						}
						else
						{
							$content->assign("prod_image", "image/no_image.png");
						}
						
						$sql_flag = "SELECT country.currency, country.name, imageflag.url FROM country 
						INNER JOIN imageflag ON country.idcountry = imageflag.country_idcountry 
						WHERE country.idcountry ='".$row['country_idcountry']."'";
						$result_flag = $conn->query($sql_flag);
						
						$flag = $result_flag->fetch_assoc();
						$content->assign( array( flag => $flag['url'],
												 name_country => $flag['name']));
												 
						if($row['price'] != 0)
						{
							$content->assign("price_a", priceConverter($$row['price']));
							$content->assign("currency", $flag['currency']);
						}
						elseif($row['auction'] == 1 && $row['price'] == 0)
						{
							$content->assign("price", "Oferta");
						}
						elseif($row['free'] == TRUE)
						{
							$content->assign("price", "Gratis");
						}
						else
						{
							$content->assign("price", "Puntra pa prijs");
						}		
					}
				}
				break;
			case popular:
				$sql_recent = "SELECT `idproduct`, `title`, `price`, `currency`, `name`, `url`, `date`, `description`, `auction`
						FROM `product` 
						INNER JOIN `view` 
						INNER Join `country`
						INNER JOIN `imageflag`
						ON product.idProduct = view.product_idproduct
						AND product.country_idcountry = country.idcountry
						AND country.idcountry = imageflag.country_idcountry
						GROUP BY product_idproduct LIMIT 20";
				$result = $conn->query($sql_recent);
				if ($result->num_rows > 0) 
				{
					while($row = $result->fetch_assoc())
					{
						$content->newBlock("CONTENT_CATEGORY");
						$content->assign(array( titulo_producto => $row['title'],
												idProduct => $row['idproduct'],
												description => $row['description'],
												datum => date("d-M-Y", strtotime($row['date'])),
												flag => $row['url'],
												name_country => $row['name']));
						
						$sql_image = "SELECT `url` FROM `imageupload` WHERE `product_idproduct`= '".$row['idproduct']."'";
						$result_image = $conn->query($sql_image);
						if($result_image->num_rows > 0)
						{
							$image = $result_image->fetch_assoc();
							$content->assign(array(prod_image => $image['url'],
													image_title => basename($image['url'])));
						}
						else
						{
							$content->assign("prod_image", "image/no_image.png");
						}
						
						if($row['price'] != 0)
						{
							$content->assign("price", priceConverter($row['price']));
							$content->assign("currency", $row['currency']);
						}
						elseif($row['auction'] == 1 && $row['price'] == 0)
						{
							$content->assign("price", "Oferta");
						}
						elseif($row['free'] == TRUE)
						{
							$content->assign("price", "Gratis");
						}
						else
						{
							$content->assign("price", "Puntra pa prijs");
						}		
						
					}
				}
				break;
			default:
				//category page
				$sql_category = "SELECT * FROM `product` WHERE `category_idcategory`= '".$category."' ORDER BY `idproduct`  DESC";
				$result = $conn->query($sql_category);
				
				if($result->num_rows > 0)
				{
					$content->newBlock('SEARCH');
					while($row = $result->fetch_assoc())
					{
						$content->newBlock('CONTENT_CATEGORY');
						$content->assign(array( titulo_producto => $row['title'],
												idProduct => $row['idproduct'],
												description => $row['description'],
												datum => date("d-M-Y", strtotime($row['date']))));
						
						$sql_image = "SELECT `url` FROM `imageupload` WHERE `product_idproduct`= '".$row['idproduct']."'";
						$result_image = $conn->query($sql_image);
						if($result_image->num_rows > 0)
						{
							$image = $result_image->fetch_assoc();
							$content->assign(array(prod_image => $image['url'],
													image_title => basename($image['url'])));
						}
						else
						{
							$content->assign("prod_image", "image/no_image.png");
						}
						
						$sql_flag = "SELECT country.currency, country.name, imageflag.url FROM country 
						INNER JOIN imageflag ON country.idcountry = imageflag.country_idcountry 
						WHERE country.idcountry ='".$row['country_idcountry']."'";
						$result_flag = $conn->query($sql_flag);
						
						$flag = $result_flag->fetch_assoc();
						$content->assign( array( flag => $flag['url'],
												 name_country => $flag['name']));
						 
						if($row['price'] != 0)
						{
							$content->assign("price", priceConverter($row['price']));
							$content->assign("currency", $flag['currency']);
						}
						elseif($row['auction'] == 1 && $row['price'] == 0)
						{
							$content->assign("price", "Oferta");
						}
						elseif($row['free'] == TRUE)
						{
							$content->assign("price", "Gratis");
						}
						else
						{
							$content->assign("price", "Puntra pa prijs");
						}		
					}
				}
				else
				{
					// no results found
					$content->newBlock("NORESULTS");
				}
				
		}
		
	}
	elseif(!empty($_GET['category'])&& !empty($_GET['country']))
	{
		$sql_search = "";
		if($_GET['category'] != "*" && $_GET['country'] == "*")
		{
			$sql_search = "SELECT * FROM `product` WHERE 
			`category_idcategory`= '".$_GET['category']."' ORDER BY `idproduct` DESC ";
			
		}
		elseif($_GET['category'] != "*" && $_GET['country'] != "*")
		{
			$sql_search = "SELECT * FROM `product` WHERE 
			`category_idcategory`= '".$_GET['category']."' AND 
			`country_idcountry` = '".$_GET['country']."' ORDER BY `idproduct` DESC";
		}
		elseif($_GET['category'] == "*" && $_GET['country'] != "*")
		{
			$sql_search = "SELECT * FROM `product` WHERE
			`country_idcountry` = '".$_GET['country']."' ORDER BY `idproduct` DESC";
		}
		else
		{
			$sql_search = "SELECT * FROM `product` ORDER BY `idproduct` DESC ";
		}
		
		$result = $conn->query($sql_search);
		
		if($result->num_rows > 0)
		{
			$content->newBlock('SEARCH');
			while($row = $result->fetch_assoc())
			{
				$content->newBlock("CONTENT_CATEGORY");
				$content->assign(array( titulo_producto => $row['title'],
										idProduct => $row['idproduct'],
										description => $row['description'],
										datum => date("d-M-Y", strtotime($row['date']))));
				
				$sql_image = "SELECT `url` FROM `imageupload` WHERE `product_idproduct`= '".$row['idproduct']."'";
				$result_image = $conn->query($sql_image);
				if($result_image->num_rows > 0)
				{
					$image = $result_image->fetch_assoc();
					$content->assign(array(prod_image => $image['url'],
											image_title => basename($image['url'])));
				}
				else
				{
					$content->assign("prod_image", "image/no_image.png");
				}
				
				
				$sql_flag = "SELECT country.currency, country.name, imageflag.url FROM country 
				INNER JOIN imageflag ON country.idcountry = imageflag.country_idcountry 
				WHERE country.idcountry ='".$row['country_idcountry']."'";
				$result_flag = $conn->query($sql_flag);
				
				$flag = $result_flag->fetch_assoc();
				$content->assign( array( flag => $flag['url'],
										 name_country => $flag['name']));
										 
				
				if($row['price'] != 0)
				{
					$content->assign("price", priceConverter($row['price']));
					$content->assign("currency", $flag['currency']);
				}
				elseif($row['auction'] == 1 && $row['price'] == 0)
				{
					$content->assign("price", "Oferta");
				}
				elseif($row['free'] == TRUE)
				{
					$content->assign("price", "Gratis");
				}
				else
				
				{
					$content->assign("price", "Puntra pa prijs");
				}		
			}
		}
		else
		{
			// no results found
			$content->newBlock("NORESULTS");
		}
	
	}
	else
	{
		// home page
		$content->newBlock('CONTENT');
		
		// recent
		$sql_recent = "SELECT * FROM `product` ORDER BY `idproduct` DESC LIMIT 8";
		$result = $conn->query($sql_recent);
		if ($result->num_rows > 0) 
		{
			$content->newBlock("RECENT_TITLE");
			while($row = $result->fetch_assoc())
			{
				$content->newBlock("RECENT");
				$content->assign(array( titulo_producto => $row['title'],
										idProduct => $row['idproduct']));
				
				$sql_image = "SELECT `url` FROM `imageupload` WHERE `product_idproduct`= '".$row['idproduct']."'";
				$result_image = $conn->query($sql_image);
				if($result_image->num_rows > 0)
						{
							$image = $result_image->fetch_assoc();
							$content->assign(array(prod_image => $image['url'],
													image_title => basename($image['url'])));
						}
						else
						{
							$content->assign("prod_image", "image/no_image.png");
						}
				
				$sql_flag = "SELECT country.currency, country.name, imageflag.url FROM country 
				INNER JOIN imageflag ON country.idcountry = imageflag.country_idcountry 
				WHERE country.idcountry ='".$row['country_idcountry']."'";
				$result_flag = $conn->query($sql_flag);
				
				$flag = $result_flag->fetch_assoc();
				$content->assign( array( flag => $flag['url'],
										 name_country => $flag['name']));
										 
										 
				if($row['price'] != 0)
				{
					$content->assign("price", priceConverter($row['price']));
					$content->assign("currency", $flag['currency']);
				}
				elseif($row['auction'] == 1 && $row['price'] == 0)
				{
					$content->assign("price", "Oferta");
				}
				elseif($row['free'] == TRUE)
				{
					$content->assign("price", "Gratis");
				}
				else
				{
					$content->assign("price", "Puntra pa prijs");
				}							 
			}
		}
		
		//auction
		$sql_action = "SELECT * FROM `product` WHERE `auction`= '1' ORDER BY `idproduct`  DESC LIMIT 8";
		$result_action = $conn->query($sql_action);
		if ($result_action->num_rows > 0) 
		{
			$content->newBlock("AUCTION_TITLE");
			$content->newBlock("HR");
			while($row_auction = $result_action->fetch_assoc())
			{
				$content->newBlock("AUCTION");
				$content->assign(array( titulo_producto_a => $row_auction['title'],
										idProduct_a => $row_auction['idproduct']));
				
				$sql_image = "SELECT `url` FROM `imageupload` WHERE `product_idproduct`= '".$row_auction['idproduct']."'";
				$result_image_a = $conn->query($sql_image);
				if($result_image_a->num_rows > 0)
						{
							$image = $result_image_a->fetch_assoc();
							$content->assign(array(prod_image => $image['url'],
													image_title => basename($image['url'])));
						}
						else
						{
							$content->assign("prod_image", "image/no_image.png");
						}
				
				$sql_flag = "SELECT country.currency, country.name, imageflag.url FROM country 
				INNER JOIN imageflag ON country.idcountry = imageflag.country_idcountry 
				WHERE country.idcountry ='".$row_auction['country_idcountry']."'";
				$result_flag = $conn->query($sql_flag);
				
				$flag = $result_flag->fetch_assoc();
				$content->assign( array( flag => $flag['url'],
										 name_country => $flag['name']));
										 
				if($row_auction['price'] != 0)
				{
					$content->assign("price_a", priceConverter($row_auction['price']));
					$content->assign("currency", $flag['currency']);
				}
				elseif($row_auction['auction'] == 1 && $row_auction['price'] == 0)
				{
					$content->assign("price_a", "Oferta");
				}
				else
				{
					$content->assign("price_a", "Puntra pa prijs");
				}							 
			}
		}
		
		//popular
		$sql_recent = "SELECT * FROM `product` 
						INNER JOIN `view` 
						INNER Join `country`
						INNER JOIN `imageflag`
						ON product.idProduct = view.product_idproduct
						AND product.country_idcountry = country.idcountry
						AND country.idcountry = imageflag.country_idcountry
						GROUP BY product_idproduct LIMIT 8";
		$result = $conn->query($sql_recent);
		if ($result->num_rows > 0) 
		{
			$content->newBlock("POPULAR_TITLE");
			while($row = $result->fetch_assoc())
			{
				$content->newBlock("POPULAR");
				$content->assign(array( titulo_producto => $row['title'],
										idProduct => $row['idproduct'],
										flag => $row['url'],
										name_country => $row['name']));
				
				$sql_image = "SELECT `url` FROM `imageupload` WHERE `product_idproduct`= '".$row['idproduct']."'";
				$result_image = $conn->query($sql_image);
				if($result_image->num_rows > 0)
				{
					$image = $result_image->fetch_assoc();
					$content->assign(array(prod_image => $image['url'],
											image_title => basename($image['url'])));
				}
				else
				{
					$content->assign("prod_image", "image/no_image.png");
				}
				
								
				$sql_flag = "SELECT country.currency, country.name, imageflag.url FROM country 
				INNER JOIN imageflag ON country.idcountry = imageflag.country_idcountry 
				WHERE country.idcountry ='".$row['country_idcountry']."'";
				$result_flag = $conn->query($sql_flag);
				
				$flag = $result_flag->fetch_assoc();
				$content->assign( array( flag => $flag['url'],
										 name_country => $flag['name']));
				
				if($row['price'] != 0)
				{
					$content->assign("price", priceConverter($row['price']));
					$content->assign("currency", $flag['currency']);
				}
				elseif($row['auction'] == 1 && $row['price'] == 0)
				{
					$content->assign("price", "Oferta");
				}
				elseif($row['free'] == TRUE)
				{
					$content->assign("price", "Gratis");
				}
				else
				{
					$content->assign("price", "Puntra pa prijs");
				}		
			}
		}
		
		
		
		
	}
}





?>