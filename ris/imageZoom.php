<?php

/* 
 * Zoom facility allowing user to see regular sized image of thumbnail clicked
 * on in the Search Module in a new window. When regular sized image is clicked,
 * full sized image will appear.
 * 
 * Author: Costa
 */
include("sessionCheck.php");

if (sessionCheck()) {
    // Check if thumbnail was clicked
    if (isset($_POST['thumbnail_x'])) {
        $regular = $_POST['regular'];
        $full = $_POST['full'];
        
        echo '<form method="POST" action="imageZoom.php">';
        echo '<input type="image" src="data:image/jpeg;base64,'.$regular.'" name="regular" />';
        echo '<input type="hidden" name="full" value="'.$full.'" />';
        echo '</form>';
    }
    // Check if regular sized image was clicked
    else if (isset($_POST['regular_x'])) {
        $full = $_POST['full'];
        echo '<img src="data:image/jpeg;base64,'.$full.'"/>';
    }
    
    else {
        echo "Error retrieving image.";
    }
}

?>