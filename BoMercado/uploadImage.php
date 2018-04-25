 <?php

function loadImage($name, $filename)
{
	$target_dir = "userImages/".$name."/";


	if(!file_exists($target_dir)){
		mkdir($target_dir, 0777, true);
	}

	$imageFileType= array();
	$file = $filename;
	$fileLenght = count($file);
	$target_file = array();
	$tmp_file= $_FILES["file"]["tmp_name"];
	$error = 0;

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
					// it's not an image
					$error = 1;
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
			$error = 2;
			$uploadOk = 0;
		}
	} 

	// Check file size
	foreach($_FILES["file"]["size"] as $fileSize)
	{
		if ($fileSize > 10485760) 
		{
			//file size to big
			$error = 3;
			$uploadOk = 0;
		}
	}

	// Allow certain file formats
	foreach($imageFileType as $typeFileImage)
	{
		if($typeFileImage != "jpg" && $typeFileImage != "png" && $typeFileImage != "jpeg"
		&& $typeFileImage != "JPG" && $typeFileImage != "PNG" && $typeFileImage != "JPEG") 
		{
			//file format is not acceptable
			$error = 4;
			$uploadOk = 0;
		}
	}

	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk != 0) 
	{
		// if everything is ok, try to upload file
		for($i = 0 ; $i < $fileLenght ; $i++)
		{
			if(!move_uploaded_file($tmp_file[$i], $target_file[$i]))
			{
				//there was an error uploading your file.
				$error = 5;
			}
		}
	  
	}
	
	return $error;
}
?>