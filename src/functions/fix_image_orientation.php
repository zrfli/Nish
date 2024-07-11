<?php
if ($_SERVER['REQUEST_URI'] == '/src/functions/fix_image_orientation.php') { header("Location: /"); exit(); }

function fixImageOrientation($filename) {
    if (function_exists('exif_read_data')) {
        $exif = @exif_read_data($filename);
        
        if ($exif && isset($exif['Orientation'])) {
            $orientation = $exif['Orientation'];
            $image = imagecreatefromjpeg($filename);
            
            if ($image !== false) {
                $image = match ($orientation) {
                    3 => imagerotate($image, 180, 0),
                    6 => imagerotate($image, -90, 0),
                    8 => imagerotate($image, 90, 0),
                    default => $image,
                };

                imagejpeg($image, $filename); 
                imagedestroy($image);
            }
        }
    }
}