<?php
function Img_Resize($path) 
{
	$source = '';
	$path_info = pathinfo($path);

	$filename = $path_info['filename'];
	$percent = 0.5;
	$file_extention = $path_info['extension'];

	// Get new sizes
	list($width, $height) = getimagesize($filename);
	$newwidth = $width * $percent;
	$newheight = $height * $percent;

	// Load
	$thumb = imagecreatetruecolor($newwidth, $newheight);
	
	switch($file_extention)
	{
		case jpeg:
			$source = imagecreatefromjpeg($filename);
			break;
		case jpg:
			$source = imagecreatefromjpeg($filename);
			break;
		case png:
			$source = imagecreatefrompng($filename);
			break;
	}
	

	// Resize
	imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

}
?>