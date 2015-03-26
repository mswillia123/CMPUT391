<!--  TODO

deal with error if page is navigated without ID's
change hdnCmd and txtEdit names
error check for unique key error

-->
<!-- 
	Selects and renders an image from passed recordID and associated imageID
	Variables passed by URL: 
		$recordID, primary key ID
		$imageID, unique image identifier for associated recordID
		$imageType, image size option (THUMBNAIL, REGULAR_SIZE, FULL_SIZE)
	
		Author: Michael Williams
 -->


<html>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<head><link href="base.css" rel="stylesheet" type="text/css"></head>
<h2>Image Upload</h2>
<a href="manageRadiologyRecords.php"> Radiology Record Module</a><p>
<?php
	include("PHPconnectionDB.php");
	include("imageMultipleView.php");
	include("imageResize.php");
	
	// Function: uploads/inserts an image to the pacs_images table
	// Parameters:
	//		$imgfile, image to be stored
	// 		$recordID, primary key ID
	//		$imageID, unique identifier for image associated with recordID
	//		$imageType, image size option (THUMBNAIL, REGULAR_SIZE, FULL_SIZE)
	function uploadImage($imgfile, $recordID, $imageID, $imageType){
		$conn = connect();
		$lob = oci_new_descriptor($conn, OCI_D_LOB);
		//$lob2 = oci_new_descriptor($conn, OCI_D_LOB);
		//$query = "SELECT ".$imageType." FROM PACS_I
		$query = "insert into pacs_images (record_id, image_id, ".$imageType.") "
				."values(:recordID, :imageID, empty_blob()) returning ".$imageType." into :blobdata";
		$stmt = oci_parse($conn, $query);
		oci_bind_by_name($stmt, ':recordID', $recordID);
		oci_bind_by_name($stmt, ':imageID', $imageID);
		oci_bind_by_name($stmt, ':blobdata', $lob, -1, OCI_B_BLOB);
		//oci_bind_by_name($stmt, ':blobdata2', $lob2, -1, OCI_B_BLOB);
		oci_execute($stmt, OCI_DEFAULT);  // Note OCI_DEFAULT
			
		if ($lob->savefile($imgfile)) {
			oci_commit($conn);
			echo "BLOB uploaded";
		}
		else {
			echo "Couldn't upload BLOB\n";
			echo "<h1>Record ID is: " . $recordID . "</h1>";
			echo "<h1>Image ID is: " . $imageID . "</h1>";
		}
			
		$lob->free();
		oci_close($conn);
		return;
	}
		
	// Display upload form if upload has not occurred
	if (!isset($_FILES['image'])) {
		$recordID = $_GET["recordID"];
		
		// Display thumbnail list
		// Must be all-caps for imageResize and imageView queries
		multi_image($recordID, 'THUMBNAIL'); 
?>		
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
		<table>
			<tr>
				<td>Image ID: </td><td><input type="text" name="txtEditimageID">
				<input type="hidden" name="hdnCmd" value="<?php echo $_GET["recordID"]?>">
				</td>
			</tr>
			<tr>
				<td>Image filename: </td>
				<td><input type="file" name= "image" ></td>
			</tr>		
			<tr>
				<td></td>
				<td><input type="submit" value="Upload" ></td>
			</tr>
		</table>
		</form>
	
	<?php
	} // end of if statement
	else {
		
	  	// POST file upload

		$recordID = $_POST["hdnCmd"];
		$imageID = $_POST["txtEditimageID"];		
		// Database connection
		$conn = connect();

		  
		  // Delete (overwrite) any existing BLOB with the same record and image ID's		  
		  $query = "delete from pacs_images where record_id = :recordID and image_id = :imageID";
		  echo $query;
		  $stmt = oci_parse ($conn, $query);
		  oci_bind_by_name($stmt, ':recordID', $recordID);
		  oci_bind_by_name($stmt, ':imageID', $imageID);
		  $e = oci_execute($stmt);
		  oci_free_statement($stmt);
		  $imgfile = $_FILES['image']['tmp_name'];
		  
		  //**Resizing is currently broken
		
		  $imgfile = resize(50, $imgfile);
		  //$imgfile2 = resize(200, $imgfile);
		  uploadImage($imgfile, $recordID, $imageID, 'THUMBNAIL');		  

		  header('Location: imageUploadModule.php?recordID='.$recordID.'');
	}
	
	?>
</html>