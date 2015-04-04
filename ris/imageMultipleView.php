<!-- 
	Creates a table containing sequence of images (image_ID's) associated with the passed recordID
	Parameters: 
		$recordID, primary key ID
		$imageType, image size option (THUMBNAIL, REGULAR_SIZE, FULL_SIZE)
	
		Author: Michael Williams
 -->

<?php 
function multi_image($recordID, $imageType){
	$conn = connect();
	// Select all image_ID associated with record_ID
	$query = "SELECT IMAGE_ID, ".$imageType." FROM PACS_IMAGES WHERE RECORD_ID = ".$recordID." AND ".$imageType." IS NOT NULL";
	$stid = oci_parse($conn, $query);
	oci_execute($stid);
	echo "<table ><tr>";
	// Display image of specified type (thumbnail, regular, full size)
	while($row = oci_fetch_array($stid, OCI_ASSOC)){
		if (!$row) {
			header('Status: 404 Not Found');
		} else {
			$result = base64_encode($row[$imageType]->load());
			echo '<td>';
			echo $row['IMAGE_ID'];
			echo '</td><td><img src="data:image/jpeg;base64,'.$result.'" /></span></td>';
		}
	}
	echo "</tr></table>";
	oci_close($conn);
}
?>
