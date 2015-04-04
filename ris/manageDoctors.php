<!--
	User management module: Manage Doctors
	Update or delete existing family_doctor records, create new records.
	
	Author: Michael Williams
	
-->

<html>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<head><link href="base.css" rel="stylesheet" type="text/css"></head>
    <body>
        <?php
        
        	// warning handler prevents system printing of warning messages
        	// this is handled individually at the code level
        	function warning_handler($errno, $errstr) { 
				//no message to be printed, 
			}
			set_error_handler("warning_handler", E_WARNING);
			
            include("sessionCheck.php");
            include("PHPconnectionDB.php");
            include("userInfoDisplay.php");
            if (!sessionCheck()) {
				header('Location: loginModule.php');
            }
            else {
            	
            	echo "<h2>User Management Module</h2>";
            	userInfoDisplay();
            	echo "<a href='adminMenu.php'>Administrator menu</a><br><br>";
            	
            	error_reporting(E_ALL ^ E_NOTICE);            	
            	$conn = connect();

				// Add record button selected: Add a new doctor record from the filled form data 
            	if($_POST['hdnCmd'] == "Add")
            	{
            		$str = "INSERT INTO FAMILY_DOCTOR ";
            		$str .="(DOCTOR_ID, PATIENT_ID) ";
            		$str .="VALUES ";
            		$str .="('".$_POST['txtAdddoctor_id']."','".$_POST['txtAddpatient_id']."')";

            		$stid = oci_parse($conn, $str);
            		$res = oci_execute($stid);

            		if($res)
            		{
            			oci_commit($conn);
            		}
            		else
            		{
            			$e = oci_error($stid);
            			echo "Error Adding record: please verify the input is properly formatted [".$e['message']."]";
            		}

            	}
				
				// Update button selected: Update existing DB record from edited changes to row
            	if($_POST['hdnCmd'] == "Update")
            	{
					//try{
            		$str = "UPDATE FAMILY_DOCTOR SET ";
            		$str .="DOCTOR_ID = '".$_POST['txtEditdoctor_id']."' ";
            		$str .=",PATIENT_ID = '".$_POST['txtEditpatient_id']."' ";

            		$str .="WHERE DOCTOR_ID = '".$_POST['hdnEditdoctor_id']."' ";
            		$str .="AND PATIENT_ID = '".$_POST['hdnEditpatient_id']."'";
            		$stid = oci_parse($conn, $str);     
            		$res = oci_execute($stid);
            		
        			/*
            			oci_commit($conn);
            			}
        			catch (Exception $e) {
            		
            			echo "INVALID";
            			
            		}
            		*/
            		
            		if($res)
            		{
            			oci_commit($conn);
            		}

            		
            		else
            		{
            		
            			$e = oci_error($stid);
            			echo "Error updating record: please verify the input is properly formatted [".$e['message']."]"; 
            		}
					
            	}
            	
				// Delete button selected: Delete an existing record
            	if($_GET['Action'] == "Del")
            	{
            		$str = "DELETE FROM FAMILY_DOCTOR WHERE DOCTOR_ID = '".$_GET['keyID']."' ";
            		$stid = oci_parse($conn, $str);
            		$res = oci_execute($stid);
            		if($res)
            		{
            			oci_commit($conn);
            		}
            		else
            		{
            			$e = oci_error($stid);
            			echo "Error deleting record [".$e['message']."]";
            		}
            	} 	
                
            	// Select all records, render and display all records and form elements for editing, adding, deleting records
				$sql="SELECT * FROM FAMILY_DOCTOR ORDER BY DOCTOR_ID";
                $stid = oci_parse($conn, $sql);
                $res = oci_execute($stid);
                if (!$res) {
                    $err = oci_error($stid);
                    echo htmlentities($err['message']);
                }
                else {
                ?>    

					<div class="tabGroup">
				    <input type="radio" name="tabGroup1" id="rad1" class="tab1" onclick="document.location.href='manageUsers.php'"/>
				    <label for="rad1">Users</label>
				 
				    <input type="radio" name="tabGroup1" id="rad2" class="tab2" onclick="document.location.href='managePersons.php'"/>
				    <label for="rad2">Person</label>
				     
				    <input type="radio" name="tabGroup1" id="rad3" class="tab3" checked="checked"/>
				    <label for="rad3">Doctor</label>
				     
				    <br/>
				 
				    <div class="tab1"></div>
				    <div class="tab2"></div>
	    			<div class="tab3">
					
                	<form name="frmMain" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
                	<input type="hidden" name="hdnCmd" value="">
                	<table  >
                						
					<tr>
					    <th width=25> <div align="center">Doctor ID </div></th>
					    <th> <div align="center">Patient ID </div></th>

					</tr>
  					<?php 
  					
  					// Iterate through all selected rows
					while ($row=oci_fetch_array($stid, OCI_BOTH)) 
					{
						// If edit button selected, enter editing mode with editable fields on the selected row
						// Update button submits form to page (self) with POST using hidden button named Update
						if( $row['DOCTOR_ID'] == $_GET['keyID'] and $row['PATIENT_ID'] == $_GET['patientKeyID'] and $_GET['Action'] == "Edit")
						{
						?>
						<tr>
							<td><div align="center">
								<input type="text" name="txtEditdoctor_id"  size="15" value="<?php echo $row['DOCTOR_ID'];?>">
								<input type="hidden" name="hdnEditdoctor_id" value="<?php echo $row['DOCTOR_ID'];?>">
							</div></td>
							<td><div align="center">
								<input type="text" name="txtEditpatient_id" size="15" value="<?php echo $row['PATIENT_ID'];?>">
								<input type="hidden" name="hdnEditpatient_id" value="<?php echo $row['PATIENT_ID'];?>">
							</div></td>

							<td colspan="2" align="right"><div align="center">
								<input name="btnAdd" type="button" id="btnUpdate" value="Update" OnClick="frmMain.hdnCmd.value='Update';frmMain.submit();">
								<input name="btnAdd" type="button" id="btnCancel" value="Cancel" OnClick="window.location='<?php echo $_SERVER['PHP_SELF'];?>';">
							</div></td>
						</tr>
						<?php
						} else {
						// Render and display all record information for all non-edit mode rows
						// Icon buttons for editing and deleting records:
						// Edit button reloads page with edit POST action and record ID
						// Delete button reloads page with delete GET action and record ID							
  						?>
						  
						    <tr>
							    <td><div align="center"><?php echo $row['DOCTOR_ID'];?></div></td>
							    <td><div align="center"><?php echo $row['PATIENT_ID'];?></div></td>

							    <td align="center"><a href="<?php echo $_SERVER['PHP_SELF'];?>?Action=Edit&keyID=<?php echo $row['DOCTOR_ID'];?>&patientKeyID=<?php echo $row['PATIENT_ID'];?>"><img src="edit-16x16.png"></a></td>
								<td align="center"><a href="JavaScript:if(confirm('Confirm Delete?')==true){window.location='<?php echo $_SERVER['PHP_SELF'];?>?Action=Del&keyID=<?php echo $row['DOCTOR_ID'];?>';}"><img src="delete-16x16.png"></a></td>
					  		</tr>
						  
					   	<?php
						}
					}
				// Render and display form fields for adding new record details and form submission
				// Add button submits form to page (self) with POST using hidden button named Add
			  	?>						  	
				<tr>
				    <td><div align="center"><input type="text" name="txtAdddoctor_id" size="15" ></div></td>
				    <td><div align="center"><input type="text" name="txtAddpatient_id" size="15"></div></td>
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

	<?php 
    }
	?>					
    </body>
</html>
