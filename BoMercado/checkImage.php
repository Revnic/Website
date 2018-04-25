<?php

function checkImage($tmp_file)
{
	foreach($tmp_file as $single_file)
	{
		if(!empty($single_file))
		{
			$check = getimagesize($single_file);
			if($check === false)
			{
				// it's not an image
				$error = 1;
			}
		}
	}
	
	return $error;
}

function imageExist ($target_file)
{
	foreach($target_file as $x)
	{
		if(file_exists($x))
		{
			$fileExist = 1;
		}
	}
		
	return $fileExist;
}


function imageSize($file_size)
{
	foreach($file_size as $fileSize)
	{
		if ($fileSize > 10485760) 
		{
			//file size to big
			$size = 1;
		}
	}
	
	return $size;
}

function fileFormat($imageFileType)
{
	foreach($imageFileType as $typeFileImage)
	{
		if($typeFileImage != "jpg" && $typeFileImage != "png" && $typeFileImage != "jpeg"
		&& $typeFileImage != "JPG" && $typeFileImage != "PNG" && $typeFileImage != "JPEG") 
		{
			//file format is not acceptable
			$formatFile = 1;
		}
	}
	return $formatFile;
}

?>