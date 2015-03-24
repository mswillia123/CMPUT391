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
            		$str = "insert into radiology_record (record_id, patient_id, doctor_id, radiologist_id, test_type, prescribing_date, test_date, diagnosis, description) ";
            		$str .="values ('".$_POST["txtAddrecord_id"]."' ";
            		$str .=",'".$_POST["txtAddpatient_id"]."' ";
            		$str .=",'".$_POST["txtAdddoctor_id"]."' ";
            		$str .=",'".$_POST["txtAddradiologist_id"]."' ";
            		$str .=",'".$_POST["txtAddtest_type"]."' ";
            		$str .=", TO_DATE('".$_POST["txtAddprescribing_date"]."', 'yyyy-mm-dd') ";
            		$str .=", TO_DATE('".$_POST["txtAddtest_date"]."', 'yyyy-mm-dd') ";
            		$str .=",'".$_POST["txtAdddiagnosis"]."' ";
            		$str .=",'".$_POST["txtAdddescription"]."' ";
            		$str .=")";
            		
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

            		$str = "update radiology_record set ";
            		$str .="record_id = '".$_POST["txtEditrecord_id"]."' ";
            		$str .=",patient_id = '".$_POST["txtEditpatient_id"]."' ";
            		$str .=",doctor_id = '".$_POST["txtEditdoctor_id"]."' ";
            		$str .=",radiologist_id = '".$_POST["txtEditradiologist_id"]."' ";
            		$str .=",test_type = '".$_POST["txtEdittest_type"]."' ";
            		$str .=",prescribing_date = TO_DATE('".$_POST["txtEditprescribing_date"]."', 'yyyy-mm-dd') ";
            		$str .=",test_date = TO_DATE('".$_POST["txtEdittest_date"]."', 'yyyy-mm-dd') ";
            		$str .=",diagnosis = '".$_POST["txtEditdiagnosis"]."' ";
            		$str .=",description = '".$_POST["txtEditdescription"]."' ";
            		$str .="where record_id = '".$_POST["hdnEditrecord_id"]."' ";
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
            	
            	// Upload images
/*<td align="center"><a href="<?=$_SERVER["PHP_SELF"];?>?Action=Upload&recordID=<?=$row["RECORD_ID"];?>"><img src="edit-16x16.png"></a></td>*/
            	if($_GET["Action"] == "Upload")
            	{
            		
            		/*
            		$str = "delete from radiology_record where record_id = '".$_GET["keyID"]."' ";
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
            		*/
            		if (!isset($_FILES['lob_upload'])) {
            			// If nothing uploaded, display the upload form
            			?>
            			<form action="<?php echo $_SERVER['PHP_SELF']; ?>"
            			method="POST" enctype="multipart/form-data">
            			<input type="submit" value="Upload">
            			</form>
            			<?php
            		}
            		else{
            		// Delete any existing BLOB
            		$query = "delete from pacs_images where record_id = '".$_GET["recordID"]."'";
            		$stmt = oci_parse ($conn, $query);
            		oci_bind_by_name($stmt, $_GET["recordID"], $myblobid);
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
            	
            	}
            	
            	// Delete
            	if($_GET["Action"] == "Del")
            	{
            		$str = "delete from radiology_record where record_id = '".$_GET["keyID"]."' ";
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
                
				$sql="select * from radiology_record order by record_id";
                $stid = oci_parse($conn, $sql);
                $res = oci_execute($stid);
                if (!$res) {
                    $err = oci_error($stid);
                    echo htmlentities($err['message']);
                }
                else {
                ?>    
               
					<h2>Radiology Record Module</h2>
					
					<div class="tabGroup">
				    <input type="radio" name="tabGroup1" id="rad1" class="tab1" onclick="document.location.href='manageUsers.php'" />
				    <label for="rad1">Radiology Record</label>
				 
				    <input type="radio" name="tabGroup1" id="rad2" class="tab2" checked="checked"/>
				    <label for="rad2">Upload Images</label>
				     

				     
				    <br/>
				 
				    <div class="tab1"></div>
				    <div class="tab2">
					
                	<form name="frmMain" method="post" action="<?=$_SERVER["PHP_SELF"];?>">
                	<input type="hidden" name="hdnCmd" value="">
                	<table  >                						
					<tr>
					    <th> <div align="center" size="25">Record ID</div></th>
					    <th> <div align="center">Patient ID </div></th>
					    <th width=25 > <div align="center">Doctor ID </div></th>
					    <th> <div align="center">Radiologist ID </div></th>
					    <th> <div align="center">Test Type </div></th>
					    <th> <div align="center">Prescribing Date </div></th>
					    <th> <div align="center">Test Date </div></th>
					    <th> <div align="center">Diagnosis </div></th>
					    <th> <div align="center">Description </div></th>
					</tr>
  					<?php 
				//while ($row=oci_fetch_array($stid, OCI_BOTH))
  				while ($row=oci_fetch_array($stid, OCI_BOTH))
				//while ($row=oci_fetch_array($stid, OCI_BOTH))
  					/*<input type="text" name="txtEditrecord_id"  size="1" value="<?=$row['RECORD_ID'];?>">
					*/
					{

						if( $row["RECORD_ID"] == $_GET["keyID"] and $_GET["Action"] == "Edit")
						{
						?>
						<tr>
							<td><div align="center"><?=$row["RECORD_ID"];?>	
								<input type="hidden" name="txtEditrecord_id"  size="1" value="<?=$row['RECORD_ID'];?>">
								<input type="hidden" name="hdnEditrecord_id" value="<?=$row["RECORD_ID"];?>">
							</div></td>
							<td><div align="center"><input type="text" name="txtEditpatient_id" size="1" value="<?=$row["PATIENT_ID"];?>"></div></td>
							<td><div align="center"><input type="text" name="txtEditdoctor_id" size="1" value="<?=$row["DOCTOR_ID"];?>"></div></td>
							<td><div align="center"><input type="text" name="txtEditradiologist_id" size="1" value="<?=$row["RADIOLOGIST_ID"];?>"></div></td>
							<td><div align="center"><input type="text" name="txtEdittest_type" size="15" value="<?=$row["TEST_TYPE"];?>"></div></td>
							<td><div align="center"><input type="date" name="txtEditprescribing_date" size="1" value="<?=$row["TEST_DATE"];?>"></div></td>
							<td><div align="center"><input type="date" name="txtEdittest_date" size="1" value="<?=$row["PRESCRIBING_DATE"];?>"></div></td>							
							<td><div align="center"><input type="text" name="txtEditdiagnosis" size="15" value="<?=$row["DIAGNOSIS"];?>"></div></td>	
							<td><div align="center"><input type="text" name="txtEditdescription" size="15" value="<?=$row["DESCRIPTION"];?>"></div></td>
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
							    <td><div align="center"><?=$row["RECORD_ID"];?></div></td>
							    <td><div align="center"><?=$row["PATIENT_ID"];?></div></td>
							    <td><div align="center"><?=$row["DOCTOR_ID"];?></div></td>
							    <td><div align="center"><?=$row["RADIOLOGIST_ID"];?></div></td>
							    <td><div align="center"><?=$row["TEST_TYPE"];?></div></td>
							    <td><div align="center"><?=$row["PRESCRIBING_DATE"];?></div></td>
							    <td><div align="center"><?=$row["TEST_DATE"];?></div></td>
							    <td><div align="center"><?=$row["DIAGNOSIS"];?></div></td>
							    <td><div align="center" ><?=$row["DESCRIPTION"];?></div></td>							    
							    <td align="center"><a href="<?=$_SERVER["PHP_SELF"];?>?Action=Edit&keyID=<?=$row["RECORD_ID"];?>"><img src="edit-16x16.png"></a></td>
							    <td align="center"><a href="uploadingModule3.php?recordID=<?=$row["RECORD_ID"];?>"><img src="edit-16x16.png"></a></td>
								<td align="center"><a href="JavaScript:if(confirm('Confirm Delete?')==true){window.location='<?=$_SERVER["PHP_SELF"];?>?Action=Del&keyID=<?=$row["RECORD_ID"];?>';}"><img src="delete-16x16.png"></a></td>
								
					  		</tr>
						  
					   	<?php
						}
					}
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
		}
		?>
		</div>


	<?php 
    }
	?>					
    </body>
</html>
