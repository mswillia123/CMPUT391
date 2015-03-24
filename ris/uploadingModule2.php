<html>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<head><link href="base.css" rel="stylesheet" type="text/css"></head>
<?php
include("PHPconnectionDB.php");
if (!isset($_FILES['lob_upload'])) {
// If nothing uploaded, display the upload form
//<tr><td>Description </td><td><input type="text" size="1024" name="description"/></td><br>
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>"
      method="POST" enctype="multipart/form-data">
<table>
	<tr><td>Record ID:</td> <td><input type="number" name="record_id"/></td></tr>
	<tr><td>Patient ID: </td> <td><input type="number" name="patient_id"/></td></tr>
	<tr><td>Doctor ID: </td> <td><input type="number" name="doctor_id"/></td></tr>
	<tr><td>Radiologist ID: </td> <td><input type="number" name="radiologist_id"/></td></tr>
	<tr><td>Test Type: </td> <td><input type="text" name="test_type"/></td></tr>
	<tr><td>Prescribing Date: </td><td><input type="date" name="prescribing_date"/></td></tr>
	<tr><td>Test Date: </td><td><input type="date" name="test_date"/></td></tr>
	<tr><td>Diagnosis: </td><td><input type="text" name="diagnosis"/></td></tr>
	<tr><td>Description: </td><td><textarea name="description"></textarea></td></tr>
	<tr><td>Image filename: </td><td><input type="file" name="lob_upload"></td></tr>
<tr><td></td><td><input type="submit" value="Upload"></td></tr>
</table>
</form>


<html>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<head><link href="base.css" rel="stylesheet" type="text/css"></head>

<?php
} // closing brace from 'if' in earlier PHP code
else {
  // else script was called with data to upload


  $myblobid = 1; // should really be a unique id e.g. a sequence number

  // Establishes connection with database
  $conn = connect();
  //$conn = oci_connect("phphol", "welcome", "//localhost/orcl");

  echo $_POST['test_date'];
  $query = "insert into radiology_record (record_id,"
   ."patient_id,"
   ."doctor_id,"
   ."radiologist_id,"
  ."test_type,"
    ."prescribing_date,"
  ."test_date,"
    ."diagnosis,"
  ."description)"
  ." values({$_POST['record_id']},"
   ."{$_POST['patient_id']},"
   ."{$_POST['doctor_id']},"
   ."{$_POST['radiologist_id']},"
  ."'{$_POST['test_type']}',"
	."TO_DATE('{$_POST['prescribing_date']}', 'yyyy-mm-dd' ),"
  ."TO_DATE('{$_POST['test_date']}', 'yyyy-mm-dd' ),"
  ."'{$_POST['diagnosis']}',"
  ."'{$_POST['description']}')";
  
  $stmt = oci_parse ($conn, $query);
  //oci_bind_by_name($stmt, ':myblobid', $myblobid);
  $e = oci_execute($stmt);
  oci_commit($conn);
   
  // Delete any existing BLOB
  $query = 'delete from pacs_images where record_id = :myblobid';
  $stmt = oci_parse ($conn, $query);
  oci_bind_by_name($stmt, ':myblobid', $myblobid);
  $e = oci_execute($stmt);

  // Insert the BLOB from PHP's temporary upload area
  $lob = oci_new_descriptor($conn, OCI_D_LOB);
  $stmt = oci_parse($conn, 'insert into pacs_images (record_id, image_id, full_size) '
        .'values(:myblobid, 1, empty_blob()) returning full_size into :blobdata');
  oci_bind_by_name($stmt, ':myblobid', $myblobid);
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