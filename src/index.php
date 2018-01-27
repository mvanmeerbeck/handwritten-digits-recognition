<?php

$filename = $argv[1];
$numberOfImages = $argv[2];
$handle = fopen($filename, 'rb');
$int = 4;

$header = unpack('N4', fread($handle, $int * 4));

$numberOfRows = $header[3];
$numberOfColumns = $header[3];

$digits = [];
for ($i = 1; $i <= $numberOfImages; $i++) {
    $digit = [];
    $gdImage = imagecreate($numberOfColumns, $numberOfRows);

    for ($row = 0; $row < $numberOfRows; $row++) {
        for ($column = 0; $column < $numberOfColumns; $column++) {
            $pixel = unpack('C', fread($handle, 1));

            $image[$row][$column] = $pixel[1];

            $pixelColor = imagecolorexact($gdImage, $pixel[1], $pixel[1], $pixel[1]);

            if (-1 === $pixelColor) {
                $pixelColor = imagecolorallocate($gdImage, $pixel[1], $pixel[1], $pixel[1]);
            }

            imagesetpixel($gdImage, $column, $row, $pixelColor);

            $digit[] = $pixel[1] + (mt_rand() / mt_getrandmax());
        }
    }

    $digits[] = $digit;
    imagejpeg($gdImage, "digit$i.jpg");
    imagedestroy($gdImage);
}

fclose($handle);

$handle = fopen($argv[3], 'w');
$header = [];

for ($i = 0; $i < $numberOfRows * $numberOfColumns; $i++) {
    $header[] = $i;
}

fputcsv($handle, $header);

foreach ($digits as $digit) {
    fputcsv($handle, $digit);
}

fclose($handle);