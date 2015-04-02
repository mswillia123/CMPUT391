<!--TODO*******************
	error checking
	fix date field population issue (blank insert to db) on edit
	close connections
	use sqlquery function
	render return - menu return
	order by record id
	indicate borrowed code references
	cleanup upload code
-->

<!--
	Persons table record management:
	Update or delete existing records, create new records.
	
	Author: Michael Williams
-->

<html>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<head><link href="base.css" rel="stylesheet" type="text/css"></head>
<body>
<?php
include("sessionCheck.php");
include("PHPconnectionDB.php");
if (!sessionCheck()) {
	echo 'Not logged in! <br/>';
} else {
	error_reporting(E_ALL ^ E_NOTICE);
	$conn = connect();
	// 'Add record' button selected: Add a new DB record from the filled form data
	if ($_POST["hdnCmd"] == "Add") {
		$str = "INSERT INTO PERSONS ";
		$str .= "(PERSON_ID, FIRST_NAME, LAST_NAME, ADDRESS, EMAIL, PHONE) ";
		$str .= "VALUES ";
		$str .= "('" . $_POST["txtAddperson_id"] . "','" . $_POST["txtAddfirst_name"] . "' ";
		$str .= ",'" . $_POST["txtAddlast_name"] . "' ";
		$str .= ",'" . $_POST["txtAddaddress"] . "' ";
		$str .= ",'" . $_POST["txtAddemail"] . "' ";
		$str .= ",'" . $_POST["txtAddphone"] . "') ";
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
	if ($_POST["hdnCmd"] == "Update") {
		$str = "UPDATE PERSONS SET ";
		$str .= "PERSON_ID = '" . $_POST["txtEditperson_id"] . "' ";
		$str .= ",FIRST_NAME = '" . $_POST["txtEditfirst_name"] . "' ";
		$str .= ",LAST_NAME = '" . $_POST["txtEditlast_name"] . "' ";
		$str .= ",ADDRESS = '" . $_POST["txtEditaddress"] . "' ";
		$str .= ",EMAIL = '" . $_POST["txtEditemail"] . "' ";
		$str .= ",PHONE = '" . $_POST["txtEditphone"] . "' ";
		$str .= "WHERE PERSON_ID = '" . $_POST["hdnEditperson_id"] . "' ";
		$stid = oci_parse($conn, $str);
		$res  = oci_execute($stid);
		if ($res) {
			oci_commit($conn);
		} else {
			$e = oci_error($stid);
			echo "Error Update [" . $e['message'] . "]";
		}
	}
	// Delete button selected: Delete an existing record
	if ($_GET["Action"] == "Del") {
		$str  = "DELETE FROM PERSONS WHERE PERSON_ID = '" . $_GET["keyID"] . "' ";
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
	$sql  = "SELECT * FROM persons";
	$stid = oci_parse($conn, $sql);
	$res  = oci_execute($stid);
	if (!$res) {
		$err = oci_error($stid);
		echo htmlentities($err['message']);
	} else {
?>              
		<h2>User Management Module</h2>
		
		<div class="tabGroup">
		<input type="radio" name="tabGroup1" id="rad1" class="tab1" onclick="document.location.href='manageUsers.php'" />
		<label for="rad1">Users</label>
	 
		<input type="radio" name="tabGroup1" id="rad2" class="tab2" checked="checked"/>
		<label for="rad2">Person</label>
		 
		<input type="radio" name="tabGroup1" id="rad3" class="tab3" onclick="document.location.href='manageDoctors.php'"/>
		<label for="rad3">Doctor</label>
		 
		<br/>
	 
		<div class="tab1"></div>
		<div class="tab2">
		
		<form name="frmMain" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<input type="hidden" name="hdnCmd" value="">
		<table  >
							
		<tr>
			<th width=25> <div align="center">Person ID </div></th>
			<th> <div align="center">First Name </div></th>
			<th width=25 > <div align="center">Last Name </div></th>
			<th> <div align="center">Address </div></th>
			<th> <div align="center">Email </div></th>
			<th> <div align="center">Phone </div></th>
		</tr>
		<?php
		// Iterate through all selected rows
		while ($row = oci_fetch_array($stid, OCI_BOTH)) 
		{
			// If edit button selected, enter editing mode with editable fields on the selected row
			if ($row["PERSON_ID"] == $_GET["keyID"] and $_GET["Action"] == "Edit") 
			{
?>
				<tr>
					<td><div align="center">
						<input type="text" name="txtEditperson_id"  size="1" value="<?php echo $row['PERSON_ID']; ?>">
						<input type="hidden" name="hdnEditperson_id" value="<?php echo $row['PERSON_ID']; ?>">
					</div></td>
					<td><div align="center">
						<input type="text" name="txtEditfirst_name" size="15" value="<?php echo $row['FIRST_NAME']; ?>">
					</div></td>
					<td><div align="center">
						<input type="text" name="txtEditlast_name" size="15" value="<?php echo $row['LAST_NAME']; ?>">
					</div></td>
					<td><div align="center">
						<input type="text" name="txtEditaddress" size="15" value="<?php echo $row['ADDRESS']; ?>">
					</div></td>
					<td><div align="center">
						<input type="text" name="txtEditemail" size="15" value="<?php echo $row['EMAIL']; ?>">
					</div></td>
					<td><div align="center">
						<input type="text" name="txtEditphone" size="15" value="<?php echo $row['PHONE']; ?>">
					</div></td>	


					<td colspan="2" align="right"><div align="center">
						<input name="btnAdd" type="button" id="btnUpdate" value="Update" OnClick="frmMain.hdnCmd.value='Update';frmMain.submit();">
						<input name="btnAdd" type="button" id="btnCancel" value="Cancel" OnClick="window.location='<?php echo $_SERVER['PHP_SELF']; ?>';">
					</div></td>
				</tr>
				<?php
			} else {
				// Render and display all record information for all non-edit mode rows 
?>					  
				<tr>
					<td><div align="center"><?php echo $row["PERSON_ID"]; ?></div></td>
					<td><div align="center"><?php echo $row["FIRST_NAME"]; ?></div></td>
					<td><div align="center"><?php echo $row["LAST_NAME"]; ?></div></td>
					<td><div align="center"><?php echo $row["ADDRESS"]; ?></div></td>
					<td><div align="center"><?php echo $row["EMAIL"]; ?></div></td>
					<td><div align="center"><?php echo $row["PHONE"]; ?></div></td>
					<td align="center"><a href="<?php echo $_SERVER["PHP_SELF"]; ?>?Action=Edit&keyID=<?php echo $row["PERSON_ID"]; ?>"><img src="edit-16x16.png"></a></td>
					<td align="center"><a href="JavaScript:if(confirm('Confirm Delete?')==true){window.location='<?php echo $_SERVER["PHP_SELF"]; ?>?Action=Del&keyID=<?php echo $row["PERSON_ID"]; ?>';}"><img src="delete-16x16.png"></a></td>
				</tr>			  
				<?php
			}
		}
				// Render and display form fields for new record details and submission
?>				
				<tr>
				    <td><div align="center"><input type="text" name="txtAddperson_id" size="15" ></div></td>
				    <td><div align="center"><input type="text" name="txtAddfirst_name" size="15"></div></td>
				    <td><div align="center"><input type="text" name="txtAddlast_name"  size="15"></div></td>
					<td><div align="center"><input type="text" name="txtAddaddress"  size="15"></div></td>
				    <td><div align="center"><input type="text" name="txtAddemail"  size="15"></div></td>
				    <td><div align="center"><input type="text" name="txtAddphone"  size="15"></div></td>				    
				    <td colspan="2" align="right"><div align="center">
				      <input name="btnAdd" type="image" src="plus-16x16.png" id="btnAdd" value="Add" OnClick="frmMain.hdnCmd.value='Add';frmMain.submit();">
				    </div></td>
			    </tr>
			</table>
		</form>
		<?php
	}
?>
	</div>

	<div class="tab3"></div>
	</div>
	<?php
}	//end of sessionCheck if statement
?>					
</body>
</html>

