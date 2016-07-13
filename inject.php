#!/usr/bin/env php
<?php

$content = gzcompress(stream_get_contents(STDIN), 9) . '@endOfJailbird; ';
$data = '';

for($i=0; $i<strlen($content); $i++)
{
    $data .= sprintf( "%08d", decbin(ord($content[$i])));
}

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

// we need to keep track of what data we have to write next so
// lets set a data index variable
$dataIndex = 0;

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
            // check if there is still data available
            if (!isset($data[$dataIndex]))
            {
                break 2;    
            }

            $color = $rgb[$ic];
            $bit = $data[$dataIndex];

            // what is the current pixel
            $negative = ($color % 2 == 0);  

            // should it be positive
            if ($bit == '1')
            {
                // should be positive but is negative
                if ($negative)
                {
                    if ($color < 255) {
                        $color++;
                    } else {
                        $color--;
                    }
                }
            }
            // should be negative
            else
            {
                // should be negative but is positive
                if (!$negative)
                {
                    if ($color < 255) {
                        $color++;
                    } else {
                        $color--;
                    }
                }
            }
            
            // set the new color
            $rgb[$ic] = $color;

            // update the index
            $dataIndex++;   
        }

        imagesetpixel($image, $ix, $iy, imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]));
    }
}

imagepng($image, dirname($imagePath) . '/jailbirded_' . basename($imagePath), 0);