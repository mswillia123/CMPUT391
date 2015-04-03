
<!-- 
	Selects and renders an image from passed recordID and associated imageID
	Variables passed by URL: 
		$recordID, primary key ID
		$imageID, unique image identifier for associated recordID
		$imageType, image size option (THUMBNAIL, REGULAR_SIZE, FULL_SIZE)	
		Author: Michael Williams
		
		Based on code from php.net website:
		http://php.net/manual/en/function.oci-new-descriptor.php
		Author: unknown/php.net contributors
 -->
<html>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<head><link href="base.css" rel="stylesheet" type="text/css"></head>
<body>

<h2>Image Upload</h2>
<a href="manageRadiologyRecords.php"> Radiology Record Module</a><p>

<?php
include("PHPconnectionDB.php");
include("sessionCheck.php");
include("imageMultipleView.php");
include("imageResize.php");
//Check for user session active
if (!sessionCheck()) {
	header('Location: loginModule.php');	
} else {
		
	// Function: uploads/inserts an image to the pacs_images table
	// Parameters:
	//		$imgfile, image to be stored
	// 		$recordID, primary key ID
	//		$imageID, unique identifier for image associated with recordID
	//		$imageType, image size option (THUMBNAIL, REGULAR_SIZE, FULL_SIZE)
	function uploadImage($imgfile, $recordID, $imageID, $imageType){
		error_reporting(E_ALL ^ E_NOTICE);
		$conn = connect();
		$lob = oci_new_descriptor($conn, OCI_D_LOB);
		
		//Check whether row with recordID and imageID exists
		$query = "select * from pacs_images "
		."where record_id = :recordID and image_id = :imageID";
		$stid = oci_parse($conn, $query);
		oci_bind_by_name($stid, ':recordID', $recordID);
		oci_bind_by_name($stid, ':imageID', $imageID);
		oci_execute($stid, OCI_DEFAULT);
		echo "Num of rows: ".oci_num_rows($stid);
		if (oci_fetch_all($stid, $res) > 0){
			//update record if it exists
			$query ="update pacs_images set ".$imageType." = empty_blob() "
			."where record_id = :recordID and image_id = :imageID "
			."returning ".$imageType." into :blobdata";
		} else {
			//insert new record if it does not exist
			$query = "insert into pacs_images (record_id, image_id, ".$imageType.") "
			."values(:recordID, :imageID, empty_blob()) "
			."returning ".$imageType." into :blobdata";
		}
		oci_free_statement($stid);
		echo $query;
		$stid = oci_parse($conn, $query);
		oci_bind_by_name($stid, ':recordID', $recordID);
		oci_bind_by_name($stid, ':imageID', $imageID);
		oci_bind_by_name($stid, ':blobdata', $lob, -1, OCI_B_BLOB);
		oci_execute($stid, OCI_DEFAULT);
		
		if ($lob->save($imgfile)) {
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
		
		// Display thumbnail list, calling multi_image function (imageMultipleView.php), which in
		// turn resizes and displays images via imageResize.php and imageView.php
		// Must be all-caps for imageResize and imageView queries
		multi_image($recordID, 'THUMBNAIL');
?>		
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
		<table>
			<tr>
				<td>Image ID: </td><td><input type="text" name="txtEditimageID">
				<input type="hidden" name="hiddenRecordID" value="<?php echo $_GET["recordID"]?>">
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
	} else {
		// POST: file upload button selected
		$recordID = $_POST["hiddenRecordID"];
		$imageID = $_POST["txtEditimageID"];		
		// Database connection
		$conn = connect();		
		  
		//Call image upload function to store images in database		
		//Store full sized image to DB
		uploadImage($_FILES['image']['tmp_name'], $recordID, $imageID, 'FULL_SIZE');
		
		//Resize to regular size image and store to DB
		$scaled = resize(200, $_FILES['image']['tmp_name']);
		uploadImage($scaled, $recordID, $imageID, 'REGULAR_SIZE');
		
		//Resize to thumbnail image and store to DB		
		$scaled = resize(50, $_FILES['image']['tmp_name']);		  
		uploadImage($scaled, $recordID, $imageID, 'THUMBNAIL');
		
		//Return to image upload module (which will display updated thumnail list with new image)
		header('Location: imageUploadModule.php?recordID='.$recordID.'');
	}
}
?>
	
</body>
</html>
