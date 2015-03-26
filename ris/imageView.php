<!-- 
	Selects and renders an image from passed recordID and associated imageID
	Variables passed by URL: 
		$recordID, primary key ID
		$imageID, unique image identifier for associated recordID
		$imageType, image size option (THUMBNAIL, REGULAR_SIZE, FULL_SIZE)
	
		Author: Michael Williams
 -->

<?php

	include("PHPconnectionDB.php");
	$conn = connect();
	$imageID = $_GET['imageID'];
	$recordID = $_GET['recordID'];
	$imageType = $_GET['imageType'];
	
	$query = "SELECT ".$imageType." FROM PACS_IMAGES WHERE RECORD_ID = :recordID AND IMAGE_ID = :imageID";
	$stmt = oci_parse ($conn, $query);
	//oci_bind_by_name($stmt, ':imageType', $imageType);
	oci_bind_by_name($stmt, ':recordID', $recordID);
	oci_bind_by_name($stmt, ':imageID', $imageID);	
	oci_execute($stmt);
	$arr = oci_fetch_array($stmt, OCI_ASSOC);
	$result = $arr[$imageType]->load();
	header("Content-type: image/JPEG");
	echo $result;
	
	oci_close($conn);

?>