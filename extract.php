#!/usr/bin/env php
<?php

// we dont need the first argument
array_shift($argv);

// get image by argument
$imagePath = array_shift($argv);

if ((!file_exists($imagePath)) || (!is_readable($imagePath)))
{
	die("The given image does not exist or is not readable.\n");
}

// load the image with GD
$image = imagecreatefrompng($imagePath);
$imageWidth = imagesx($image);
$imageHeight = imagesy($image);

// create an empty string where our data will end up 
$data = '';

// and start iterating y
for ($iy = 0; $iy < $imageHeight; $iy++)
{
	// and x
	for ($ix = 0; $ix < $imageWidth; $ix++)
	{
		$rgb = imagecolorat($image, $ix, $iy);

		// split rgb to an array
		$rgb = [($rgb >> 16) & 0xFF, ($rgb >> 8) & 0xFF, $rgb & 0xFF];

		// and for every color
		for($ic = 0; $ic < 3; $ic++)
		{
			$color = $rgb[$ic];

			// what is the current pixel
			if ($color % 2 == 0)
			{
				$data .= '0';
			}
			else
			{
				$data .= '1';
			}
		}
	}
}

$content = '';

foreach(str_split($data, 8) as $char)
{
	$content .= chr(bindec($char));
}

// does the jailbird end of line exist?
if (strpos($content, '@endOfJailbird;') === false)
{
	die('Image does not contain any jailbird data.');
}

// cut the compressed data out,
// decompress it and print it.
echo gzuncompress(substr($content, 0, strpos($content, '@endOfJailbird;')));

