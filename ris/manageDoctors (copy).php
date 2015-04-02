<html>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<head><link href="base.css" rel="stylesheet" type="text/css"></head>
    <body>
        <?php
			
            include("sessionCheck.php");
            include("PHPconnectionDB.php");
            if (!sessionCheck()) {
                echo 'Not logged in! <br/>';
            }
            else {
            	error_reporting(E_ALL ^ E_NOTICE);
            	
            	$conn = connect();
            	
            	// Add
            	if($_POST["hdnCmd"] == "Add")
            	{
            		$str = "INSERT INTO FAMILY_DOCTOR ";
            		$str .="(DOCTOR_ID, PATIENT_ID) ";
            		$str .="VALUES ";
            		$str .="('".$_POST["txtAdddoctor_id"]."','".$_POST["txtAddpatient_id"]."')";

            		$stid = oci_parse($conn, $str);
            		$res = oci_execute($stid);

            		if($res)
            		{
            			oci_commit($conn);
            		}
            		else
            		{
            			$e = oci_error($stid);
            			echo "Error Add [".$e['message']."]";
            		}

            	}
				
            	// Update
            	if($_POST["hdnCmd"] == "Update")
            	{

            		$str = "UPDATE FAMILY_DOCTOR SET ";
            		$str .="DOCTOR_ID = '".$_POST["txtEditdoctor_id"]."' ";
            		$str .=",PATIENT_ID = '".$_POST["txtEditpatient_id"]."' ";

            		$str .="WHERE DOCTOR_ID = '".$_POST["hdnEditdoctor_id"]."' ";
            		$stid = oci_parse($conn, $str);     
            		$res = oci_execute($stid);
            		if($res)
            		{
            			oci_commit($conn);
            		}
            		else
            		{
            			$e = oci_error($stid);
            			echo "Error Update [".$e['message']."]";
            		}

            	}
            	
            	// Delete
            	if($_GET["Action"] == "Del")
            	{
            		$str = "DELETE FROM FAMILY_DOCTOR WHERE DOCTOR_ID = '".$_GET["keyID"]."' ";
            		$stid = oci_parse($conn, $str);
            		$res = oci_execute($stid);
            		if($res)
            		{
            			oci_commit($conn);
            		}
            		else
            		{
            			$e = oci_error($stid);
            			echo "Error Delete [".$e['message']."]";
            		}
            	} 	
                
				$sql="SELECT * FROM FAMILY_DOCTOR";
                $stid = oci_parse($conn, $sql);
                $res = oci_execute($stid);
                if (!$res) {
                    $err = oci_error($stid);
                    echo htmlentities($err['message']);
                }
                else {
                ?>    
               
					<h2>User Management Module</h2>
					
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
					
                	<form name="frmMain" method="post" action="<?=$_SERVER["PHP_SELF"];?>">
                	<input type="hidden" name="hdnCmd" value="">
                	<table  >
                						
					<tr>
					    <th width=25> <div align="center">Doctor ID </div></th>
					    <th> <div align="center">Patient ID </div></th>

					</tr>
  					<?php 
					while ($row=oci_fetch_array($stid, OCI_BOTH)) 
					{

						if( $row["DOCTOR_ID"] == $_GET["keyID"] and $_GET["Action"] == "Edit")
						{
						?>
						<tr>
							<td><div align="center">
								<input type="text" name="txtEditdoctor_id"  size="15" value="<?=$row['DOCTOR_ID'];?>">
								<input type="hidden" name="hdnEditdoctor_id" value="<?=$row["DOCTOR_ID"];?>">
							</div></td>
							<td><div align="center">
								<input type="text" name="txtEditpatient_id" size="15" value="<?=$row["PATIENT_ID"];?>">
							</div></td>

							<td colspan="2" align="right"><div align="center">
								<input name="btnAdd" type="button" id="btnUpdate" value="Update" OnClick="frmMain.hdnCmd.value='Update';frmMain.submit();">
								<input name="btnAdd" type="button" id="btnCancel" value="Cancel" OnClick="window.location='<?=$_SERVER["PHP_SELF"];?>';">
							</div></td>
						</tr>
						<?php
						}
						else
						{
  						?>
						  
						    <tr>
							    <td><div align="center"><?=$row["DOCTOR_ID"];?></div></td>
							    <td><div align="center"><?=$row["PATIENT_ID"];?></div></td>

							    <td align="center"><a href="<?=$_SERVER["PHP_SELF"];?>?Action=Edit&keyID=<?=$row["DOCTOR_ID"];?>"><img src="edit-16x16.png"></a></td>
								<td align="center"><a href="JavaScript:if(confirm('Confirm Delete?')==true){window.location='<?=$_SERVER["PHP_SELF"];?>?Action=Del&keyID=<?=$row["DOCTOR_ID"];?>';}"><img src="delete-16x16.png"></a></td>
					  		</tr>
						  
					   	<?php
						}
					}
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
