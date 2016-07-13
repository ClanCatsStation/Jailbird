#!/usr/bin/env php
<?php

$neededBits = (strlen(gzcompress(stream_get_contents(STDIN), 9)) + 16) * 8;

$neededPixels = ceil($neededBits / 3);

$neededSize = ceil(sqrt($neededPixels)); 

echo sprintf("bits: %s pixels: %s min-size: %sx%s \n", $neededBits, $neededPixels, $neededSize, $neededSize);
