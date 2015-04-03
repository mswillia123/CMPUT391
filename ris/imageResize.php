<!-- 
	Image (jpeg) resizing function
	Parameters: 
		$source, image blob passed to function
		$max_dimension, determines the maximum resized dimensions
	
		Author: Michael Williams
		
		Based on code from:
		http://stackoverflow.com/questions/12143635/php-image-scaling-does-not-work
		Authored by: Cl'
 -->

<?php

function resize($max_dimension, $source) {

    $source_pic = $source;
	//determine image blob dimensions
    list($width, $height) = getimagesize($source);
	//creates GD image representation from image blob for GD resizing
    $src = imagecreatefromjpeg($source);

    $x_ratio = $max_dimension / $width;
    $y_ratio = $max_dimension / $height;
    
    //determine maximum dimension based upon shortest side of image (ratio of width to height)
    if( ($width <= $max_dimension) && ($height <= $max_dimension) ){
        $tn_width = $width;
        $tn_height = $height;
        }elseif (($x_ratio * $height) < $max_dimension){
            $tn_height = ceil($x_ratio * $height);
            $tn_width = $max_dimension;
        }else{
            $tn_width = ceil($y_ratio * $width);
            $tn_height = $max_dimension;
    }
	
    //create an internal true color representation of image
    $temp = imagecreatetruecolor($tn_width,$tn_height);
	//resize image
    imagecopyresampled($temp,$src,0,0,0,0,$tn_width, $tn_height,$width,$height);
    
    //use object buffer to retrieve resized/resampled image from memory (rather than default, which writes to disk)
    ob_start();
    imagejpeg($temp);
    $final_image = ob_get_clean();

    return $final_image;
}

?>