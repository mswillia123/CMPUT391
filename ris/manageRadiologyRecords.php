<!--
	Manage Radiology Records:
	Update or delete existing records, create new records, add images to records.
	
	Author: Michael Williams
-->

<html>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<head><link href="base.css" rel="stylesheet" type="text/css"></head>
<body>
<?php
include("sessionCheck.php");
include("PHPconnectionDB.php");
include("userInfoDisplay.php");

if (!sessionCheck()) {
	header('Location: loginModule.php');
} else {
	
	echo "<h2>Radiology Record Module</h2>";
	userInfoDisplay();
	echo "<a href='radiologistMenu.php'> Radiologist menu</a><p>";
	error_reporting(E_ALL ^ E_NOTICE);
	$conn = connect();
	
	// Add record button selected: Add a new radiology record from the filled form data
	if ($_POST['hdnCmd'] == "Add") {
		$str = "insert into radiology_record (record_id, patient_id, doctor_id, radiologist_id, test_type, prescribing_date, test_date, diagnosis, description) ";
		$str .= "values ('" . $_POST['txtAddrecord_id'] . "' ";
		$str .= ",'" . $_POST['txtAddpatient_id'] . "' ";
		$str .= ",'" . $_POST['txtAdddoctor_id'] . "' ";
		$str .= ",'" . $_POST['txtAddradiologist_id'] . "' ";
		$str .= ",'" . $_POST['txtAddtest_type'] . "' ";
		$str .= ", TO_DATE('" . $_POST['txtAddprescribing_date'] . "', 'yyyy-mm-dd') ";
		$str .= ", TO_DATE('" . $_POST['txtAddtest_date'] . "', 'yyyy-mm-dd') ";
		$str .= ",'" . $_POST['txtAdddiagnosis'] . "' ";
		$str .= ",'" . $_POST['txtAdddescription'] . "' ";
		$str .= ")";
		$stid = oci_parse($conn, $str);
		$res  = oci_execute($stid);
		if ($res) {
			oci_commit($conn);
		} else {
			$e = oci_error($stid);
			echo "Error Add [" . $e['message'] . "]";
		}
		
		// Insert related record into radiology_search table
		$str = "DECLARE	patient_name varchar(49);"; // rs_diagnosis varchar(128); rs_description varchar(1024);";
		$str .= "BEGIN ";
		$str .="SELECT first_name || ' ' || last_name INTO patient_name ";
		$str .="FROM   persons ";
		$str .="WHERE  person_id = ". $_POST['txtAddpatient_id'].";";	
			
		$str .="INSERT INTO radiology_search VALUES";
		$str .="(". $_POST['txtAddrecord_id'] .", ". $_POST['txtAddpatient_id'] .", patient_name, '" . $_POST['txtAdddiagnosis'] . "', '" . $_POST['txtAdddescription'] . "'); END;";
		
		//echo $str;
		$stid = oci_parse($conn, $str);
		$res  = oci_execute($stid);
		if ($res) {
			oci_commit($conn);
		} else {
			$e = oci_error($stid);
			echo "Error Add [" . $e['message'] . "]";
		}		
	}
	
	// Update button selected: Update existing DB record from edited changes to row
	if ($_POST['hdnCmd'] == "Update") {
		$str = "update radiology_record set ";
		$str .= "record_id = '" . $_POST['txtEditrecord_id'] . "' ";
		$str .= ",patient_id = '" . $_POST['txtEditpatient_id'] . "' ";
		$str .= ",doctor_id = '" . $_POST['txtEditdoctor_id'] . "' ";
		$str .= ",radiologist_id = '" . $_POST['txtEditradiologist_id'] . "' ";
		$str .= ",test_type = '" . $_POST['txtEdittest_type'] . "' ";
		$str .= ",prescribing_date = TO_DATE('" . $_POST['txtEditprescribing_date'] . "', 'yyyy-mm-dd') ";
		$str .= ",test_date = TO_DATE('" . $_POST['txtEdittest_date'] . "', 'yyyy-mm-dd') ";
		$str .= ",diagnosis = '" . $_POST['txtEditdiagnosis'] . "' ";
		$str .= ",description = '" . $_POST['txtEditdescription'] . "' ";
		$str .= "where record_id = '" . $_POST['hdnEditrecord_id'] . "' ";
		$stid = oci_parse($conn, $str);
		$res  = oci_execute($stid);
		if ($res) {
			oci_commit($conn);
		} else {
			$e = oci_error($stid);
			echo "Error Update [" . $e['message'] . "]";
		}
		
		// Update related record in radiology_search table
		$str = "DECLARE	patient_name varchar(49);"; // rs_diagnosis varchar(128); rs_description varchar(1024);";
		$str .= "BEGIN ";
		$str .="SELECT first_name || ' ' || last_name INTO patient_name ";
		$str .="FROM   persons ";
		$str .="WHERE  person_id = ". $_POST['txtEditpatient_id'].";";
		
		$str .="update radiology_search set ";
		$str .= "patient_id = '" . $_POST['txtEditpatient_id'] . "' ";
		$str .= ",patient_name = patient_name ";
		$str .= ",diagnosis = '" . $_POST['txtEditdiagnosis'] . "' ";
		$str .= ",description = '" . $_POST['txtEditdescription'] . "'";
		$str .= "where record_id = '". $_POST['txtEditrecord_id'] ."'; END; ";
		
		//echo $str;
		$stid = oci_parse($conn, $str);
		$res  = oci_execute($stid);
		if ($res) {
			oci_commit($conn);
		} else {
			$e = oci_error($stid);
			echo "Error Add [" . $e['message'] . "]";
		}
	}
	
	// Delete button selected: Delete an existing record
	if ($_GET['Action'] == "Del") {
		
		$str  = "delete from radiology_search where record_id = '" . $_GET['keyID'] . "' ";
		$stid = oci_parse($conn, $str);
		$res  = oci_execute($stid);
		if ($res) {
			oci_commit($conn);
		} else {
			$e = oci_error($stid);
			echo "Error Delete [" . $e['message'] . "]";
		}		
		
		$str  = "delete from radiology_record where record_id = '" . $_GET['keyID'] . "' ";
		$stid = oci_parse($conn, $str);
		$res  = oci_execute($stid);
		if ($res) {
			oci_commit($conn);
		} else {
			$e = oci_error($stid);
			echo "Error Delete [" . $e['message'] . "]";
		}

	}

	// Select all records, render and display all records and form elements for editing, adding, deleting records
	$sql  = "select * from radiology_record order by record_id";
	$stid = oci_parse($conn, $sql);
	$res  = oci_execute($stid);
	if (!$res) {
		$err = oci_error($stid);
		echo htmlentities($err['message']);
	} else {
	
?>             
				
		<form name="frmMain" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
        <input type="hidden" name="hdnCmd" value=".">
		<table >                						
		<tr>
			<th> <div align="center" >Record ID</div></th>
			<th> <div align="center">Patient ID </div></th>
			<th> <div align="center">Doctor ID </div></th>
			<th> <div align="center">Radiologist ID </div></th>
			<th> <div align="center">Test Type </div></th>
			<th> <div align="center">Prescribing Date </div></th>
			<th> <div align="center">Test Date </div></th>
			<th> <div align="center">Diagnosis </div></th>
			<th> <div align="center">Description </div></th>
		</tr>
		<?php
		// Iterate through all selected rows
		while ($row = oci_fetch_array($stid, OCI_BOTH))
		{			
			// If edit button selected, enter editing mode with editable fields on the selected row
			// Update button submits form to page (self) with POST using hidden button named Update
			if ($row['RECORD_ID'] == $_GET['keyID'] and $_GET['Action'] == "Edit") {?>
				<tr>
					<td><div align="center"><?php echo $row['RECORD_ID']; ?>
						<input type="hidden" name="txtEditrecord_id"  size="1" value="<?php echo $row['RECORD_ID']; ?>">
						<input type="hidden" name="hdnEditrecord_id" value="<?php echo $row['RECORD_ID']; ?>">
					</div></td>
					<td><div align="center"><input type="text" name="txtEditpatient_id" size="15" value="<?php echo $row['PATIENT_ID']; ?>"></div></td>
					<td><div align="center"><input type="text" name="txtEditdoctor_id" size="15" value="<?php echo $row['DOCTOR_ID']; ?>"></div></td>
					<td><div align="center"><input type="text" name="txtEditradiologist_id" size="15" value="<?php echo $row['RADIOLOGIST_ID']; ?>"></div></td>
					<td><div align="center"><input type="text" name="txtEdittest_type" size="15" value="<?php echo $row['TEST_TYPE']; ?>"></div></td>
					<td><div align="center"><input type="date" name="txtEditprescribing_date" size="15" value="<?php echo $row['TEST_DATE']; ?>"></div></td>
					<td><div align="center"><input type="date" name="txtEdittest_date" size="15" value="<?php echo $row['PRESCRIBING_DATE']; ?>"></div></td>							
					<td><div align="center"><input type="text" name="txtEditdiagnosis" size="15" value="<?php echo $row['DIAGNOSIS']; ?>"></div></td>	
					<td><div align="center"><input type="text" name="txtEditdescription" size="15" value="<?php echo $row['DESCRIPTION']; ?>"></div></td>
					<td colspan="2" align="right"><div align="center">
						<input name="btnAdd" type="button" id="btnUpdate" value="Update" OnClick="frmMain.hdnCmd.value='Update';frmMain.submit();">
						<input name="btnAdd" type="button" id="btnCancel" value="Cancel" OnClick="window.location='<?php echo $_SERVER['PHP_SELF'];?>';">

					</div></td>
				</tr>
				<?php
			} else {
				// Render and display all record information for all non-edit mode rows
				// Icon buttons for editing and deleting records, and adding images:
				// Edit button reloads page with edit POST action and record ID
				// Image button redirects to image upload page and passes record ID 
				// Delete button reloads page with delete GET action and record ID
?>			  
				<tr>
					
					<td><div align="center"><?php echo $row['RECORD_ID']; ?></div></td>
					<td><div align="center"><?php echo $row['PATIENT_ID']; ?></div></td>
					<td><div align="center"><?php echo $row['DOCTOR_ID']; ?></div></td>
					<td><div align="center"><?php echo $row['RADIOLOGIST_ID']; ?></div></td>
					<td><div align="center"><?php echo $row['TEST_TYPE']; ?></div></td>
					<td><div align="center"><?php echo $row['PRESCRIBING_DATE']; ?></div></td>
					<td><div align="center"><?php echo $row['TEST_DATE']; ?></div></td>
					<td><div align="center"><?php echo $row['DIAGNOSIS']; ?></div></td>
					<td><div align="center" ><?php echo $row['DESCRIPTION']; ?></div></td>							    
					<td align="center"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?Action=Edit&keyID=<?php echo $row['RECORD_ID']; ?>"><img src="edit-16x16.png"></a></td>
					<td align="center"><a href="JavaScript:if(confirm('Confirm Delete?')==true){window.location='<?php echo $_SERVER['PHP_SELF']; ?>?Action=Del&keyID=<?php echo $row['RECORD_ID']; ?>';}"><img src="delete-16x16.png"></a></td>
					<td align="center"><a href="imageUploadModule.php?recordID=<?php echo $row['RECORD_ID']; ?>"><img src="jpeg-16x16.png"></a></td>								
				</tr>
			  
				<?php
			}
		}
				// Render and display form fields for adding new record details and form submission
				// Add button submits form to page (self) with POST using hidden button named Add
?>
				<tr>					
				    <td><div align="center"><input type="text" name="txtAddrecord_id" size="1" ></div></td>
				    <td><div align="center"><input type="text" name="txtAddpatient_id" size="1"></div></td>
				    <td><div align="center"><input type="text" name="txtAdddoctor_id"  size="1"></div></td>
					<td><div align="center"><input type="text" name="txtAddradiologist_id"  size="2"></div></td>
				    <td><div align="center"><input type="text" name="txtAddtest_type"  size="15"></div></td>				  				    
					<td><div align="center"><input type="date" name="txtAddprescribing_date" size="10" ></div></td>
					<td><div align="center"><input type="date" name="txtAddtest_date" size="10" ></div></td>							
					<td><div align="center"><input type="text" name="txtAdddiagnosis" size="15" ></div></td>	
					<td><div align="center"><input type="text" name="txtAdddescription" size="15" ></div></td>				    
				    <td colspan="2" align="right"><div align="center">
				      <input name="btnAdd" type="image" src="plus-16x16.png" id="btnAdd" value="Add" OnClick="frmMain.hdnCmd.value='Add';frmMain.submit();">
				    </div></td>
			    </tr>
		</table>
		</form>
		
		
		<?php
		$str  = "select p.person_id, p.first_name, p.last_name, u.user_name, u.class from persons p, users u where p.person_id = u.person_id";
		$stid = oci_parse($conn, $str);
		$res  = oci_execute($stid);
		if ($res) {
			oci_commit($conn);
		} else {
			$e = oci_error($stid);
			echo "Error Add [" . $e['message'] . "]";
		}
		
		//Display selected data
		echo "<h3>Person reference table</h3>";
		echo "<table>";
		$number_columns = oci_num_fields($stid);
		for ($i = 1; $i <= $number_columns; ++$i) {
			echo " <th style='text-align:center'>".strtolower(oci_field_name($stid, $i))."</th>";
		}
		while ($row = oci_fetch_array($stid, OCI_NUM)) {
			echo "<tr>";
			//foreach ($row as $item) {
				//echo "<td>". ($item !== null ? htmlentities($item, ENT_QUOTES):".") . "</td>";
			//}
			for ($i = 0; $i < $number_columns; $i++) {
				echo "<td style='text-align:center'>" . ($row[$i] != null ? $row[$i] : "-------"). "</td>";
			}
			echo "</tr>";			
		}
		
		
	}

} //end of sessionCheck if statement
?>					
</body>
</html>

