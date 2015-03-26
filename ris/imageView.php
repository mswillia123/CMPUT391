<?php

	include("PHPconnectionDB.php");
	$conn = connect();
	$imageID = $_GET['imageID'];
	$recordID = $_GET['recordID'];
	$imageType = $_GET['imageType'];
	
	$query = "SELECT ".$imageType." FROM PACS_IMAGES WHERE RECORD_ID = :recordID AND IMAGE_ID = :imageID";
	$stmt = oci_parse ($conn, $query);
	oci_bind_by_name($stmt, ':recordID', $recordID);
	oci_bind_by_name($stmt, ':imageID', $imageID);
	
	oci_execute($stmt);
	$arr = oci_fetch_array($stmt, OCI_ASSOC);
	$result = $arr[$imageType]->load();
	header("Content-type: image/JPEG");
	echo $result;
	oci_close($conn);
?>