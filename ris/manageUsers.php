<!--
	User management module: Manage Users
	Update or delete existing user records, create new records.
	
	Author: Michael Williams
	
-->
<html>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<head><link href="base.css" rel="stylesheet" type="text/css"></head>
    <body>
        <?php
			// warning handler prevents system printing of warning messages
			// these messages handled individually at the code level
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
            	
				// Add record button selected: Add a new user record from the filled form data
            	if($_POST["hdnCmd"] == "Add")
            	{
            		$str = "INSERT INTO USERS ";
            		$str .="(USER_NAME, PASSWORD, CLASS, PERSON_ID, DATE_REGISTERED) ";
            		$str .="VALUES ";
            		$str .="('".$_POST["txtAdduser_name"]."','".$_POST["txtAddpassword"]."' ";
            		$str .=",'".$_POST["txtAddclass"]."' ";
            		$str .=",'".$_POST["txtAddperson_id"]."',SYSDATE) ";
            		$stid = oci_parse($conn, $str);
            		$res = oci_execute($stid);
            		if($res)
            		{
            			oci_commit($conn);
            		}
            		else
            		{
            			$e = oci_error($stid);
            			echo "Error Adding the record: please verify the input is properly formatted [".$e['message']."]";
            		}
            	}
				
				// Update button selected: Update existing DB record from edited changes to row
            	if($_POST["hdnCmd"] == "Update")
            	{
            		$str = "UPDATE USERS SET ";
            		$str .="USER_NAME = '".$_POST["txtEdituser_name"]."' ";
            		$str .=",PASSWORD = '".$_POST["txtEditpassword"]."' ";
            		$str .=",CLASS = '".$_POST["txtEditclass"]."' ";
            		$str .=",PERSON_ID = '".$_POST["txtEditperson_id"]."' ";
            		//$str .=",DATE_REGISTERED = '".$_POST["txtEditdate_registered"]."' ";
            		$str .="WHERE USER_NAME = '".$_POST["hdnEdituser_name"]."' ";
            		$stid = oci_parse($conn, $str);     
            		$res = oci_execute($stid);
            		if($res)
            		{
            			oci_commit($conn);
            		}
            		else
            		{
            			$e = oci_error($stid);
            			echo "Error Updating the record: please verify the input is properly formatted [".$e['message']."]";
            		}
            	}
            	
				// Delete button selected: Delete an existing record
            	if($_GET["Action"] == "Del")
            	{
            		$str = "DELETE FROM USERS WHERE USER_NAME = '".$_GET["keyID"]."' ";
            		$stid = oci_parse($conn, $str);
            		$res = oci_execute($stid);
            		if($res)
            		{
            			oci_commit($conn);
            		}
            		else
            		{
            			$e = oci_error($stid);
            			echo "Error Deleting the record [".$e['message']."]";
            		}
            	} 	
            	
            	// Select all records, render and display all records and form elements for editing, adding, deleting records
				$sql="SELECT * FROM users ORDER BY user_name";
                $stid = oci_parse($conn, $sql);
                $res = oci_execute($stid);
                if (!$res) {
                    $err = oci_error($stid);
                    echo htmlentities($err['message']);
                }
                else {
?>    

					<div class="tabGroup">
				    <input type="radio" name="tabGroup1" id="rad1" class="tab1" checked="checked"/>
				    <label for="rad1">Users</label>
				 
				    <input type="radio" name="tabGroup1" id="rad2" class="tab2" onclick="document.location.href='managePersons.php'"/>
				    <label for="rad2">Person</label>
				     
				    <input type="radio" name="tabGroup1" id="rad3" class="tab3" onclick="document.location.href='manageDoctors.php'"/>
				    <label for="rad3">Doctor</label>
				     
				    <br/>
				 
				    <div class="tab1">
					
                	<form name="frmMain" method="post" action="<?php echo $_SERVER["PHP_SELF"];?>">
                	<input type="hidden" name="hdnCmd" value="">
                	<table  >
                						
					<tr>
					    <th width=25> <div align="center">Username </div></th>
					    <th> <div align="center">Password </div></th>
					    <th width=25 > <div align="center">Class </div></th>
					    <th> <div align="center">Person_id </div></th>
					    <th> <div align="center">Date_Registered </div></th>
					</tr>
  					<?php
  					
  					// Iterate through all selected rows
					while ($row=oci_fetch_array($stid, OCI_BOTH)) 
					{
						// If edit button selected, enter editing mode with editable fields on the selected row
						// Update button submits form to page (self) with POST using hidden button named Update
						if( $row["USER_NAME"] == $_GET["keyID"] and $_GET["Action"] == "Edit")
						{
?>
						<tr>
							<td><div align="center">
								<input type="text" name="txtEdituser_name"  size="15" value="<?php echo $row['USER_NAME'];?>">
								<input type="hidden" name="hdnEdituser_name" value="<?php echo $row["USER_NAME"];?>">
							</div></td>
							<td><div align="center">
								<input type="text" name="txtEditpassword" size="15" value="<?php echo $row["PASSWORD"];?>">
							</div></td>
							<td><div align="center">
								<input type="text" name="txtEditclass" size="1" value="<?php echo $row["CLASS"];?>">
							</div></td>
							<td><div align="center">
								<input type="text" name="txtEditperson_id" size="1" value="<?php echo $row["PERSON_ID"];?>">
							</div></td>
							<td><div align="center">
								<?php echo $row["DATE_REGISTERED"];?>
								<!-- <input type="text" name="txtEditdate_registered" size="20" value="<?php echo $row["DATE_REGISTERED"];?>"> -->
							</div></td>
							<td colspan="2" align="right"><div align="center">
								<input name="btnAdd" type="button" id="btnUpdate" value="Update" OnClick="frmMain.hdnCmd.value='Update';frmMain.submit();">
								<input name="btnAdd" type="button" id="btnCancel" value="Cancel" OnClick="window.location='<?php echo $_SERVER["PHP_SELF"];?>';">
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
							    <td><div align="center"><?php echo $row["USER_NAME"];?></div></td>
							    <td><div align="center"><?php echo $row["PASSWORD"];?></div></td>
							    <td><div align="center"><?php echo $row["CLASS"];?></div></td>
							    <td><div align="center"><?php echo $row["PERSON_ID"];?></div></td>
							    <td><div align="center"><?php echo $row["DATE_REGISTERED"];?></div></td>
							    <td align="center"><a href="<?php echo $_SERVER["PHP_SELF"];?>?Action=Edit&keyID=<?php echo $row["USER_NAME"];?>"><img src="edit-16x16.png"></a></td>
								<td align="center"><a href="JavaScript:if(confirm('Confirm Delete?')==true){window.location='<?php echo $_SERVER["PHP_SELF"];?>?Action=Del&keyID=<?php echo $row["USER_NAME"];?>';}"><img src="delete-16x16.png"></a></td>
					  		</tr>
						  
					   	<?php
						}
					}
				// Render and display form fields for adding new record details and form submission
				// Add button submits form to page (self) with POST using hidden button named Add
?>						  	
				<tr>
				    <td><div align="center"><input type="text" name="txtAdduser_name" size="15" ></div></td>
				    <td><div align="center"><input type="text" name="txtAddpassword" size="15"></div></td>
				    <td><div align="center"><input type="text" name="txtAddclass"  size="1"></div></td>
				    <td><div align="center"><div align="center"><input type="text" name="txtAddperson_id" size="1" ></div></td>
				    <td><div align="center"><?php echo date("d-M-y")?></div></td>
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
		<div class="tab2"></div>
	    <div class="tab3"></div>
		</div>
	<?php 
    }
?>					
    </body>
</html>
