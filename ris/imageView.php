<?php
/* Loads from the database and renders the selected jpeg image (recordID and imageID)
 * Parameters:
 * $recordID, required radiology record
 * $imageID, chosen image associated with radiology record
 * $imageType, image size option (THUMBNAIL, REGULAR_SIZE, FULL_SIZE)
 * 
 * Note: any white space above the intial <?php will break the content-type decoding
 *
 *  Author: Michael Williams
 */
	include("PHPconnectionDB.php");
	$conn = connect();
	$imageID = $_GET['imageID'];
	$recordID = $_GET['recordID'];
	$imageType = $_GET['imageType'];
	
	$query = "SELECT ".$imageType." FROM PACS_IMAGES WHERE RECORD_ID = :recordID AND IMAGE_ID = :imageID";
	$stid = oci_parse ($conn, $query);
	oci_bind_by_name($stid, ':recordID', $recordID);
	oci_bind_by_name($stid, ':imageID', $imageID);
	
	oci_execute($stid);
	//extract image blob from the selection array and display as jpg
	//echo src="data:image/jpeg;base64,'.$regular.'"$result;
	$arr = oci_fetch_array($stid, OCI_ASSOC);
	$result = base64_encode($arr[$imageType]->load());
	echo '<img src="data:image/jpeg;base64,'.$result.'" />';
	//echo '<img src="data:image/jpeg;base64,'.$full.'"/>';
	//header("Content-type: image/JPEG");
	//echo $result;
	oci_close($conn);
?>
