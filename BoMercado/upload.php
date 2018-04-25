<?php

$target_dir = "userImages/TESTER/";


if(!file_exists($target_dir)){
	mkdir($target_dir, 0777, true);
}

$imageFileType= array();
$file = $_FILES["file"]["name"];
$fileLenght = count($file);
$target_file = array();
$tmp_file= $_FILES["file"]["tmp_name"];

$nr = 0;
foreach($file as $filename)
{
	if(!empty($filename))
	{
		$target_file[] = $target_dir . basename($filename);
		$uploadOk = 1;
		$imageFileType[] = pathinfo($target_file[$nr],PATHINFO_EXTENSION);
		++$nr;
	}	
}
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) 
{
	
	foreach($tmp_file as $single_file)
	{
		if(!empty($single_file))
		{
			$check = getimagesize($single_file);
			if($check !== false)
			{
				$uploadOk = 1;
			}
			else 
			{
				echo"1";
				$uploadOk = 0;
			}
		}
	}
}

// Check if file already exists
foreach($target_file as $x)
{
	if(file_exists($x))
	{
		echo"2";
		$uploadOk = 0;
	}
} 

// Check file size
foreach($_FILES["file"]["size"] as $fileSize)
{
	if ($fileSize > 5242880) 
	{
		echo"3";
		$uploadOk = 0;
	}
}

// Allow certain file formats
foreach($imageFileType as $typeFileImage)
{
	if($typeFileImage != "jpg" && $typeFileImage != "png" && $typeFileImage != "jpeg"
	&& $typeFileImage != "JPG" && $typeFileImage != "PNG" && $typeFileImage != "JPEG") 
	{
		echo"4";
		$uploadOk = 0;
	}
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) 
{
	echo "Sorry, your images was not uploaded";
// if everything is ok, try to upload file
} 
else 
{
	
	for($i = 0 ; $i < $fileLenght ; $i++)
	{
		if(!move_uploaded_file($tmp_file[$i], $target_file[$i]))
		{
			echo "Sorry, there was an error uploading your file.";
		}
	}
  
}

?>