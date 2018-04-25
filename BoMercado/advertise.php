<?php
include_once("includes/class.TemplatePower.inc.php");
include('checkImage.php');
include_once("function/resize.php");

$content= new TemplatePower("./html/advertise.htm");
$content->prepare();

$idaccount = $_SESSION['idaccount'];
$name = $_SESSION['name'];
$upload = 1;

// category
$sql_category = "SELECT * FROM `category` ORDER BY `name`";
$result_category = $conn->query($sql_category);
while($row = $result_category->fetch_assoc())
{
	$content->newBlock('CATEGORY');
	$content->assign( array(title => $row['name'],
							id => $row['idcategory']));
}

//Get country
$sql_country = "SELECT * FROM `country` ORDER BY `name`";
$result_country = $conn->query($sql_country);

while($row2 = $result_country->fetch_assoc())
{
	$content->newBlock('COUNTRY');
	$content->assign( array(country => $row2['name'],
							id_c => $row2['idcountry']));
}

//Phone
$sql_phone = "SELECT `phonenr` FROM `account` WHERE `idaccount`='".$idaccount."'";
$result_phone = $conn->query($sql_phone);

if($result_phone->num_rows > 0)
{
	$row3 = $result_phone->fetch_assoc();
	$content->assign("_ROOT.phone", $row3['phonenr']);
}

$upload = 1;
$emptyName = 0;
$emptyCategory = 0;
$emptyCondition = 0;
$emptyDescription = 0;
$emptyTransfer = 0;
$emptyCountry = 0;
$emptyPrice = 0;
$notImage = 0;
$fileExist = 0;
$size = 0;
$formatFile = 0;


if(!empty($idaccount))
{
	
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		if(empty($_POST['titulo']))
		{
			$upload = 0;
			$emptyName = 1;
		}
		
		if(empty($_POST['category']) || $_POST['category'] == "")
		{
			$upload = 0;
			$emptyCategory = 1;
		} 
		
		if(empty($_POST['condition']))
		{
			$upload = 0;
			$emptyCondition = 1;
		}
		
		if(empty($_POST['description']))
		{
			$upload = 0;
			$emptyDescription = 1;
		}
		
		if(empty($_POST['transfer']))
		{
			$upload = 0;
			$emptyTransfer = 1;
		}
		
		if(empty($_POST['country']))
		{
			$upload = 0;
			$emptyCountry = 1;
		}
		
		if(empty($_POST['radio']))
		{
			$upload = 0;
			$emptyPrice = 1;
		}
		
		if($_POST['radio'] == "exacto" && empty($_POST['ex_prijs_nr']))
		{
			$upload = 0;
			$emptyPriceEx = 1;
		}
		
		if($_POST['radio'] == "findishi_prijs" && empty($_POST['offer_prijs_nr']))
		{
			$upload = 0;
			$emptyPriceEx = 1;
		}
		
		$date = date("dmY_His");
		
		$target_dir = "userImages/".$name."/".$date."/";

		if(!file_exists($target_dir)){
			mkdir($target_dir, 0755, true);
		}

		$imageFileType= array();
		$file = $_FILES["file"]["name"];
		$fileLenght = count($file);
		$target_file = array();
		$tmp_file= $_FILES["file"]["tmp_name"];
		$file_size = $_FILES["file"]["size"];
		$error = 0;

		$nr = 0;
		foreach($file as $filename)
		{
			if(!empty($filename))
			{
				$target_file[] = $target_dir . basename($filename);
				$upload = 1;
				$imageFileType[] = pathinfo($target_file[$nr],PATHINFO_EXTENSION);
				++$nr;
			}	
		}
		
		// Check if image file is a actual image or fake image
		$error = checkImage($tmp_file);
		if($error == 1)
		{
			$upload = 0;
		}
		

		// Check if file already exist
		$fileExist = imageExist($target_file);
		if($fileExist == 1)
		{
			$upload = 0;
		}

		// Check file size
		$size = imageSize($file_size);
		if($size == 1)
		{
			$upload = 0;
		}
		
		
		// Allow certain file formats
		$formatFile = fileFormat($imageFileType);
		if($formatFile == 1)
		{
			$upload = 0;
		}
		
		if($upload == 0)
		{
			$content->assign("_ROOT.titulo", $_POST['titulo']);
			$content->assign("_ROOT.description", $_POST['description']);
			
			$content->newBlock('warning');
			
			if($emptyName == 1)
			{
				$content->newBlock('empty_name');
			}
			
			if($emptyCategory == 1)
			{
				$content->newBlock('empty_cate');
			}
			
			if($emptyCondition == 1)
			{
				$content->newBlock('empty_condition');
			}
			
			if($emptyDescription == 1)
			{
				$content->newBlock('empty_description');
			}
			
			if($emptyTransfer == 1)
			{
				$content->newBlock('empty_transfer');
			}
			
			if($emptyCountry  == 1)
			{
				$content->newBlock('empty_country');
			}
			
			if($emptyPrice  == 1)
			{
				$content->newBlock('empty_price');
			}
			
			if($emptyPriceEx  == 1)
			{
				$content->newBlock('empty_price_ex');
			}
			
			if($notImage == 1)
			{
				$content->newBlock('not_image');
			}
			
			if($fileExist == 1)
			{
				$content->newBlock('image_exist');
			}
			
			if($size == 1)
			{
				$content->newBlock('image_size');
			}
			
			if($formatFile == 1)
			{
				$content->newBlock('image_format');
			}
			
			
		}
		else
		{
			$date = date("Y-m-d");
			
			$amount = 0;	
			if($_POST['radio'] == "exacto")
			{
				$amount = $_POST['ex_prijs_nr'];
			}
			
			if($_POST['radio'] == "findishi_prijs")
			{
				$amount = $_POST['offer_prijs_nr'];
			}
			
			$free = False;
			if($_POST['radio'] == "gratis")
			{
				$free = True;
			}
			
			$auction = 0;
			if($_POST['radio'] == "findishi" || $_POST['radio'] == "findishi_prijs" )
			{
				$auction = 1;
			}
			
			$emailcheck = 0;
			if($_POST['emailcheck'] == true)
			{
				$emailcheck = 1;
			}
			
			$callcheck = 0;
			if($_POST['callcheck'] == true)
			{
				$callcheck = 1;
			}
			
			
			$sql_product = "INSERT INTO `product`(`title`, `condition`, `description`, `price`, `date`, 
			`free`,`auction`, `delivery`, `show_mail`, `show_phone`, `category_idcategory`, `country_idcountry`, 
			`account_idaccount`) VALUES ('".$_POST['titulo']."','".$_POST['condition']."','".$_POST['description']."',
			'".$amount."','".$date."','".$free."','".$auction."','".$_POST['transfer']."','".$emailcheck."',
			'".$callcheck."','".$_POST['category']."','".$_POST['country']."','".$idaccount."')";
			
			if($conn->query($sql_product) === true)
			{
				//get last id
				$product_id = $conn->insert_id;
				
				//check phone
				if(!empty($_POST['phone']))
				{
					$sql_phone= "SELECT  `phonenr` FROM `account` WHERE `idaccount` = '".$idaccount."'";
					$result = $conn->query($sql_phone);
					$row = $result->fetch_assoc();
					
					if($row['phonenr'] != $_POST['phone'])
					{
						$sql_phone_updat = "UPDATE `account` SET `phonenr`='".$_POST['phone']."' WHERE `idaccount`='".$idaccount."'";
						$conn->query($sql_phone_updat);
					}
				
				}
				
				// check for auction
				if($auction == 1)
				{
					$sql_auction = "INSERT INTO `auction`(`product_idproduct`) VALUES ('".$product_id."')";
					$conn->query($sql_auction);
				}
				
				for($i = 0 ; $i < $fileLenght ; $i++)
				{
					
					if(move_uploaded_file($tmp_file[$i], $target_file[$i]))
					{
						
						//insert url in data base
						$sql_image = "INSERT INTO `imageupload`(`url`, `product_idproduct`) 
						VALUES ('".$target_file[$i]."','".$product_id."')";
						
						$conn->query($sql_image);
						
					}
					else
					{
						//there was an error uploading image.
						foreach($file as $filename)
						{
							if(!empty($filename))
							{
								echo '<script type="text/javascript">alert("There was an error uploading your image: '.basename($target_file[$i]).' ");</script>';
							}	
						}
						
					}
				}
				
				// redirect page with timer
				$content->newBlock("DONE");
				header("refresh:3; url=Home");
			}
			else
			{
				//echo "Error: " . $sql_product . "<br>" . $conn->error;
			}
			
			$conn->close();
		}
	}  

}
else
{
	header("Location: Log-in");
}


?>