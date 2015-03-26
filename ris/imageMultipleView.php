<?php 
function multi_image($recordID, $imageType){
	echo "Uploading images for radiology record #".$recordID; //.$_GET["recordID"];
	$conn = connect();
	//$recordID = $_GET["recordID"];
	$sql = "SELECT IMAGE_ID FROM PACS_IMAGES WHERE RECORD_ID = :recordID AND :imageType IS NOT NULL";// . (int) $_GET["recordID"];
	echo $imageType;

	$stid = oci_parse($conn, $sql);
	oci_bind_by_name($stid, ':recordID', $recordID);
	oci_bind_by_name($stid, ':imageType', $imageType);
	echo $stid;
	oci_execute($stid);
	echo "<table>";
	while($row = oci_fetch_array($stid, OCI_ASSOC)){
		if (!$row) {
			header('Status: 404 Not Found');
		} else {	
?>	
			<tr><td><img src="imageView.php?recordID=<?=$recordID?>&imageID=<?=$row["IMAGE_ID"]?>&imageType=<?=$imageType?>" /></td><td>'<?=$imageType?>'</td></tr>
			<?php 	
		}
	}
?>
	</table>
	<?php 
	oci_close($conn);
}
?>