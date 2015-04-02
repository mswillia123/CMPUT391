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
		/*
		$query = "insert into pacs_images (record_id, image_id, ".$imageType.") "
			."values(:recordID, :imageID, empty_blob()) returning ".$imageType." into :blobdata "
			."on duplicate key update ".$imageType." = empty_blob() returning ".$imageType." into :blobdata";
		*/
		//update pacs_images set ".$imageType." = empty_blob() where record_id = :recordID and image_id = :imageID returning ".$imageType." into :blobdata";
		/*
		begin 
		insert into t (mykey, mystuff)
		values ('X', 123);
		exception
		when dup_val_on_index then
		update t
		set    mystuff = 123
		where  mykey = 'X';
		end;
		*/
		
		//$query = "begin "
		/*
		$query = "delete from pacs_images where record_id = :recordID and image_id = :imageID";
		//echo $query;
		$stmt = oci_parse ($conn, $query);
		oci_bind_by_name($stmt, ':recordID', $recordID);
		oci_bind_by_name($stmt, ':imageID', $imageID);
		$e = oci_execute($stmt);
		oci_free_statement($stmt);
		*/

		/*
		$query = "insert into pacs_images (record_id, image_id, ".$imageType.") "
		."values(:recordID, :imageID, empty_blob()) "		
		."returning ".$imageType." into :blobdata;"
		."exception "
		."when dup_val_on_index then "
		."update pacs_images set ".$imageType." = empty_blob() "
		."where record_id = :recordID and image_id = :imageID "
		."returning ".$imageType." into :blobdata; ";
		*/
		
		/* Merge does not work with 'returning'
		 * this was the last query that I tried
		 */
		/*
		$query = "merge into pacs_images "
		."using dual on (record_id = :recordID and image_id = :imageID) "
		."when matched then "
		."update set ".$imageType." = empty_blob() "
		//."where record_id = :recordID and image_id = :imageID "
		."returning ".$imageType." into :blobdata "
		."when not matched then "		
		."insert (record_id, image_id, ".$imageType.") "
		."values (:recordID, :imageID, empty_blob()) "
		."returning ".$imageType." into :blobdata;";
		*/
		//."end;";
		$query = "update pacs_images set ".$imageType." = empty_blob() "
		."where record_id = :recordID and image_id = :imageID;";	
		echo $query;
		$query = oci_parse($conn, $query);
		oci_bind_by_name($query, ':recordID', $recordID);
		oci_bind_by_name($query, ':imageID', $imageID);
		oci_bind_by_name($query, ':blobdata', $lob, -1, OCI_B_BLOB);
		//oci_bind_by_name($stmt, ':blobdata2', $lob2, -1, OCI_B_BLOB);
		oci_execute($query, OCI_DEFAULT);  // Note OCI_DEFAULT
		
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
		
		// Display thumbnail list
		// Must be all-caps for imageResize and imageView queries
		multi_image($recordID, 'REGULAR_SIZE'); 
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

		  
		  // Delete then overwrite) any existing BLOB with the same record and image ID's
		  /*		  
		  $query = "delete from pacs_images where record_id = :recordID and image_id = :imageID";
		  //echo $query;
		  $stmt = oci_parse ($conn, $query);
		  oci_bind_by_name($stmt, ':recordID', $recordID);
		  oci_bind_by_name($stmt, ':imageID', $imageID);
		  $e = oci_execute($stmt);
		  oci_free_statement($stmt);
		  */
		  //$imgfile = $_FILES['image']['tmp_name'];
		  //$src_img = imagecreatefromjpeg($imgfile);
		  //**Resizing is currently broken
		
		  //$src_img = resize(50, $src_img);
		  //$scaled = addslashes($scaled);
		  
		//Upload full sized image  
		uploadImage($_FILES['image']['tmp_name'], $recordID, $imageID, 'FULL_SIZE');
		echo '<img src="data:image/jpg;base64,' .  base64_encode($_FILES['image']['tmp_name'])  . '" />';
		
		//Resize to regular size image and store to DB
		$scaled = resize(200, $_FILES['image']['tmp_name']);
		uploadImage($scaled, $recordID, $imageID, 'REGULAR_SIZE');
		
		//Resize to thumbnail image and store to DB
		echo '<img src="data:image/jpg;base64,' .  base64_encode($scaled)  . '" />';
		
		  $scaled = resize(50, $_FILES['image']['tmp_name']);		  
		  uploadImage($scaled, $recordID, $imageID, 'THUMBNAIL');
		  echo '<img src="data:image/jpg;base64,' .  base64_encode($scaled)  . '" />';
		  


		  
		  
		  /*
		  header("Content-type: image/JPEG");
		  ?>
		  <td><?=$imageID?></td><td><img src="imageView.php?recordID=<?=$recordID?>&imageID=<?=$row["IMAGE_ID"]?>&imageType=<?=$imageType?>" /></td>
		  <?php 		  //echo $scaled;
		  */
		  //$src_img = resize(50, $_FILES['image']['tmp_name']);
		  //$imgfile2 = resize(200, $imgfile);


		  //$imgfile2 = resize(50, $imgfile);
		  //$imgfile2 = resize(200, $imgfile);
		  //$src_img = imagecreatefromjpeg($imgfile);
		  //uploadImage($src_img, $recordID, $imageID, 'THUMBNAIL');

		  //header('Location: imageUploadModule.php?recordID='.$recordID.'');
	}
	
	?>
</html>
