<!-- 
	TODO - renaming for clarity/derived
-->

<!--
	Resize image to a maximum of the provided size
	Parameters:
		$max_dimension, maximum allowable size for largest side of image
		$imgfile, image (jpeg) to be resized

	Author: Michael Williams
	Code for resizing images derived from:
    http://docs.oracle.com/cd/B28359_01/appdev.111/b28845/ch7.htm
			
-->

<?php 
	function resize($max_dimension, $imgfile){
		  $src_img = imagecreatefromjpeg($imgfile);
		  list($w, $h) = getimagesize($imgfile);
		  if ($w > $max_dimension || $h > $max_dimension)
		  {
		  	$scale =  $max_dimension / (($h > $w) ? $h : $w);
		  	$nw = $w * $scale;
		  	$nh = $h * $scale;
		  
		  	$dest_img = imagecreatetruecolor($nw, $nh);
		  	imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $nw, $nh, $w, $h);
		  
		  	imagejpeg($dest_img, $imgfile);  // overwrite with new resized image
		  
		  	imagedestroy($src_img);
		  	imagedestroy($dest_img);
		  }
		  return $imgfile;
		  
	}
?>