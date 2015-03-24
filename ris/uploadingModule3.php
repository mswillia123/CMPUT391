<html>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<head><link href="base.css" rel="stylesheet" type="text/css"></head>
<h2>Image Upload</h2>

<?php

include("PHPconnectionDB.php");

echo "Uploading images for radiology record #".$_GET["recordID"];
$conn = connect();
$recordID = $_GET["recordID"];
$sql = "SELECT IMAGE_ID FROM PACS_IMAGES WHERE RECORD_ID = :recordID AND FULL_SIZE IS NOT NULL";// . (int) $_GET["recordID"];

$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ':recordID', $recordID);

oci_execute($stid);
echo "<table>";
while($row = oci_fetch_array($stid, OCI_ASSOC)){
if (!$row) {
	header('Status: 404 Not Found');
} else {	
	?>
	<tr><td><img src="imageView.php?recordID=<?=$recordID?>&imageID=<?=$row["IMAGE_ID"]?>"/></td></tr>
	<?php 
	
}
}
?>
</table>
<?php 
oci_close($conn);


// Display upload form if upload has not occurred
if (!isset($_FILES['lob_upload'])) {
	

	

	//$recordID = $_GET["recordID"];
	//echo "<h1>ID is: " . $recordID . "</h1>";
// If nothing uploaded, display the upload form
//<tr><td>Description </td><td><input type="text" size="1024" name="description"/></td><br>
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>"
      method="POST" enctype="multipart/form-data">
<table>
<tr><td>Image ID: </td><td><input type="text" name="txtEditimageID">
	<input type="hidden" name="hdnCmd" value="<?php echo $_GET["recordID"]?>">
</td></tr>
<tr><td>Image filename: </td><td><input type="file" name= "lob_upload" ></td></tr>

<tr><td></td><td><input type="submit" value="Upload" ></td></tr>
</table>
</form>

<html>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<head><link href="base.css" rel="stylesheet" type="text/css"></head>

<?php
} // end of if statement
else {
  // POST file upload
  
	//echo "<h1>Second ID is: " . $_POST["hdnCmd"]. "</h1>";

	$recordID = $_POST["hdnCmd"];
	$imageID = $_POST["txtEditimageID"];
	
  // Establishes connection with database
  $conn = connect();
  //echo "<h1>Hello " . $recordID . "</h1>";
  
  // Delete (overwrite) any existing BLOB with the same record and image ID's
  $query = "delete from pacs_images where record_id = :recordID and image_id = :imageID";
  $stmt = oci_parse ($conn, $query);
  oci_bind_by_name($stmt, ':recordID', $recordID);
  oci_bind_by_name($stmt, ':imageID', $imageID);
  $e = oci_execute($stmt);

  // Insert the BLOB from PHP's temporary upload area
  $lob = oci_new_descriptor($conn, OCI_D_LOB);
  $stmt = oci_parse($conn, 'insert into pacs_images (record_id, image_id, full_size) '
        .'values(:recordID, :imageID, empty_blob()) returning full_size into :blobdata');
  oci_bind_by_name($stmt, ':recordID', $recordID);
  oci_bind_by_name($stmt, ':blobdata', $lob, -1, OCI_B_BLOB);
  oci_execute($stmt, OCI_DEFAULT);  // Note OCI_DEFAULT
  if ($lob->savefile($_FILES['lob_upload']['tmp_name'])) {
    oci_commit($conn);
    echo "BLOB uploaded";
  }
  else {
    echo "Couldn't upload BLOB\n";
  }
  $lob->free();
}

?>
</html>