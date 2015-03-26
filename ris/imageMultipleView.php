<!-- 
	Creates a table containing sequence of images (image_ID's) associated with the passed recordID
	Parameters: 
		$recordID, primary key ID
		$imageType, image size option (THUMBNAIL, REGULAR_SIZE, FULL_SIZE)
	
		Author: Michael Williams
 -->

<?php 
function multi_image($recordID, $imageType){
	echo "<b>Uploading images for radiology record #".$recordID."</b><p>"; 
	$conn = connect();
	// Select all image_ID associated with record_ID
	$sql = "SELECT IMAGE_ID FROM PACS_IMAGES WHERE RECORD_ID = :recordID AND :imageType IS NOT NULL";
	$stid = oci_parse($conn, $sql);
	oci_bind_by_name($stid, ':recordID', $recordID);
	oci_bind_by_name($stid, ':imageType', $imageType);
	oci_execute($stid);
	echo "<table><tr>";
	while($row = oci_fetch_array($stid, OCI_ASSOC)){
		if (!$row) {
			header('Status: 404 Not Found');
		} else {
		// redirect to imageView.php to render image with proper content type
?>
		<td><?=$row["IMAGE_ID"]?></td><td><img src="imageView.php?recordID=<?=$recordID?>&imageID=<?=$row["IMAGE_ID"]?>&imageType=<?=$imageType?>" /></td>
		<?php 
		}
	}
	echo "</tr></table>";
	oci_close($conn);
}
?>